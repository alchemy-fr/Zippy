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

interface ParserInterface
{
    /**
     * Parse a string
     *
     * @param String $output The string to parse
     *
     * @return Array The chunk of output
     */
    public function parse($output);
}
