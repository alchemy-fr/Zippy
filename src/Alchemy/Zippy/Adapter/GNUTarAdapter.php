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

use Alchemy\Zippy\Archive\Archive;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\RuntimeException;

/**
 * GNUTarAdapter allows you to create and extract files from archives using GNU tar
 *
 * @see http://www.gnu.org/software/tar/manual/tar.html
 */
class GNUTarAdapter extends AbstractBinaryAdapter
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

        if (!$recursive) {
           $builder->add('--no-recursion');
        }

        $builder->add('-cf');

        if (0 === count($files)) {
            $nullFile = defined('PHP_WINDOWS_VERSION_BUILD') ? 'NUL' : '/dev/null';

            $builder->add('-');
            $builder->add(sprintf('--files-from %s', $nullFile));
            $builder->add(sprintf('> %s', $path));
        } else {

            $builder->add($path);

            if (!$recursive) {
               $builder->add('--no-recursion');
            }

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
        $process = $this
            ->inflator
            ->create()
            ->add('--version')
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            return false;
        }

        $lines = explode("\n", $process->getOutput(), 2);

        return false !== stripos($lines[0], '(gnu tar)');
    }

    /**
     * @inheritdoc
     */
    public function listMembers($path)
    {
        $process = $this
            ->inflator
            ->create()
            ->add('--utc -tf')
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

        if (!$recursive) {
           $builder->add('--no-recursion');
        }

        $builder
            ->add('-rf')
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
    public function getInflatorVersion()
    {
        $process = $this
            ->inflator
            ->create()
            ->add('--version')
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
            ->add('--delete')
            ->add(sprintf('--file=%s', $path));

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
    public function extract($path, $to = null)
    {
        if (null !== $to && !is_dir($to)) {
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }

        $archiveFile = new \SplFileInfo($path);

        $builder = $this
            ->inflator
            ->create();

        $builder
            ->add('-xf')
            ->add($path);

        if (null !== $to) {
            $builder
                ->add('-C')
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
            ->inflator
            ->create();

        $builder
            ->add('--extract')
            ->add(sprintf('--file=%s', $path));

        if (null !== $to) {
            $builder
                ->add('-C')
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

    /**
     * @inheritdoc
     */
    public function getDeflatorVersion()
    {
        return $this->getInflatorVersion();
    }

    /**
     * @inheritdoc
     */
    public static function getName()
    {
        return 'gnu-tar';
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultDeflatorBinaryName()
    {
        return 'tar';
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultInflatorBinaryName()
    {
        return 'tar';
    }
}
