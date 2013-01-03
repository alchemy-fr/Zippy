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

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Adapter\BinaryAdapterInterface;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

abstract class AbstractProcessBuilder implements ProcessBuilderInterface
{
    /**
     * The binary path
     * 
     * @var string 
     */
    protected $binary;
    
    /**
     *
     * @var type 
     */
    protected $binaryFinder;
    
    /**
     * Constructor
     */
    public function __construct($binary, ExecutableFinder $finder)
    {
        $this->binaryFinder = $finder;
        $this->setBinary($binary);
    }
    
    /**
     * @inheritdoc
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * @inheritdoc
     */
    public function setBinary($binary, array $extraDirs = array())
    {
        $this->binary = $this->binaryFinder->find($binary, null, $extraDirs);
        
        return $this;
    }
    
    /**
     * Returns a new instance of Symfony ProcessBuilder
     * 
     * @return ProcessBuilder
     * 
     * @throws InvalidArgumentException
     */
    protected function getProcessBuilder()
    {
        if (null === $this->binary) {
            throw new InvalidArgumentException(sprintf('Could not find `%s` binary', $this->binary));
        }
        
        return ProcessBuilder::create(array($this->binary));
    }
    
    /**
     * Adds files to argument list
     * 
     * @param array             $files      An array of files
     * @param ProcessBuilder    $builder    A Builder instance
     * @param Integer           $type       Authorized type of files
     * 
     * @return AbstractProcess
     */
    protected function addBuilderFileArgument(array $files, ProcessBuilder $builder, $type) {
        $iterations = 0;
        
        array_walk($files, function($file) use ($builder, $type, &$iterations) {
            $file = $file instanceof \SplFileInfo ? $file->getRealpath() : $file;
            
            if (file_exists($file)) {
                if ($type === BinaryAdapterInterface::FILES && is_file($file)) {
                    $builder->add($file);
                } else if($type === BinaryAdapterInterface::DIRECTORIES && is_dir($file)) {
                    $builder->add($file);
                } else {
                    $builder->add($file);
                }
                
                $iterations++;
            }
        });
        
        return 0 !== $iterations;
    }
}
