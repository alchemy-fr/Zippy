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
use Alchemy\Zippy\Parser\ParserFactory;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderInterface;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory;

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
        
        return $this;
    }

    /**
     * Returns a new instance of the invoked adapter
     *
     * @return AbstractBinaryAdapter
     *
     * @throws InvalidArgumentException In case no process builder or output parser were found
     */
    public static function newInstance()
    {
        $adapterName = static::getName();

        $processBuilder = ProcessBuilderFactory::create(
            $adapterName,
            static::getDefaultBinaryName()
        );

        $outputParser = ParserFactory::create($adapterName);

        return new static($outputParser, $processBuilder);
    }
}
