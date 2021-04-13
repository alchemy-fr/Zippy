<?php

namespace Alchemy\Zippy\Functional;

class ListArchiveTest extends FunctionalTestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testOpen()
    {
        $adapter = $this->getAdapter();
        $archiveFile = $this->getArchiveFileForAdapter($adapter);

        $archive = $adapter->open($archiveFile);

        return $archive;
    }

    /**
     * @depends testOpen
     */
    public function testList($archive)
    {
        $target = __DIR__ . '/samples/tmp';

        $files2find = array(
            'directory/',
            'directory/README.md',
            'directory/photo.jpg'
        );

        foreach ($archive as $member) {
            $this->assertInstanceOf('Alchemy\Zippy\Archive\MemberInterface', $member);
            $this->assertContains($member->getLocation(), $files2find);
            unset($files2find[array_search($member->getLocation(), $files2find)]);
        }

        $this->assertEquals(array(), $files2find);
    }

    /**
     * @depends testOpen
     */
    public function testCount($archive)
    {
        $this->assertCount(3, $archive);
    }
}
