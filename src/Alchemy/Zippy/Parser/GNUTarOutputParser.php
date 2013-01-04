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

/**
 * This class is responsable of parsing GNUTar command line output
 */
class GNUTarOutputParser implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function parseFileListing($output)
    {
        return array_values(array_filter(explode("\n", $output)));
    }

    /**
     * @inheritdoc
     */
    public function parseVersion($output)
    {
        $chuncks = explode(' ', $output, 3);

        if (2 > count($chuncks)) {
            return null;
        }

        list($name, $version) = $chuncks;

        return $version;
    }
}
