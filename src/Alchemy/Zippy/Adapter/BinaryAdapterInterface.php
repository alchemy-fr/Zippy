<?php

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Parser\ParserInterface;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderInterface;

interface BinaryAdapterInterface
{
    const FILES = 1;
    const DIRECTORIES = 2;
    const FILES_AND_DIRECTORIES = 4;
    
    /**
     * Sets a custom binary
     *
     * @param String $path The path to the binary
     * @param array  $extraDirs Additional dirs to check into
     *
     * @return BinaryAdapterInterface
     *
     * @throws InvalidArgumentException In case the binary is not executable
     */
    public function useBinary($path, array $extraDirs = array());
    
    /**
     * Returns the binary path
     *
     * @return string
     */
    public function getBinary();
    
    /**
     * Returns the parser
     *
     * @return ParserInterface
     */
    public function getParser();
    
    /**
     * Sets the parser
     *
     * @param ParserInterface $parser The parser to use
     *
     * @return AbstractBinaryAdapter
     */
    public function setParser(ParserInterface $parser);
    
    /**
     * Returns the parser
     *
     * @return ProcessBuilderInterface
     */
    public function getProcessBuilder();
    
    /**
     * Sets the parser
     *
     * @param ParserInterface $parser The parser to use
     *
     * @return AbstractBinaryAdapter
     */
    public function setProcessBuilder(ProcessBuilderInterface $processBuilder);

    /**
     * Returns the binary version
     *
     * @return string
     */
    public function getVersion();
}
