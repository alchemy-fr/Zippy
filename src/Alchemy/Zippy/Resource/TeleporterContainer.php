<?php

namespace Alchemy\Zippy\Resource;

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Resource\Teleporter\LocalTeleporter;
use Alchemy\Zippy\Resource\Teleporter\GuzzleTeleporter;
use Alchemy\Zippy\Resource\Teleporter\StreamTeleporter;

class TeleporterContainer extends \Pimple
{
    public function fromResource(Resource $resource)
    {
        switch(true)
        {
            case is_resource($resource->getOriginal()) :
                $teleporter = 'stream-teleporter';
                break;
            case is_string($resource->getOriginal()) :
                $data = parse_url($resource->getOriginal());

                if(!isset($data['scheme']) || 'file' === $data['scheme']) {
                    $teleporter = 'local-teleporter';
                } elseif (in_array($data['scheme'], array('http', 'https'))) {
                    $teleporter = 'guzzle-teleporter';
                } else {
                    $teleporter = 'stream-teleporter';
                }
                break;
            default:
                throw new InvalidArgumentException('No teleporter found');
                break;
        }

        return $this[$teleporter];
    }

    public static function load()
    {
        $container = new static();

        $container['stream-teleporter'] = $container->share(function(){
            return StreamTeleporter::create();
        });
        $container['local-teleporter'] = $container->share(function(){
            return LocalTeleporter::create();
        });
        $container['guzzle-teleporter'] = $container->share(function(){
            return GuzzleTeleporter::create();
        });

        return $container;
    }
}
