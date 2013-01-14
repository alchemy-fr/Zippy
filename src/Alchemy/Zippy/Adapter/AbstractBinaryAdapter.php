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
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\FileInterface;
use Alchemy\Zippy\Parser\ParserInterface;
use Alchemy\Zippy\Parser\ParserFactory;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

abstract class AbstractBinaryAdapter extends AbstractAdapter implements BinaryAdapterInterface
{
    /**
     * The parser to use to parse command output
     *
     * @var ParserInterface
     */
    protected $parser;

    /**
     * The deflator process builder factory to use to build binary command line
     *
     * @var ProcessBuilderFactoryInterface
     */
    protected $deflatorProcessBuilder;

    /**
     * The inflator process builder factory to use to build binary command line
     *
     * @var ProcessBuilderFactoryInterface
     */
    protected $inflatorProcessBuilder;

    /**
     * Constructor
     *
     * @param ParserInterface                     $parser                 An output parser
     * @param ProcessBuilderFactoryInterface      $inflatorProcessBuilder A process builder factory for the inflator binary
     * @param ProcessBuilderFactoryInterface|null $deflatorProcessBuilder A process builder factory for the deflator binary
     */
    public function __construct(ParserInterface $parser, ProcessBuilderFactoryInterface $inflatorProcessBuilder, ProcessBuilderFactoryInterface $deflatorProcessBuilder = null)
    {
        $this->parser = $parser;
        $this->deflatorProcessBuilder = $deflatorProcessBuilder;
        $this->inflatorProcessBuilder = $inflatorProcessBuilder;
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
    public function getDeflatorProcessBuilder()
    {
        return $this->deflatorProcessBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getInflatorProcessBuilder()
    {
        return $this->inflatorProcessBuilder;
    }

    /**
     * @inheritdoc
     */
    public function setDeflatorProcessBuilder(ProcessBuilderFactoryInterface $processBuilder)
    {
        $this->deflatorProcessBuilder = $processBuilder;

        return $this;
    }

    public function setInflatorProcessBuilder(ProcessBuilderFactoryInterface $processBuilder)
    {
        $this->inflatorProcessBuilder = $processBuilder;

        return $this;
    }

    /**
     * Returns a new instance of the invoked adapter
     *
     * @params String|null $inflatorBinaryName The inflator binary name to use
     * @params String|null $deflatorBinaryName The deflator binary name to use
     *
     * @return AbstractBinaryAdapter
     *
     * @throws RuntimeException In case object could not be instanciated
     */
    public static function newInstance($inflatorBinaryName = null, $deflatorBinaryName = null)
    {
        $finder = new ExecutableFinder();

        $inflatorBinaryName = $inflatorBinaryName ?: static::getDefaultInflatorBinaryName();

        $inflatorProcessBuilder = new ProcessBuilderFactory($finder->find($inflatorBinaryName));

        $deflatorProcessBuilder = null;

        if (static::getDefaultInflatorBinaryName() !== static::getDefaultDeflatorBinaryName()) {
            $deflatorBinaryName = $deflatorBinaryName ?: static::getDefaultDeflatorBinaryName();
            $deflatorProcessBuilder = new ProcessBuilderFactory($finder->find($deflatorBinaryName));
        }

        try {
            $outputParser = ParserFactory::create(static::getName());
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException(sprintf(
                'Failed to get a new instance of %s',
                get_called_class()), $e->getCode(), $e
            );
        }

        return new static($outputParser, $inflatorProcessBuilder, $deflatorProcessBuilder);
    }

    /**
     * Adds files to argument list
     *
     * @param Array          $files   An array of files
     * @param ProcessBuilder $builder A Builder instance
     *
     * @return Boolean
     */
    protected function addBuilderFileArgument(array $files, ProcessBuilder $builder)
    {
        $iterations = 0;

        array_walk($files, function($file) use ($builder, &$iterations) {
            $builder->add(
                $file instanceof \SplFileInfo ?
                $file->getRealpath() :
                ($file instanceof FileInterface ? $file->getLocation() : $file)
            );

            $iterations++;
        });

        return 0 !== $iterations;
    }
}
