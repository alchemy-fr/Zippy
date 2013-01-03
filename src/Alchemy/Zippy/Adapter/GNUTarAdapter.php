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
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Exception\NotSupportedException;
use Alchemy\Zippy\Parser\ParserInterface;

/**
 * GNUTarAdapter allows you to creates and extracts files from archives using GNU tar
 * @see http://www.gnu.org/software/tar/manual/tar.html
 */
class GNUTarAdapter extends AbstractBinaryAdapter
{
    /**
     * Constructor.
     */
    public function __construct(ParserInterface $parser)
    {
        $this->setParser($parser);
    }
    
    /**
     * @inheritdoc
     */
    public function create($path, $files = null, $recursive = true)
    {
        if (null === $files) {
            throw new NotSupportedException('Gnu tar does not allow to create empty archive');
        }
        
        $builder = $this->getProcessBuilder();
        
        if (!$recursive) {
           $builder->add('--no-recursion');
        }
        
        $builder->add('-cf');
        $builder->add($path);
        
        $this->addBuilderFileArgument(
            (array) $files,
            $builder,
            self::FILES_AND_DIRECTORIES
        );
        
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
    public function getName()
    {
        return 'gnu-tar';
    }

    /**
     * @inheritdoc
     */
    public function isSupported()
    {
        return 0 === $this->getProcessBuilder()
            ->add('-h')
            ->getProcess()
            ->run();
    }

    /**
     * @inheritdoc
     */
    public function listMembers($path)
    {
        $builder = $this->getProcessBuilder();
        $builder->add('-tf');
        $builder->add($path);

        $process = $builder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf(
                'Unable to execute the following command %s {output: %s}',
                $process->getCommandLine(),
                $process->getErrorOutput()
            ));
        }

        return $this->parser->parseFileListing($process->getOutput());
    }

    /**
     * @inheritdoc
     */
    public function addFile($path, $files)
    {
        $builder = $this->getProcessBuilder();
        $builder->add('-rf');
        $builder->add($path);

        $files = (array) $files;

        $this->addBuilderFileArgument(
            $files,
            $builder,
            self::FILES
        );

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
    
    public function getVersion()
    {
        $process = $this->getProcessBuilder()
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
        
        return $this->parser->parseVersion($process->getOutput());
    }

    /**
     * @inheritdoc
     */
    public function getDefaultBinaryName()
    {
        return 'tar';
    }
}
