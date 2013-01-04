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
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderInterface;

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
     * The processBuilder use to build binary command line
     *
     * @var ProcessBuilderInterface
     */
    protected $processBuilder;
    
    /**
     * Gets the default adapter binary name
     *
     * @return String
     */
    abstract public function getDefaultBinaryName();
    
    /**
     * @inheritdoc
     */
    public function useBinary($path, array $extraDirs = array())
    {
        if (!is_executable($path)) {
            throw new InvalidArgumentException(sprintf('%s is not executable', $path));
        }

        $this->getProcessBuilder()->setBinary($path, $extraDirs);

        return $path;
    }

    /**
     * Uses the default binary
     *
     * @return AbstractBinaryAdapter
     */
    public function useDefaultBinary()
    {
        $this->getProcessBuilder()->setBinary($this->getDefaultBinaryName());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBinary()
    {
        return $this->getProcessBuilder()->getBinary();
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
    
    /**
     * @inheritdoc
     */
    public function getProcessBuilder()
    {
        return $this->processBuilder;
    }

    /**
     * @inheritdoc
     */
    public function setProcessBuilder(ProcessBuilderInterface $processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }


}
