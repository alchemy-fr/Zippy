<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Parser\ParserInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

abstract class AbstractBinaryAdapter extends AbstractAdapter implements BinaryAdapterInterface
{
    /**
     * The path to the binary file
     *
     * @var string
     */
    protected $binary;

    /**
     * The parser to use to parse command output
     *
     * @var ParserInterface
     */
    protected $parser;
    
    /**
     * Gets the default adapter binary name
     *
     * @return String
     */
    abstract public function getDefaultBinaryName();
    
    /**
     * @inheritdoc
     */
    public function useBinary($path)
    {
        if (!is_executable($path)) {
            throw new InvalidArgumentException(sprintf('%s is not executable', $path));
        }

        $this->binary = $path;

        return $path;
    }

    /**
     * Uses the default binary
     *
     * @return AbstractBinaryAdapter
     */
    public function useDefaultBinary()
    {
        $this->binary = $this->getDefaultBinaryName();

        return $this;
    }

    /**
     * Gets the used binary adapter
     *
     * @return String
     * 
     * @throws Exception In case the default binary file could not be found
     */
    public function getBinary()
    {
        if ($this->binary) {
            return $this->binary;
        }

        $finder = new ExecutableFinder();

        if (null === $this->binary = $finder->find($this->getDefaultBinaryName())) {
            throw new Exception(sprintf('Could not find `%s` binary', $this->getDefaultBinaryName()));
        }

        return $this->binary;
    }

    /**
     * Gets the binary process build
     *
     * @return ProcessBuilder
     */
    protected function getProcessBuilder()
    {
        return ProcessBuilder::create(array($this->getBinary()));
    }

    /**
     * @inheritdoc
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @inheritdoc
     */
    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;

        return $this;
    }
    
    protected function addBuilderFileArgument(array $files, $builder, $type) {
        array_walk($files, function($file) use ($builder, $type) {
            $file = $file instanceof \SplFileInfo ? $file->getRealpath() : $file;
            
            if (file_exists($file)) {
                if ($type === self::FILES && is_file($file)) {
                    $builder->add($file);
                } else if($type === self::DIRECTORIES && is_dir($file)) {
                    $builder->add($file);
                } else {
                    $builder->add($file);
                }
            }
        });
    }
}
