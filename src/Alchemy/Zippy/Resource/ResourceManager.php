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

use Alchemy\Zippy\Exception\IOException;
use Alchemy\Zippy\Resource\RequestMapper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException as SfIOException;

/**
 * This class is responsible of handling resources retrievals mechanism
 */
class ResourceManager
{
    private $mapper;
    private $teleporter;
    private $filesystem;

    public function __construct(RequestMapper $mapper, ResourceTeleporter $teleporter, Filesystem $filesystem)
    {
        $this->mapper = $mapper;
        $this->filesystem = $filesystem;
        $this->teleporter = $teleporter;
    }

    public function handle($context, $request)
    {
        $resources = $this->mapper->map($context, $request);

        if(!$resources->canBeProcessedInPlace()){
            $context = sprintf('%s/%s', sys_get_temp_dir(), uniqid('zippy_'));

            try {
                $this->filesystem->mkdir($context);
            } catch (SfIOException $e) {
                throw new IOException(sprintf('Could not create temporary folder %s', $context), $e->getCode(), $e);
            }

            $resources->setContext($context);

            foreach ($resources as $resource) {
                $this->teleporter->teleport($context, $resource);
            }

            $resources->setTemporary(true);
        }

        return $resources;
    }

    public function cleanup(ResourceCollection $collection)
    {
        if($collection->isTemporary()) {
            try {
                $this->filesystem->remove($collection->getContext());
            } catch (IOException $e) {

            }
        }
    }

    public static function create()
    {
        return new static(RequestMapper::create(), ResourceTeleporter::create(), new Filesystem());
    }
}
