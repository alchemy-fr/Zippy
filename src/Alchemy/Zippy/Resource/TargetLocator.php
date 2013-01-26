<?php

namespace Alchemy\Zippy\Resource;

class TargetLocator
{
    public function locate($context, $resource)
    {
        switch(true) {
            case is_resource($resource):
                return $this->locateFromResource($resource);
                break;
            case is_string($resource):
                return $this->locateFromString($context, $resource);
                break;
            default:
                throw new InvalidArgumentException('Unknown resource format');
                break;
        }
    }

    private function locateFromResource($resource)
    {
        $meta = stream_get_meta_data($resource);
        $data = parse_url($meta['uri']);

        return basename($data['path']);
    }

    private function locateFromString($context, $resource)
    {
        $url = parse_url($resource);

        // resource is a resource URI
        if (isset($url['scheme'])) {
            if ($this->isLocalFilesystem($url['scheme']) && $this->isFileInContext($url['path'], $context)) {
                return $this->getRelativePathFromContext($url['path'], $context);
            }

            return basename($resource);
        }

        // resource is a local path
        if ($this->isFileInContext($resource, $context)) {
            return $this->getRelativePathFromContext($resource, $context);
        } else {
            return basename($resource);
        }
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
        return false !== stripos($path, $context);
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
        return ltrim(str_replace($context, '', $path), '/');
    }

    /**
     * Gets the full path for a given location and a given resource path
     *
     * @param String $location A path to the desired resource destination
     * @param String $path     A path to a resource
     *
     * @return String
     */
    private function getCustomPath($location, $path)
    {
        return sprintf('%s/%s', ltrim(rtrim($location, '/'), '/'), basename($path));
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

    /**
     * Checks whether the given location is customized location
     *
     * @param String $location A desired location
     *
     * @return Boolean
     */
    private function isLocationCustomized($location)
    {
        return false === is_numeric($location);
    }
}
