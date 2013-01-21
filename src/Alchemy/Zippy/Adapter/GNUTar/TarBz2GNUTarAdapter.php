<?php

namespace Alchemy\Zippy\Adapter\GNUTar;

class TarBz2GNUTarAdapter extends AbstractGNUTarAdapter
{
    protected function getLocalOptions()
    {
        return array('--bzip2');
    }
}
