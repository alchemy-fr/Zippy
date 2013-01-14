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

use Alchemy\Zippy\Member;

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
        $lines = array_values(array_filter(explode("\n", $output)));
        $members = array();

        foreach ($lines as $line) {
            $chunks = explode(' ', trim($line));

            $members[] = new Member(
                $chunks[7],
                $chunks[2],
               \DateTime::createFromFormat("F d H:i Y", sprintf('%s %s %s %s',
                   $chunks[3],
                   $chunks[4],
                   $chunks[5],
                   $chunks[6]
                )),
                'd' === $line[0]
            );
        }

        return $members;
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
