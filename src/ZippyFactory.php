<?php

namespace Alchemy\Zippy;

use Alchemy\Zippy\Adapter\Adapter;
use Alchemy\Zippy\Adapter\FileAdapter;
use Alchemy\Zippy\Adapter\Pecl\RarAdapter;
use Alchemy\Zippy\Adapter\Pear\TarAdapter;
use Alchemy\Zippy\Adapter\Pecl\ZipAdapter;
use Alchemy\Zippy\Package\IteratorResolver\ProtocolBasedIteratorResolver;
use Alchemy\Zippy\Resource\ReaderResolver\ProtocolBasedReaderResolver;
use Alchemy\Zippy\Resource\WriterResolver\ProtocolBasedWriterResolver;

class ZippyFactory
{
    /**
     * @var Adapter[]
     */
    private $adapters = [];

    public function __construct()
    {
        $this->adapters[] = new FileAdapter();
        $this->adapters[] = new TarAdapter();
        $this->adapters[] = new RarAdapter();
        $this->adapters[] = new ZipAdapter();
    }

    public function create()
    {
        $iteratorResolver = new ProtocolBasedIteratorResolver();
        $readerResolver = new ProtocolBasedReaderResolver();
        $writerResolver = new ProtocolBasedWriterResolver();

        foreach ($this->adapters as $adapter) {
            $adapter->registerProtocols($iteratorResolver, $readerResolver, $writerResolver);
        }

        return new Zippy($readerResolver, $writerResolver, $iteratorResolver);
    }
}
