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

use Alchemy\Zippy\Archive;
use Alchemy\Zippy\Options;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * A set of options
     *
     * @var Options
     */
    protected $options;

    /**
     * @inheritdoc
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * @inheritdoc
     */
    public function open($path)
    {
        return new Archive($path, $this);
    }
}
