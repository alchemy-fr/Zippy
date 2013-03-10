<?php

namespace Alchemy\Zippy\Adapter\BSDTar;

use Alchemy\Zippy\Adapter\AbstractTarAdapter;

/**
 * BSDTAR allows you to create and extract files from archives using BSD tar
 *
 * @see http://people.freebsd.org/~kientzle/libarchive/man/bsdtar.1.txt
 */
class TarBSDTarAdapter extends AbstractTarAdapter
{
    /**
     * @inheritdoc
     */
    protected function getLocalOptions()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public static function getName()
    {
        return 'bsd-tar';
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultDeflatorBinaryName()
    {
        return array('bsdtar', 'tar');
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultInflatorBinaryName()
    {
        return array('bsdtar', 'tar');
    }

    /**
     * @inheritdoc
     */
    protected function isProperImplementation($versionOutput)
    {
        $lines = explode("\n", $versionOutput, 2);

        echo "is BSD tar supported ? output is ".$versionOutput."\n";

        return false !== stripos($lines[0], 'bsdtar');
    }
}
