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

use Alchemy\Zippy\Exception\TargetLocatorException;
use Alchemy\Zippy\Exception\InvalidArgumentException;

class TargetLocator
{
    public function locate($context, $resource)
    {
        switch (true) {
            case is_resource($resource):
                return $this->locateResource($resource);
                break;
            case is_string($resource):
                return $this->locateString($context, $resource);
                break;
            default:
                throw new InvalidArgumentException('Unknown resource format');
                break;
        }
    }

    private function locateResource($resource)
    {
        $meta = stream_get_meta_data($resource);
        $data = parse_url($meta['uri']);

        if (!isset($data['path'])) {
            throw new TargetLocatorException($resource, 'Unable to retrieve path from resource');
        }

        return basename($data['path']);
    }

    private function locateString($context, $resource)
    {
        $url = parse_url($resource);

        if (isset($url['scheme']) && $this->isLocalFilesystem($url['scheme'])) {
            $resource = $url['path'] = $this->cleanupPath($url['path']);;
        }

        // resource is a URI
        if (isset($url['scheme'])) {
            if ($this->isLocalFilesystem($url['scheme']) && $this->isFileInContext($url['path'], $context)) {
                return $this->getRelativePathFromContext($url['path'], $context);
            }

            return basename($resource);
        }

        // resource is a local path
        if ($this->isFileInContext($resource, $context)) {
            $resource = $this->cleanupPath($resource);

            return $this->getRelativePathFromContext($resource, $context);
        } else {
            return basename($resource);
        }
    }

    private function cleanupPath($path)
    {
        if (false === $cleanPath = realpath($path)) {
            throw new InvalidArgumentException(sprintf('%s is an invalid location', $path));
        }

        return $cleanPath;
    }

    /**
     * Checks wheteher the path belong to the context
     *
     * @param String $path A resource path
     *
     * @return Boolean
     */
    private function isFileInContext($path, $context)
    {
        return 0 === strpos($path, $context);
    }

    /**
     * Gets the relative path from the context for the given path
     *
     * @param String $path A resource path
     *
     * @return String
     */
    private function getRelativePathFromContext($path, $context)
    {
        return ltrim(str_replace($context, '', $path), '/\\');
    }

    /**
     * Checks if a scheme reffers to a local filesystem
     *
     * @param String $scheme
     *
     * @return Boolean
     */
    private function isLocalFilesystem($scheme)
    {
        return 'plainfile' === $scheme || 'file' === $scheme;
    }
}
