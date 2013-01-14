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

use Alchemy\Zippy\Archive;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\NotSupportedException;
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
        if (null === $files) {
            throw new NotSupportedException('Gnu tar does not allow to create an empty archive');
        }

        $files = (array) $files;

        $builder = $this
            ->processBuilder
            ->create();

        if (!$recursive) {
           $builder->add('--no-recursion');
        }

        $builder->add('-cf');
        $builder->add($path);

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

        return new Archive($path, $this);
    }

    /**
     * @inheritdoc
     */
    public function isSupported()
    {
        $process = $this
            ->processBuilder
            ->create()
            ->add('-h')
            ->getProcess();

        $process->run();

        return $process->isSuccessful();
    }

    /**
     * @inheritdoc
     */
    public function listMembers($path)
    {
        $process = $this
            ->processBuilder
            ->create()
            ->add('-tf')
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

        return $this->parser->parseFileListing($process->getOutput() ?: '');
    }

    /**
     * @inheritdoc
     */
    public function add($path, $files, $recursive = true)
    {
        $files = (array) $files;

        $builder = $this
            ->processBuilder
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
    public function getVersion()
    {
        $process = $this
            ->processBuilder
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

        return $this->parser->parseVersion($process->getOutput() ?: '');
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultBinaryName()
    {
        return 'tar';
    }

    /**
     * @inheritdoc
     */
    public static function getName()
    {
        return 'gnu-tar';
    }
}
