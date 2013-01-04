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

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\ProcessBuilder\GNUTarProcessBuilder;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderInterface;
use Symfony\Component\Process\ExecutableFinder;

class ProcessBuilderFactory
{
    /**
     * Maps the corresponding process builder to the selected adapter
     *
     * @param String $adapterName       An adapter name
     * @param String $adapterBinaryName A binary path
     *
     * @return ProcessBuilderInterface
     *
     * @throws InvalidArgumentException In case no adapter were found
     */
    public static function create($adapterName, $adapterBinaryName)
    {
        switch ($adapterName) {
            case 'gnu-tar':
                return new GNUTarProcessBuilder($adapterBinaryName, new ExecutableFinder());
                break;

            default:
                throw InvalidArgumentException();
                break;
        }
    }
}
