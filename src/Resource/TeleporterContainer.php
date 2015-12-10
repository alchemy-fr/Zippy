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

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Resource\Teleporter\LocalTeleporter;
use Alchemy\Zippy\Resource\Teleporter\GuzzleTeleporter;
use Alchemy\Zippy\Resource\Teleporter\StreamTeleporter;
use Alchemy\Zippy\Resource\Teleporter\TeleporterInterface;

/**
 * A container of TeleporterInterface
 */
class TeleporterContainer
{
    /**
     * @var TeleporterInterface[]
     */
    private $teleporters = array();

    /**
     * @var callable[]
     */
    private $factories = array();

    /**
     * Returns the appropriate TeleporterInterface for a given Resource
     *
     * @param Resource $resource
     * @return TeleporterInterface
     */
    public function fromResource(Resource $resource)
    {
        switch (true) {
            case is_resource($resource->getOriginal()):
                $teleporter = 'stream-teleporter';
                break;
            case is_string($resource->getOriginal()):
                $data = parse_url($resource->getOriginal());

                if (!isset($data['scheme']) || 'file' === $data['scheme']) {
                    $teleporter = 'local-teleporter';
                } elseif (in_array($data['scheme'], array('http', 'https')) && isset($this->factories['guzzle-teleporter'])) {
                    $teleporter = 'guzzle-teleporter';
                } else {
                    $teleporter = 'stream-teleporter';
                }
                break;
            default:
                throw new InvalidArgumentException('No teleporter found');
        }

        return $this->getTeleporter($teleporter);
    }

    private function getTeleporter($typeName)
    {
        if (! isset($this->teleporters[$typeName])) {
            $factory = $this->factories[$typeName];
            $this->teleporters[$typeName] = $factory();
        }

        return $this->teleporters[$typeName];
    }

    /**
     * Instantiates TeleporterContainer and register default teleporters
     *
     * @return TeleporterContainer
     */
    public static function load()
    {
        $container = new static();

        $container->factories['stream-teleporter'] = function () {
            return StreamTeleporter::create();
        };

        $container->factories['local-teleporter'] = function () {
            return LocalTeleporter::create();
        };

        if (class_exists('Guzzle\Http\Client')) {
            $container->factories['guzzle-teleporter'] = function () {
                return GuzzleTeleporter::create();
            };
        }

        return $container;
    }
}
