<?php

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Parser\ParserInterface;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface;

interface BinaryAdapterInterface
{
    /**
     * Gets the output parser
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
     * @return ProcessBuilderFactoryInterface
     */
    public function getProcessBuilder();

    /**
     * Sets the parser
     *
     * @param ParserInterface $parser The parser to use
     *
     * @return AbstractBinaryAdapter
     */
    public function setProcessBuilder(ProcessBuilderFactoryInterface $processBuilder);

    /**
     * Returns the binary version
     *
     * @return String
     */
    public function getVersion();

    /**
     * Gets the default adapter binary name
     *
     * @return String
     */
    public static function getDefaultBinaryName();
}
