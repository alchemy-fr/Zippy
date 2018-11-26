<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Adapter\Resource\ResourceInterface;
use Alchemy\Zippy\Adapter\VersionProbe\ZipVersionProbe;
use Alchemy\Zippy\Archive\Archive;
use Alchemy\Zippy\Archive\Member;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\NotSupportedException;
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Parser\ParserInterface;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface;
use Alchemy\Zippy\Resource\Resource as ZippyResource;
use Alchemy\Zippy\Resource\ResourceManager;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessException;

/**
 * ZipAdapter allows you to create and extract files from archives using Zip
 *
 * @see http://www.gnu.org/software/tar/manual/tar.html
 */
class ZipAdapter extends AbstractBinaryAdapter
{
    public function __construct(
        ParserInterface $parser,
        ResourceManager $manager,
        ProcessBuilderFactoryInterface $inflator,
        ProcessBuilderFactoryInterface $deflator
    ) {
        parent::__construct($parser, $manager, $inflator, $deflator);

        $this->probe = new ZipVersionProbe($inflator, $deflator);
    }

    /**
     * @inheritdoc
     */
    protected function doCreate($path, $files, $recursive)
    {
        $files = (array) $files;

        $process = $this
            ->inflator
            ->create();

        if (0 === count($files)) {
            throw new NotSupportedException('Can not create empty zip archive');
        }

        if ($recursive) {
            $process->add('-r');
        }

        $process->add($path);

        $collection = $this->manager->handle(getcwd(), $files);
        $process->setWorkingDirectory($collection->getContext());

        $collection->forAll(function($i, ZippyResource $resource) use ($process) {
            return $process->add($resource->getTarget());
        });

        try {
            $process->run();
        } catch (ProcessException $e) {
            $this->manager->cleanup($collection);
            throw $e;
        }

        $this->manager->cleanup($collection);

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return new Archive($this->createResource($path), $this, $this->manager);
    }

    /**
     * @inheritdoc
     */
    protected function doListMembers(ResourceInterface $resource)
    {
        $process = $this
            ->deflator
            ->create()
            ->add('-l')
            ->add($resource->getResource());

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        $members = array();

        foreach ($this->parser->parseFileListing($process->getOutput() ?: '') as $member) {
            $members[] = new Member(
                $resource,
                $this,
                $member['location'],
                $member['size'],
                $member['mtime'],
                $member['is_dir']
            );
        }

        return $members;
    }

    /**
     * @inheritdoc
     */
    protected function doAdd(ResourceInterface $resource, $files, $recursive)
    {
        $files = (array) $files;

        $process = $this
            ->inflator
            ->create();

        if ($recursive) {
            $process->add('-r');
        }

        $process
            ->add('-u')
            ->add($resource->getResource());

        $collection = $this->manager->handle(getcwd(), $files);

        $process->setWorkingDirectory($collection->getContext());

        $collection->forAll(function($i, ZippyResource $resource) use ($process) {
            return $process->add($resource->getTarget());
        });

        try {
            $process->run();
        } catch (ProcessException $e) {
            $this->manager->cleanup($collection);
            throw $e;
        }

        $this->manager->cleanup($collection);

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }
    }

    /**
     * @inheritdoc
     */
    protected function doGetDeflatorVersion()
    {
        $process = $this
            ->deflator
            ->create()
            ->add('-h');

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return $this->parser->parseDeflatorVersion($process->getOutput() ?: '');
    }

    /**
     * @inheritdoc
     */
    protected function doGetInflatorVersion()
    {
        $process = $this
            ->inflator
            ->create()
            ->add('-h');

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return $this->parser->parseInflatorVersion($process->getOutput() ?: '');
    }

    /**
     * @inheritdoc
     */
    protected function doRemove(ResourceInterface $resource, $files)
    {
        $files = (array) $files;

        $process = $this
            ->inflator
            ->create();

        $process
            ->add('-d')
            ->add($resource->getResource());

        if (!$this->addBuilderFileArgument($files, $process)) {
            throw new InvalidArgumentException('Invalid files');
        }

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return $files;
    }

    /**
     * @inheritdoc
     */
    public static function getName()
    {
        return 'zip';
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultDeflatorBinaryName()
    {
        return array('unzip');
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultInflatorBinaryName()
    {
        return array('zip');
    }

    /**
     * @inheritdoc
     */
    protected function doExtract(ResourceInterface $resource, $to)
    {
        if (null !== $to && !is_dir($to)) {
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }

        $process = $this
            ->deflator
            ->create();

        $process
            ->add('-o')
            ->add($resource->getResource());

        if (null !== $to) {
            $process
                ->add('-d')
                ->add($to);
        }

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return new \SplFileInfo($to ?: $resource->getResource());
    }

    /**
     * @inheritdoc
     */
    protected function doExtractMembers(ResourceInterface $resource, $members, $to, $overwrite = false)
    {
        if (null !== $to && !is_dir($to)) {
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }

        $members = (array) $members;

        $process = $this
            ->deflator
            ->create();

        if ((bool) $overwrite) {
            $process->add('-o');
        }

        $process
            ->add($resource->getResource());

        if (null !== $to) {
            $process
                ->add('-d')
                ->add($to);
        }

        if (!$this->addBuilderFileArgument($members, $process)) {
            throw new InvalidArgumentException('Invalid files');
        }

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return $members;
    }
}
