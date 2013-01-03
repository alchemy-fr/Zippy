<?php

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Parser\ParserInterface;

interface BinaryAdapterInterface
{
    const FILES = 1;
    const DIRECTORIES = 2;
    const FILES_AND_DIRECTORIES = 4;
    
    /**
     * Sets a custom binary
     *
     * @param String $path The path to the binary
     *
     * @return BinaryAdapterInterface
     *
     * @throws InvalidArgumentException In case the binary is not executable
     */
    public function useBinary($path);
    
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
     * Returns the binary version
     *
     * @return string
     */
    public function getVersion();
}
