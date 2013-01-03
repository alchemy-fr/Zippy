<?php

namespace Alchemy\Zippy\Adapter;

interface BinaryAdapterInterface
{
    /**
     * Sets a custom binary
     * 
     * @param   String $path The path to the binary
     * 
     * @return  BinaryAdapterInterface
     * 
     * @throws InvalidArgumentException In case the binary is not executable
     */
    public function useBinary($path);
}
