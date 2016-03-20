<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Zippy\Resource\ResourceUri;

interface PackageWriter
{

    public function write(Package $package, ResourceUri $destination);
}
