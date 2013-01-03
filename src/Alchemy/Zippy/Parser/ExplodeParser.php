<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Alchemy\Zippy\Parser;

class ExplodeParser implements ParserInterface
{
    /**
     * The delimiter use to split a string
     *
     * @var String
     */
    protected $delimiter;

    public function __construct($delimiter = "\n")
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @inheritdoc
     */
    public function parse($output)
    {
        return array_values(array_filter(explode($this->delimiter, $output)));
    }

    /**
     * Returns the delimiter
     *
     * @return String
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Sets the delimiter
     *
     * @param String $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }
}
