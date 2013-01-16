<?php

namespace Alchemy\Zippy\Tests\Parser;

use Alchemy\Zippy\Parser\ZipOutputParser;
use Alchemy\Zippy\Tests\AbstractTestFramework;
use Alchemy\Zippy\MemberInterface;

class ZipOutputParserTest extends AbstractTestFramework
{
    public function testNewParser()
    {
        return new ZipOutputParser();
    }

    /**
     * @depends testNewParser
     */
    public function testParseFileListing($parser)
    {
        $current_timezone = ini_get('date.timezone');
        ini_set('date.timezone', 'UTC');

        $output =
"Length   Date     Time     Name
-------- ----     ----     ----
     0   2006-06-09 12:06  practice/
 10240   2006-06-09 12:06  practice/records
--------                    -------
    785                      2 files";

        $members = $parser->parseFileListing($output);

        $this->assertEquals(2, count($members));

        foreach ($members as $member) {
            $this->assertTrue($member instanceof MemberInterface);
        }

        $memberDirectory = array_shift($members);

        $this->assertTrue($memberDirectory->isDir());
        $this->assertEquals('practice/', $memberDirectory->getLocation());
        $this->assertEquals(0, $memberDirectory->getSize());
        $date = $memberDirectory->getLastModifiedDate();
        $this->assertTrue($date instanceof \DateTime);
        $this->assertEquals('1149854760', $date->format("U"));

        $memberFile = array_pop($members);

        $this->assertFalse($memberFile->isDir());
        $this->assertEquals('practice/records', $memberFile->getLocation());
        $this->assertEquals(10240, $memberFile->getSize());
        $date = $memberFile->getLastModifiedDate();
        $this->assertTrue($date instanceof \DateTime);
        $this->assertEquals('1149854760', $date->format("U"));

        ini_set('date.timezone', $current_timezone);
    }

    /**
     * @depends testNewParser
     */
    public function testParseDeflatorVersion($parser)
    {
        $output = "UnZip 5.52 of 28 February 2005, by Info-ZIP.  Maintained by C. Spieler.  Send
bug reports using http://www.info-zip.org/zip-bug.html; see README for details.

Usage: unzip [-Z] [-opts[modifiers]] file[.zip] [list] [-x xlist] [-d exdir]
  Default action is to extract files in list, except those in xlist, to exdir;
  file[.zip] may be a wildcard.  -Z => ZipInfo mode ('unzip -Z' for usage).

  -p  extract files to pipe, no messages     -l  list files (short format)
  -f  freshen existing files, create none    -t  test compressed archive data
  -u  update files, create if necessary      -z  display archive comment
  -x  exclude files that follow (in xlist)   -d  extract files into exdir

modifiers:                                   -q  quiet mode (-qq => quieter)
  -n  never overwrite existing files         -a  auto-convert any text files
  -o  overwrite files WITHOUT prompting      -aa treat ALL files as text
  -j  junk paths (do not make directories)   -v  be verbose/print version info
  -C  match filenames case-insensitively     -L  make (some) names lowercase
  -X  restore UID/GID info                   -V  retain VMS version numbers
  -K  keep setuid/setgid/tacky permissions   -M  pipe through 'more' pager
Examples (see unzip.txt for more info):
  unzip data1 -x joe   => extract all files except joe from zipfile data1.zip
  unzip -p foo | more  => send contents of foo.zip via pipe into program more
  unzip -fo foo ReadMe => quietly replace existing ReadMe if archive file newer";

        $this->assertEquals('5.52', $parser->parseDeflatorVersion($output));
    }

    /**
     * @depends testNewParser
     */
    public function testParseInflatorVersion($parser)
    {
        $output = "Copyright (c) 1990-2008 Info-ZIP - Type 'zip '-L'' for software license.
This is Zip 3.0 (July 5th 2008), by Info-ZIP.
Currently maintained by E. Gordon.  Please send bug reports to
the authors using the web page at www.info-zip.org; see README for details.

Latest sources and executables are at ftp://ftp.info-zip.org/pub/infozip,
as of above date; see http://www.info-zip.org/ for other sites.

Compiled with gcc 4.2.1 Compatible Apple Clang 4.0 (tags/Apple/clang-418.0.60) for Unix (Mac OS X) on Jun 20 2012.

Zip special compilation options:
	USE_EF_UT_TIME       (store Universal Time)
	SYMLINK_SUPPORT      (symbolic links supported)
	LARGE_FILE_SUPPORT   (can read and write large files on file system)
	ZIP64_SUPPORT        (use Zip64 to store large files in archives)
	STORE_UNIX_UIDs_GIDs (store UID/GID sizes/values using new extra field)
	UIDGID_16BIT         (old Unix 16-bit UID/GID extra field also used)
	[encryption, version 2.91 of 05 Jan 2007] (modified for Zip 3)

Encryption notice:
	The encryption code of this program is not copyrighted and is
	put in the public domain.  It was originally written in Europe
	and, to the best of our knowledge, can be freely distributed
	in both source and object forms from any country, including
	the USA under License Exception TSU of the U.S. Export
	Administration Regulations (section 740.13(e)) of 6 June 2002.

Zip environment options:
             ZIP:  [none]
          ZIPOPT:  [none]";

        $this->assertEquals('3.0', $parser->parseInflatorVersion($output));
    }
}
