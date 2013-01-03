<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Options;

abstract class AbstractAdapter
{
    /**
     * A set of options
     *
     * @var Options
     */
    protected $options;

    /**
     * Sets options
     *
     * @param  Options         $options
     * @return AbstractAdapter
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Gets options
     *
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns an Iterator for the current files selection
     *
     * @param String|Array|\Traversable $files A filename, an array of files, or a \Traversable instance
     *
     * @return \ArrayObject
     */
    protected function getFilesIterator($files)
    {
        if (!$files instanceof \Traversable) {
            $files = new \ArrayObject((array) $files);
        }

        return $files;
    }
}
