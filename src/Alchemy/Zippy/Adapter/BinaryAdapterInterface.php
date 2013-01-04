<?php

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Parser\ParserInterface;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderInterface;

interface BinaryAdapterInterface
{
    const FILES = 1;
    const DIRECTORIES = 2;
    const FILES_AND_DIRECTORIES = 4;

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
    
    /**
     * Gets the default adapter binary name
     *
     * @return String
     */
    public static function getDefaultBinaryName();
}
