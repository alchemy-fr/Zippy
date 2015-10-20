<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Adapter\BSDTar\TarBSDTarAdapter;
use Alchemy\Zippy\Adapter\BSDTar\TarGzBSDTarAdapter;
use Alchemy\Zippy\Adapter\BSDTar\TarBz2BSDTarAdapter;
use Alchemy\Zippy\Adapter\GNUTar\TarGNUTarAdapter;
use Alchemy\Zippy\Adapter\GNUTar\TarGzGNUTarAdapter;
use Alchemy\Zippy\Adapter\GNUTar\TarBz2GNUTarAdapter;
use Alchemy\Zippy\Resource\ResourceManager;
use Alchemy\Zippy\Resource\RequestMapper;
use Alchemy\Zippy\Resource\TeleporterContainer;
use Alchemy\Zippy\Resource\ResourceTeleporter;
use Alchemy\Zippy\Resource\TargetLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;

class AdapterContainer extends \Pimple\Container
{
    /**
     * Builds the adapter container
     *
     * @return AdapterContainer
     */
    public static function load()
    {
        $container = new static();

        $container['zip.inflator'] = null;
        $container['zip.deflator'] = null;

        $container['resource-manager'] = function ($container) {
            return new ResourceManager(
                $container['request-mapper'],
                $container['resource-teleporter'],
                $container['filesystem']
            );
        };

        $container['executable-finder'] = function ($container) {
            return new ExecutableFinder();
        };

        $container['request-mapper'] = function ($container) {
            return new RequestMapper($container['target-locator']);
        };

        $container['target-locator'] = function () {
            return new TargetLocator();
        };

        $container['teleporter-container'] = function ($container) {
            return TeleporterContainer::load();
        };

        $container['resource-teleporter'] = function ($container) {
            return new ResourceTeleporter($container['teleporter-container']);
        };

        $container['filesystem'] = function () {
            return new Filesystem();
        };

        $container['Alchemy\\Zippy\\Adapter\\ZipAdapter'] = function ($container) {
            return ZipAdapter::newInstance(
                $container['executable-finder'],
                $container['resource-manager'],
                $container['zip.inflator'],
                $container['zip.deflator']
            );
        };

        $container['gnu-tar.inflator'] = null;
        $container['gnu-tar.deflator'] = null;

        $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarGNUTarAdapter'] = function ($container) {
            return TarGNUTarAdapter::newInstance(
                $container['executable-finder'],
                $container['resource-manager'],
                $container['gnu-tar.inflator'],
                $container['gnu-tar.deflator']
            );
        };

        $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarGzGNUTarAdapter'] = function ($container) {
            return TarGzGNUTarAdapter::newInstance(
                $container['executable-finder'],
                $container['resource-manager'],
                $container['gnu-tar.inflator'],
                $container['gnu-tar.deflator']
            );
        };

        $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarBz2GNUTarAdapter'] = function ($container) {
            return TarBz2GNUTarAdapter::newInstance(
                $container['executable-finder'],
                $container['resource-manager'],
                $container['gnu-tar.inflator'],
                $container['gnu-tar.deflator']
            );
        };

        $container['bsd-tar.inflator'] = null;
        $container['bsd-tar.deflator'] = null;

        $container['Alchemy\\Zippy\\Adapter\\BSDTar\\TarBSDTarAdapter'] = function ($container) {
            return TarBSDTarAdapter::newInstance(
                $container['executable-finder'],
                $container['resource-manager'],
                $container['bsd-tar.inflator'],
                $container['bsd-tar.deflator']
            );
        };

        $container['Alchemy\\Zippy\\Adapter\\BSDTar\\TarGzBSDTarAdapter'] = function ($container) {
            return TarGzBSDTarAdapter::newInstance(
                $container['executable-finder'],
                $container['resource-manager'],
                $container['bsd-tar.inflator'],
                $container['bsd-tar.deflator']
            );
        };

        $container['Alchemy\\Zippy\\Adapter\\BSDTar\\TarBz2BSDTarAdapter'] = function ($container) {
            return TarBz2BSDTarAdapter::newInstance(
                $container['executable-finder'],
                $container['resource-manager'],
                $container['bsd-tar.inflator'],
                $container['bsd-tar.deflator']);
        };

        $container['Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter'] = function () {
            return ZipExtensionAdapter::newInstance();
        };

        return $container;
    }
}
