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

use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Exception\NotSupportedException;
use Alchemy\Zippy\Archive\Archive;

/**
 * ZipAdapter allows you to create and extract files from archives using Zip
 *
 * @see http://www.gnu.org/software/tar/manual/tar.html
 */
class ZipAdapter extends AbstractBinaryAdapter
{
    /**
     * @inheritdoc
     */
    public function create($path, $files = null, $recursive = true)
    {
        $files = (array) $files;

        $builder = $this
            ->inflator
            ->create();

        if (0 === count($files)) {
           throw new NotSupportedException('Can not create empty zip archive');
        } else {

            if ($recursive) {
                $builder->add('-R');
            }

            $builder->add($path);

            if (!$this->addBuilderFileArgument($files, $builder)) {
                throw new InvalidArgumentException('Invalid files');
            }
        }

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return new Archive($path, $this);
    }

    /**
     * @inheritdoc
     */
    public function isSupported()
    {
        $processDeflate = $this
            ->deflator
            ->create()
            ->add('-h')
            ->getProcess();

        $processDeflate->run();

        $processInflate = $this
            ->inflator
            ->create()
            ->add('-h')
            ->getProcess();

        $processInflate->run();

        return $processInflate->isSuccessful() && $processDeflate->isSuccessful();
    }

    /**
     * @inheritdoc
     */
    public function listMembers($path)
    {
        $process = $this
            ->deflator
            ->create()
            ->add('-l')
            ->add($path)
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        $members = array();

        foreach($this->parser->parseFileListing($process->getOutput() ?: '') as $member) {
               $members[] = new Member(
                $path,
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
    public function add($path, $files, $recursive = true)
    {
        $files = (array) $files;

        $builder = $this
            ->inflator
            ->create();

        if ($recursive) {
            $builder->add('-R');
        }

        $builder
            ->add('-u')
            ->add($path);

        if (!$this->addBuilderFileArgument($files, $builder)) {
            throw new InvalidArgumentException('Invalid files');
        }

        $process = $builder->getProcess();

        $process->run();

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
    public function getDeflatorVersion()
    {
        $process = $this
            ->deflator
            ->create()
            ->add('-h')
            ->getProcess();

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
    public function getInflatorVersion()
    {
        $process = $this
            ->inflator
            ->create()
            ->add('-h')
            ->getProcess();

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
    public function remove($path, $files)
    {
        $files = (array) $files;

        $builder = $this
            ->inflator
            ->create();

        $builder
            ->add('-d')
            ->add($path);

        if (!$this->addBuilderFileArgument($files, $builder)) {
            throw new InvalidArgumentException('Invalid files');
        }

        $process = $builder->getProcess();

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
        return 'unzip';
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultInflatorBinaryName()
    {
        return 'zip';
    }

    /**
     * @inheritdoc
     */
    public function extract($path, $to = null)
    {
        if (null !== $to && !is_dir($to)) {
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }

        $archiveFile = new \SplFileInfo($path);

        $builder = $this
            ->deflator
            ->create();

        $builder
            ->add($path);

        if (null !== $to) {
            $builder
                ->add('-d')
                ->add($to);
        }

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return null === $to ? $archiveFile->getPathInfo() : new \SplFileInfo($to);
    }

    /**
     * @inheritdoc
     */
    public function extractMembers($path, $members, $to = null)
    {
        if (null !== $to && !is_dir($to)) {
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }

        $members = (array) $members;

        $builder = $this
            ->deflator
            ->create();

        $builder
            ->add($path);

        if (null !== $to) {
            $builder
                ->add('-d')
                ->add($to);
        }

        if (!$this->addBuilderFileArgument($members, $builder)) {
            throw new InvalidArgumentException('Invalid files');
        }

        $process = $builder->getProcess();

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
