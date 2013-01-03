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
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory;

/**
 * GNUTarAdapter allows you to creates and extracts files from archives using GNU tar
 * @see http://www.gnu.org/software/tar/manual/tar.html
 */
class GNUTarAdapter extends AbstractBinaryAdapter
{
    /**
     * Constructor.
     */
    public function __construct(ParserInterface $parser, ProcessBuilderFactory $processBuilderFactory)
    {
        $this
            ->setParser($parser)
            ->setProcessBuilder($processBuilderFactory->create($this));
    }
    
    /**
     * @inheritdoc
     */
    public function create($path, $files = null, $recursive = true)
    {
        if (null === $files) {
            throw new NotSupportedException('Gnu tar does not allow to create empty archive');
        }
        
        $files = (array) $files;
        
        $process = $this->processBuilder->getCreateArchiveProcess($path, $files, $recursive);

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
        $process = $this->processBuilder->getHelpProcess();

        $process->run();
        
        return $process->isSuccessful();
    }

    /**
     * @inheritdoc
     */
    public function listMembers($path)
    {
        $process = $this->processBuilder->getListMembersProcess($path);

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
        $files = (array) $files;
        
        $process = $this->processBuilder->getAddFileProcess($path, $files);
        
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
        $process = $this->processBuilder->getVersionProcess();
        
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
