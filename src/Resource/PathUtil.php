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

final class PathUtil
{
    /**
     * Extracts the file basename from a file path
     *
     * @param string $path File path
     * @return string The file basename or the input value if it has no path component.
     */
    public static function basename($path)
    {
        return (false === $pos = strrpos(strtr($path, '\\', '/'), '/')) ? $path : substr($path, $pos + 1);
    }

    /**
     * Sanitizes an extension.
     *
     * Strips dot from the beginning, converts to lowercase and remove trailing
     * whitespaces
     *
     * @param string $extension
     * @return string The sanitized extension
     */
    public static function sanitizeExtension($extension)
    {
        return ltrim(trim(mb_strtolower($extension)), '.');
    }
}
