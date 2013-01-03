<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\ProcessBuilder;

use Alchemy\Zippy\Adapter\AbstractBinaryAdapter;
use Alchemy\Zippy\Adapter\AdapterInterface;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\ProcessBuilder\GNUTarProcessBuilder;
use Symfony\Component\Process\ExecutableFinder;

class ProcessBuilderFactory
{
    /**
     * Maps the corresponding process builder to the selected adapter
     * 
     * @param AbstractBinaryAdapter $adapter
     * 
     * @return AdapterInterface
     * 
     * @throws InvalidArgumentException In case no adapter were found
     */
    public function create(AbstractBinaryAdapter $adapter)
    {
        switch ($adapter->getName()) {
            case 'gnu-tar':
                return new GNUTarProcessBuilder($adapter->getDefaultBinaryName(), new ExecutableFinder());
                break;

            default:
                throw InvalidArgumentException();
                break;
        }
    }
}
