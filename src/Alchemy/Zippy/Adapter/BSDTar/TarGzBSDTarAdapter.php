<?php

namespace Alchemy\Zippy\Adapter\BSDTar;

class TarGzBSDTarAdapter extends TarBSDTarAdapter
{
    protected function getLocalOptions()
    {
        return array('--gzip');
    }
}
