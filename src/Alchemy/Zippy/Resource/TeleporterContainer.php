<?php

namespace Alchemy\Zippy\Resource;

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
                    // todo :
                    // transform original resource in stream here instead to do
                    // this inside the stream teleporter
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
            return new StreamTeleporter();
        });
        $container['local-teleporter'] = $container->share(function(){
            return new LocalTeleporter();
        });
        $container['guzzle-teleporter'] = $container->share(function(){
            return new GuzzleTeleporter();
        });

        return $container;
    }
}
