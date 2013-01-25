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
use Alchemy\Zippy\Exception\IOException;
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Archive\MemberInterface;
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
    protected $deflator;

    /**
     * The inflator process builder factory to use to build binary command line
     *
     * @var ProcessBuilderFactoryInterface
     */
    protected $inflator;

    /**
     * Constructor
     *
     * @param ParserInterface                     $parser   An output parser
     * @param ProcessBuilderFactoryInterface      $inflator A process builder factory for the inflator binary
     * @param ProcessBuilderFactoryInterface|null $deflator A process builder factory for the deflator binary
     */
    public function __construct(ParserInterface $parser, ProcessBuilderFactoryInterface $inflator, ProcessBuilderFactoryInterface $deflator = null)
    {
        $this->parser = $parser;
        $this->deflator = $deflator;
        $this->inflator = $inflator;
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
    public function getDeflator()
    {
        return $this->deflator;
    }

    /**
     * @inheritdoc
     */
    public function getInflator()
    {
        return $this->inflator;
    }

    /**
     * @inheritdoc
     */
    public function setDeflator(ProcessBuilderFactoryInterface $processBuilder)
    {
        $this->deflator = $processBuilder;

        return $this;
    }

    public function setInflator(ProcessBuilderFactoryInterface $processBuilder)
    {
        $this->inflator = $processBuilder;

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

        $inflator = new ProcessBuilderFactory($finder->find($inflatorBinaryName));

        $deflator = null;

        if (static::getDefaultInflatorBinaryName() !== static::getDefaultDeflatorBinaryName()) {
            $deflatorBinaryName = $deflatorBinaryName ?: static::getDefaultDeflatorBinaryName();
            $deflator = new ProcessBuilderFactory($finder->find($deflatorBinaryName));
        }

        try {
            $outputParser = ParserFactory::create(static::getName());
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException(sprintf(
                'Failed to get a new instance of %s',
                get_called_class()), $e->getCode(), $e
            );
        }

        return new static($outputParser, $inflator, $deflator);
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
                ($file instanceof MemberInterface ? $file->getLocation() : $file)
            );

            $iterations++;
        });

        return 0 !== $iterations;
    }

    /**
     * Adds a resource to argument list
     *
     * @param Array          $files   An array of resource or resource URI
     * @param ProcessBuilder $builder A Builder instance
     *
     * @return AbstractBinaryAdapter
     *
     * @throws RuntimeException In case resource could not be opened or readed
     * @throws IOException In case Temporary direcory or fetched file could not be created
     * @throws InvalidArgumentException In case the provided resource is invalid
     */
    protected function addBuilderResourceArgument(array $files, ProcessBuilder $builder)
    {
        array_walk($files, function($resource, $location) use ($builder) {
            $stream = null;
            $location = ltrim($location, '/');

            if (is_resource($resource)) {
                $stream = $resource;
            } elseif (is_string($resource)) {
                $stream = fopen($resource, 'r');
            }

            if (null === $stream) {
                throw new InvalidArgumentException('A resource must be either a resource or a path to the resource');
            }

            if (false === $stream) {
                throw new RuntimeException(sprintf('Fail to open stream %s', $resource));
            }

            if (is_numeric($location)) {
                $meta = stream_get_meta_data($stream);
                $location = basename($meta['uri']);
            }

            $tempFileInfo = new \SplFileInfo(sprintf('%s/%s', getcwd(), $location));

            if (!is_dir($tempFileInfo->getPath()) && !mkdir($tempFileInfo->getPath(), 0777, true)) {
                throw new IOException(sprintf('Fail to create directory %s', $tempFileInfo->getPath()));
            }

            if (false === $content = stream_get_contents($stream)) {
                throw new RuntimeException(sprintf('Fail to fetch content from %s', $location));
            }

            try {
                $tempFile = $tempFileInfo->openFile('w');
                
                if (null !== $tempFile->fwrite($content)) {
                    $builder->add($location);
                }

                unset($tempFile, $tempFileInfo);
            } catch (\RuntimeException $e) {
                throw IOException(
                    sprintf('failed to write %s', $tempFileInfo->getRealPath()),
                    $e->getCode(),
                    $e
                );
            }

            fclose($stream);
        });

        return $this;
    }
}
