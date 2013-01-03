<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\ProcessBuilder;

use Alchemy\Zippy\Adapter\BinaryAdapterInterface;
use Alchemy\Zippy\Exception\InvalidArgumentException;

/**
 * This class is responsable of build Symfony Process instance
 * with the proper GNUTar command line for the requested action
 */
class GNUTarProcessBuilder extends AbstractProcessBuilder
{
    /**
     * @inheritdoc
     */
    public function getAddFileProcess($path, array $files)
    {
        $builder = $this->getProcessBuilder()
            ->add('-rf')
            ->add($path);

        if (!$this->addBuilderFileArgument(
            $files,
            $builder,
            BinaryAdapterInterface::FILES
        )) {
            throw new InvalidArgumentException('Invalid files');
        }

        return $builder->getProcess();
    }
    
    /**
     * @inheritdoc
     */
    public function getListMembersProcess($path)
    {
       return $this->getProcessBuilder()
            ->add('-tf')
            ->add($path)
            ->getProcess();
    }
    
    /**
     * @inheritdoc
     */
    public function getCreateArchiveProcess($path, array $files = null, $recursive = true)
    {
        $builder = $this->getProcessBuilder();
        
        if (!$recursive) {
           $builder->add('--no-recursion');
        }
        
        $builder->add('-cf');
        $builder->add($path);
        
        if (!$this->addBuilderFileArgument(
            $files,
            $builder,
            BinaryAdapterInterface::FILES_AND_DIRECTORIES
        )) {
            throw new InvalidArgumentException('Invalid files');
        }
        
        return $builder->getProcess();
    }
    
    /**
     * @inheritdoc
     */
    public function getVersionProcess()
    {
        return $this->getProcessBuilder()
            ->add('--version')
            ->getProcess();
    }
    
    /**
     * @inheritdoc
     */
    public function getHelpProcess()
    {
        return $this->getProcessBuilder()
            ->add('-h')
            ->getProcess();
    }

}
