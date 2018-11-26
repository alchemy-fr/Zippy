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
use Alchemy\Zippy\Archive\Archive;
use Alchemy\Zippy\Archive\Member;
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Resource\Resource as ZippyResource;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessException;

abstract class AbstractTarAdapter extends AbstractBinaryAdapter
{
    /**
     * @inheritdoc
     */
    protected function doCreate($path, $files, $recursive)
    {
        return $this->doTarCreate($this->getLocalOptions(), $path, $files, $recursive);
    }

    /**
     * @inheritdoc
     */
    protected function doListMembers(ResourceInterface $resource)
    {
        return $this->doTarListMembers($this->getLocalOptions(), $resource);
    }

    /**
     * @inheritdoc
     */
    protected function doAdd(ResourceInterface $resource, $files, $recursive)
    {
        return $this->doTarAdd($this->getLocalOptions(), $resource, $files, $recursive);
    }

    /**
     * @inheritdoc
     */
    protected function doRemove(ResourceInterface $resource, $files)
    {
        return $this->doTarRemove($this->getLocalOptions(), $resource, $files);
    }

    /**
     * @inheritdoc
     */
    protected function doExtractMembers(ResourceInterface $resource, $members, $to, $overwrite = false)
    {
        return $this->doTarExtractMembers($this->getLocalOptions(), $resource, $members, $to, $overwrite);
    }

    /**
     * @inheritdoc
     */
    protected function doExtract(ResourceInterface $resource, $to)
    {
        return $this->doTarExtract($this->getLocalOptions(), $resource, $to);
    }

    /**
     * @inheritdoc
     */
    protected function doGetInflatorVersion()
    {
        $process = $this
            ->inflator
            ->create()
            ->add('--version');

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(), $process->getErrorOutput()
            ));
        }

        return $this->parser->parseInflatorVersion($process->getOutput() ?: '');
    }

    /**
     * @inheritdoc
     */
    protected function doGetDeflatorVersion()
    {
        return $this->getInflatorVersion();
    }

    protected function doTarCreate($options, $path, $files = null, $recursive = true)
    {
        $files = (array) $files;

        $process = $this
            ->inflator
            ->create();

        if (!$recursive) {
            $process->add('--no-recursion');
        }

        $process->add('-c');

        foreach ((array) $options as $option) {
            $process->add((string) $option);
        }

        if (0 === count($files)) {
            $nullFile = defined('PHP_WINDOWS_VERSION_BUILD') ? 'NUL' : '/dev/null';

            $process->add('-f');
            $process->add($path);
            $process->add('-T');
            $process->add($nullFile);

            $process->run();

        } else {

            $process->add(sprintf('--file=%s', $path));

            if (!$recursive) {
                $process->add('--no-recursion');
            }

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
        }

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return new Archive($this->createResource($path), $this, $this->manager);
    }

    protected function doTarListMembers($options, ResourceInterface $resource)
    {
        $process = $this
            ->inflator
            ->create();

        foreach ($this->getListMembersOptions() as $option) {
            $process->add($option);
        }

        $process
            ->add('--list')
            ->add('-v')
            ->add(sprintf('--file=%s', $resource->getResource()));

        foreach ((array) $options as $option) {
            $process->add((string) $option);
        }

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

    protected function doTarAdd($options, ResourceInterface $resource, $files, $recursive = true)
    {
        $files = (array) $files;

        $process = $this
            ->inflator
            ->create();

        if (!$recursive) {
            $process->add('--no-recursion');
        }

        $process
            ->add('--append')
            ->add(sprintf('--file=%s', $resource->getResource()));

        foreach ((array) $options as $option) {
            $process->add((string) $option);
        }

        // there will be an issue if the file starts with a dash
        // see --add-file=FILE
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

        return $files;
    }

    protected function doTarRemove($options, ResourceInterface $resource, $files)
    {
        $files = (array) $files;

        $process = $this
            ->inflator
            ->create();

        $process
            ->add('--delete')
            ->add(sprintf('--file=%s', $resource->getResource()));

        foreach ((array) $options as $option) {
            $process->add((string) $option);
        }

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

    protected function doTarExtract($options, ResourceInterface $resource, $to = null)
    {
        if (null !== $to && !is_dir($to)) {
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }

        $process = $this
            ->inflator
            ->create();

        $process
            ->add('--extract')
            ->add(sprintf('--file=%s', $resource->getResource()));

        foreach ($this->getExtractOptions() as $option) {
            $process
                ->add($option);
        }

        foreach ((array) $options as $option) {
            $process->add((string) $option);
        }

        if (null !== $to) {
            $process
                ->add('--directory')
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
     * @param array             $options
     * @param ResourceInterface $resource
     * @param array             $members
     * @param string            $to
     * @param bool              $overwrite
     *
     * @return array
     */
    protected function doTarExtractMembers($options, ResourceInterface $resource, $members, $to = null, $overwrite = false)
    {
        if (null !== $to && !is_dir($to)) {
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }

        $members = (array) $members;

        $process = $this
            ->inflator
            ->create();

        if ($overwrite == false) {
            $process->add('-k');
        }

        $process
            ->add('--extract')
            ->add(sprintf('--file=%s', $resource->getResource()));

        foreach ($this->getExtractMembersOptions() as $option) {
            $process
                ->add($option);
        }

        foreach ((array) $options as $option) {
            $process->add((string) $option);
        }

        if (null !== $to) {
            $process
                ->add('--directory')
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

    /**
     * Returns an array of option for the listMembers command
     *
     * @return array
     */
    abstract protected function getListMembersOptions();

    /**
     * Returns an array of option for the extract command
     *
     * @return array
     */
    abstract protected function getExtractOptions();

    /**
     * Returns an array of option for the extractMembers command
     *
     * @return array
     */
    abstract protected function getExtractMembersOptions();

    /**
     * Gets adapter specific additional options
     *
     * @return array
     */
    abstract protected function getLocalOptions();
}
