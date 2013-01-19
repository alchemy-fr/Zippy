<?php

namespace Alchemy\Zippy\Adapter\GNUTar;

class TarGzGNUTarAdapter extends AbstractGNUTarAdapter
{
    protected function getLocalOptions()
    {
        return array('--gzip');
    }
}
