<?php

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Exception\InvalidArgumentException;

interface BinaryAdapterInterface
{
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
}
