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
     * Returns the inflator process builder
     *
     * @return ProcessBuilderFactoryInterface
     */
    public function getInflator();

    /**
     * Sets the inflator process builder
     *
     * @param ParserInterface $parser The parser to use
     *
     * @return AbstractBinaryAdapter
     */
    public function setInflator(ProcessBuilderFactoryInterface $processBuilder);

    /**
     * Returns the deflator process builder
     *
     * @return ProcessBuilderFactoryInterface
     */
    public function getDeflator();

    /**
     * Sets the deflator process builder
     *
     * @param ParserInterface $parser The parser to use
     *
     * @return AbstractBinaryAdapter
     */
    public function setDeflator(ProcessBuilderFactoryInterface $processBuilder);

    /**
     * Returns the inflator binary version
     *
     * @return String
     */
    public function getInflatorVersion();

    /**
     * Returns the deflator binary version
     *
     * @return String
     */
    public function getDeflatorVersion();

    /**
     * Gets the inflator adapter binary name
     *
     * @return String
     */
    public static function getDefaultInflatorBinaryName();

    /**
     * Gets the deflator adapter binary name
     *
     * @return String
     */
    public static function getDefaultDeflatorBinaryName();
}
