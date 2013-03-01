<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Resource;

use Doctrine\Common\Collections\ArrayCollection;

class ResourceCollection extends ArrayCollection
{
    private $context;
    private $temporary = false;

    public function __construct($context, array $elements = array())
    {
        $this->context = $context;
        parent::__construct($elements);
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function isTemporary()
    {
        return $this->temporary;
    }

    public function setTemporary($temporary)
    {
        $this->temporary = (Boolean) $temporary;

        return $this;
    }

    public function canBeProcessedInPlace()
    {
        foreach($this as $resource) {
            if(!$resource->canBeProcessedInPlace($this->context)) {
                return false;
            }
        }

        return true;
    }
}
