<?php

namespace Alchemy\Zippy\Adapter\BSDTar;

class TarBz2BSDTarAdapter extends TarBSDTarAdapter
{
    protected function getLocalOptions()
    {
        return array('--bzip2');
    }
}
