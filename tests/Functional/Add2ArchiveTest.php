<?php

namespace Alchemy\Zippy\Functional;

use Symfony\Component\Finder\Finder;

class Add2ArchiveTest extends FunctionalTestCase
{
    private static $file;

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        if (file_exists(self::$file)) {
            unlink(self::$file);
            self::$file = null;
        }
    }

    /**
     * @return \Alchemy\Zippy\Archive\ArchiveInterface
     */
    private function create()
    {
        $directory = __DIR__ . '/samples/directory';
        $emptyDirectory = __DIR__ . '/samples/directory/empty';
        $adapter = $this->getAdapter();
        $extension = $this->getArchiveExtensionForAdapter($adapter);

        self::$file = __DIR__ . '/samples/create-archive.' . $extension;

        if (! file_exists($emptyDirectory)) {
            mkdir($emptyDirectory);
        }
        $archive = $adapter->create(self::$file, array(
            'directory' => $directory,
        ), true);

        return $archive;
    }

    public function testAdd()
    {
        $archive = $this->create();

        $target = __DIR__ . '/samples/tmp';
        if (!is_dir($target)) {
            mkdir($target);
        }

        if (in_array(get_class($this->getAdapter()), array(
            'Alchemy\Zippy\Adapter\GNUTar\TarGzGNUTarAdapter',
            'Alchemy\Zippy\Adapter\GNUTar\TarBz2GNUTarAdapter',
            'Alchemy\Zippy\Adapter\BSDTar\TarGzBSDTarAdapter',
            'Alchemy\Zippy\Adapter\BSDTar\TarBz2BSDTarAdapter',
        ))) {
            $this->expectException('Alchemy\Zippy\Exception\NotSupportedException', 'Updating a compressed tar archive is not supported.');
        }

        $archive->addMembers(array('somemorefiles/nicephoto.jpg' => __DIR__ . '/samples/morefiles/morephoto.jpg'));
        $archive->extract($target);

        $finder = new Finder();
        $finder
            ->in($target);

        $files2find = array(
            '/directory',
            '/directory/empty',
            '/directory/README.md',
            '/directory/photo.jpg',
            '/somemorefiles',
            '/somemorefiles/nicephoto.jpg',
        );

        foreach ($finder as $file) {
            $this->assertEquals(0, strpos($file->getPathname(), $target));
            $member = substr($file->getPathname(), strlen($target));
            $this->assertContains($member, $files2find, "looking for $member in files2find");
            unset($files2find[array_search($member, $files2find)]);
        }

        $this->assertEquals(array(), $files2find);
    }
}
