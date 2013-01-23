<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Exception\RuntimeException;


/**
 * ZipExtensionAdapter allows you to create and extract files from archives using PHP Zip extension
 *
 * @see http://www.php.net/manual/en/book.zip.php
 */
class ZipExtensionAdapter extends AbstractAdapter 
{
    /**
     * Returns a new instance of the invoked adapter
     *
     * @return AbstractAdapter
     *
     * @throws RuntimeException In case object could not be instanciated
     */
    public static function newInstance()
    {
        
    }
    
     /**
     * @inheritdoc
     */
    public function isSupported()
    {
        return class_exists("\\ZipArchive");
    }
}
