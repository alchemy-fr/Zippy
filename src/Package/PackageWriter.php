<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Resource\ResourceUri;

interface PackageWriter
{

    public function write(Package $package, ResourceUri $destination);
}
