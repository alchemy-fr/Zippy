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

use Alchemy\Zippy\Adapter\AdapterInterface;
use Alchemy\Zippy\Archive;
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Parser\ExplodeParser;

/**
 * GNUTarAdapter allows you to creates and extracts files from archives using GNU tar
 */
class GNUTarAdapter extends AbstractBinaryAdapter implements AdapterInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setParser(new ExplodeParser());
    }
    
    /**
     * @inheritdoc
     */
    public function create($path, $files)
    {
        $builder = $this->getProcessBuilder();
        $builder->add('-cf');
        $builder->add($path);
        
        foreach ($this->getFilesIterator($files) as $file) {
            $file = $file instanceof \SplFileInfo ? $file->getRealpath() : $file;
            
            if (is_file($file)) {
                $builder->add($file);
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
    public function getName()
    {
        return 'gnu_tar';
    }

    /**
     * @inheritdoc
     */
    public function isSupported()
    {
        $builder = $this->getProcessBuilder();
        $builder->add('-h');
            
        $process = $builder->getProcess();
        $process->run();
        
        return $process->isSuccessful();
    }

    /**
     * @inheritdoc
     */
    public function open($path)
    {
        return new Archive($path, $this);
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
        
        return $this->parser->parse($process->getOutput());
    }
    
    /**
     * @inheritdoc
     */
    public function addFile($path, $files)
    {
        $builder = $this->getProcessBuilder();
        $builder->add('-rf');
        $builder->add($path);
        
        $fileIterator = $this->getFilesIterator($files);
        
        foreach ($fileIterator as $file) {
            $file = $file instanceof \SplFileInfo ? $file->getRealpath() : $file;
            
            if (is_file($file)) {
                $builder->add($file);
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
        
        return $fileIterator;
    }
    
    /**
     * @inheritdoc
     */
    public function getDefaultBinaryName()
    {
        return 'tar';
    }
}
