<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy;

use Alchemy\Zippy\ArchiveInterface;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Options;

Interface AdapterInterface
{
    /**
     * Returns the adapter name
     *
     * @return  String
     */
    public function getName();
    
    /**
     * Returns the adapter options
     *
     * @return  Options
     */
    public function getOptions();
    
    /**
     * Sets adapter options
     * 
     * @param   Options $option
     * 
     * @return  AdapterInterface
     */
    public function setOptions(Options $option);
    
    /**
     * Opens an archive
     * 
     * @param   String  $path   The path to the archive
     * 
     * @return  ArchiveInterface
     * 
     * @throws  InvalidArgumentException in case of the provided path is not valid
     * @throws  RuntimeException in case of failure
     */
    public function open($path);

    /**
     * Creates a new archive
     * 
     * @param   String  $path   The path to the archive
     * 
     * @return  ArchiveInterface
     * 
     * @throws  RuntimeException in case of failure
     */
    public function create($path);

    /**
     * Tests adapter support for current environment
     *
     * @return Boolean
     */
    public static function isSupported();

}
