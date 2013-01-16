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
     * The process builder factory to use to build binary command line
     *
     * @var ProcessBuilderFactoryInterface
     */
    protected $processBuilder;

    /**
     * Constructor
     *
     * @param ParserInterface                $parser         An output parser
     * @param ProcessBuilderFactoryInterface $processBuilder A process builder factory
     */
    public function __construct(ParserInterface $parser, ProcessBuilderFactoryInterface $processBuilder)
    {
        $this->parser = $parser;
        $this->processBuilder = $processBuilder;
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
    public function setProcessBuilder(ProcessBuilderFactoryInterface $processBuilder)
    {
        $this->processBuilder = $processBuilder;

        return $this;
    }

    /**
     * Returns a new instance of the invoked adapter
     *
     * @return AbstractBinaryAdapter
     *
     * @throws RuntimeException In case object could not be instanciated
     */
    public static function newInstance()
    {
        $finder = new ExecutableFinder();

        $processBuilder = new ProcessBuilderFactory($finder->find(static::getDefaultBinaryName()));

        try {
            $outputParser = ParserFactory::create(static::getName());
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException(sprintf(
                'Failed to get a new instance of %s',
                get_called_class()), $e->getCode(), $e
            );
        }

        return new static($outputParser, $processBuilder);
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
