<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Adapter\GNUTar;

use Alchemy\Zippy\Adapter\AbstractBinaryAdapter;
use Alchemy\Zippy\Archive\Archive;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\RuntimeException;

/**
 * GNUTarAdapter allows you to create and extract files from archives using GNU tar
 *
 * @see http://www.gnu.org/software/tar/manual/tar.html
 */
abstract class AbstractGNUTarAdapter extends AbstractBinaryAdapter
{
    /**
     * @inheritdoc
     */
    public function create($path, $files = null, $recursive = true)
    {
        return $this->doCreate($this->getLocalOptions(), $path, $files, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function listMembers($path)
    {
        return $this->doListMembers($this->getLocalOptions(), $path);
    }

    /**
     * @inheritdoc
     */
    public function add($path, $files, $recursive = true)
    {
        return $this->doAdd($this->getLocalOptions(), $path, $files, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function remove($path, $files)
    {
        return $this->doRemove($this->getLocalOptions(), $path, $files);
    }

    /**
     * @inheritdoc
     */
    public function extractMembers($path, $members, $to = null)
    {
        return $this->doExtractMembers($this->getLocalOptions(), $path, $members, $to);
    }

    /**
     * @inheritdoc
     */
    public function extract($path, $to = null)
    {
        return $this->doExtract($this->getLocalOptions(), $path, $to);
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
                    'Unable to execute the following command %s {output: %s}', $process->getCommandLine(), $process->getErrorOutput()
            ));
        }

        return $this->parser->parseInflatorVersion($process->getOutput() ? : '');
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

    protected function doCreate($options, $path, $files = null, $recursive = true)
    {
        $files = (array) $files;

        $builder = $this
            ->inflator
            ->create();

        if (!$recursive) {
            $builder->add('--no-recursion');
        }

        $builder->add('-cf');

        foreach((array) $options as $option) {
            $builder->add((string) $option);
        }

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
                    'Unable to execute the following command %s {output: %s}', $process->getCommandLine(), $process->getErrorOutput()
            ));
        }

        return new Archive($path, $this);
    }

    protected function doListMembers($options, $path)
    {
        $builder = $this
            ->inflator
            ->create()
            ->add('--utc')
            ->add('-t')
            ->add(sprintf('--file=%s', $path));

        foreach((array) $options as $option) {
            $builder->add((string) $option);
        }

        $process = $builder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                    'Unable to execute the following command %s {output: %s}', $process->getCommandLine(), $process->getErrorOutput()
            ));
        }

        $members = array();

        foreach ($this->parser->parseFileListing($process->getOutput() ? : '') as $member) {
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

    protected function doAdd($options, $path, $files, $recursive = true)
    {
        $files = (array) $files;

        $builder = $this
            ->inflator
            ->create();

        if (!$recursive) {
            $builder->add('--no-recursion');
        }

        $builder
            ->add('--delete')
            ->add('--append')
            ->add(sprintf('--file=%s', $path));

        foreach((array) $options as $option) {
            $builder->add((string) $option);
        }

//        --add-file=FILE        add given FILE to the archive (useful if its name
//                             starts with a dash)
        if (!$this->addBuilderFileArgument($files, $builder)) {
            throw new InvalidArgumentException('Invalid files');
        }

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                    'Unable to execute the following command %s {output: %s}', $process->getCommandLine(), $process->getErrorOutput()
            ));
        }

        return $files;
    }

    protected function doRemove($options, $path, $files)
    {
        $files = (array) $files;

        $builder = $this
            ->inflator
            ->create();

        $builder
            ->add('--delete')
            ->add(sprintf('--file=%s', $path));

        foreach((array) $options as $option) {
            $builder->add((string) $option);
        }

        if (!$this->addBuilderFileArgument($files, $builder)) {
            throw new InvalidArgumentException('Invalid files');
        }

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                    'Unable to execute the following command %s {output: %s}', $process->getCommandLine(), $process->getErrorOutput()
            ));
        }

        return $files;
    }

    protected function doExtract($options, $path, $to = null)
    {
        if (null !== $to && !is_dir($to)) {
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }

        $archiveFile = new \SplFileInfo($path);

        $builder = $this
            ->inflator
            ->create();

        $builder
            ->add('--extract')
            ->add(sprintf('--file=%s', $path));

        foreach((array) $options as $option) {
            $builder->add((string) $option);
        }

        if (null !== $to) {
            $builder
                ->add('--directory')
                ->add($to);
        }

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                    'Unable to execute the following command %s {output: %s}', $process->getCommandLine(), $process->getErrorOutput()
            ));
        }

        return null === $to ? $archiveFile->getPathInfo() : new \SplFileInfo($to);
    }

    protected function doExtractMembers($options, $path, $members, $to = null)
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

        foreach((array) $options as $option) {
            $builder->add((string) $option);
        }

        if (null !== $to) {
            $builder
                ->add('--directory')
                ->add($to);
        }

        if (!$this->addBuilderFileArgument($members, $builder)) {
            throw new InvalidArgumentException('Invalid files');
        }

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                    'Unable to execute the following command %s {output: %s}', $process->getCommandLine(), $process->getErrorOutput()
            ));
        }

        return $members;
    }

    abstract protected function getLocalOptions();
}
