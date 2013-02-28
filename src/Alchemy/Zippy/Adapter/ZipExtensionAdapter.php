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

use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\NotSupportedException;
use Alchemy\Zippy\Archive\Member;
use Alchemy\Zippy\Adapter\Resource\ResourceInterface;
use Alchemy\Zippy\Adapter\Resource\ZipArchiveResource;
use Alchemy\Zippy\Archive\Archive;

/**
 * ZipExtensionAdapter allows you to create and extract files from archives using PHP Zip extension
 *
 * @see http://www.php.net/manual/en/book.zip.php
 */
class ZipExtensionAdapter extends AbstractAdapter
{
    private $errorCodesMapping=array(
        \ZIPARCHIVE::ER_EXISTS =>"File already exists",
        \ZIPARCHIVE::ER_INCONS =>"Zip archive inconsistent",
        \ZIPARCHIVE::ER_INVAL =>"Invalid argument",
        \ZIPARCHIVE::ER_MEMORY =>"Malloc failure",
        \ZIPARCHIVE::ER_NOENT =>"No such file",
        \ZIPARCHIVE::ER_NOZIP =>"Not a zip archive",
        \ZIPARCHIVE::ER_OPEN =>"Can't open file",
        \ZIPARCHIVE::ER_READ =>"Read error",
        \ZIPARCHIVE::ER_SEEK =>"Seek error"
    );

    /**
     * Returns a new instance of the invoked adapter
     *
     * @return AbstractAdapter
     *
     * @throws RuntimeException In case object could not be instanciated
     */
    public static function newInstance()
    {
        return new ZipExtensionAdapter();
    }

    public function __construct()
    {
        if (!$this->isSupported()) {
            throw new RuntimeException("Zip Extension is not available");
        }
    }

    /**
     * @inheritdoc
     */
    public function open($path)
    {
        return new Archive($path, $this, new ZipArchiveResource($this->_open($path)));
    }

    /**
     * @inheritdoc
     */
    public function isSupported()
    {
         return class_exists("\\ZipArchive");
    }

    /**
     * @inheritdoc
     */
    public function listMembers(ResourceInterface $resource)
    {
        $members = array();
        for ($i=0;$i<$resource->getResource()->numFiles;$i++) {
            $stat=$resource->getResource()->statIndex($i);
            $members[] = new Member(
             $resource,
             $this,
             $stat['name'],
             $stat['size'],
             new \DateTime('@'.$stat['mtime']),
             strlen($resource->getResource()->getFromIndex($i,1))==0
            );
        }

        return $members;
    }

    /**
     * @inheritdoc
     */
    public static function getName()
    {
        return 'zip-extension';
    }

    /**
     * @inheritdoc
     */
    public function extract(ResourceInterface $resource, $to = null)
    {
        return $this->extractMembers($resource, null, $to);
    }

    /**
     * @inheritdoc
     */
    public function extractMembers(ResourceInterface $resource, $members, $to = null)
    {
        if (null === $to) {
            $to=dirname(realpath($resource->getResource()->filename)); // if no destination is given, will extract to zip current folder
        }
        if (!is_dir($to)) {
            $resource->getResource()->close();
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }
        if (!is_writable($to)) {
            $resource->getResource()->close();
            throw new InvalidArgumentException(sprintf("%s is not writable", $to));
        }
        if (null!==$members) {
           $membersTemp=(array) $members;
           if (empty($membersTemp)) {
               $resource->getResource()->close();
               throw new InvalidArgumentException("no members provided");
           }
           $members=array();
           foreach ($membersTemp as $member) { // allows $members to be an array of strings or array of Members
               if ($member instanceof Member) {
                   $member=$member->getLocation();
               }
               if ($resource->getResource()->locateName($member)===false) {
                   $resource->getResource()->close();
                   throw new InvalidArgumentException(sprintf('%s is not on the zip file', $member));
               }
               $members[]=$member;
           }
        }

        if (!$resource->getResource()->extractTo($to, $members)) {
            $resource->getResource()->close();
            throw new InvalidArgumentException($resource->getResource()->getStatusString());
        }

        return new \SplFileInfo($to);
    }

    /**
     * @inheritdoc
     */
    public function remove(ResourceInterface $resource, $files)
    {
        $files=(array) $files;
        if (empty($files)) {
            throw new InvalidArgumentException("no files provided");
        }

        // either remove all files or none in case of error
        foreach ($files as $file) {
            if ($resource->getResource()->locateName($file)===false) {
                $resource->getResource()->unchangeAll();
                $resource->getResource()->close();
                throw new InvalidArgumentException(sprintf('%s is not on the zip file', $file));
            }
            if (!$resource->getResource()->deleteName($file)) {
                $resource->getResource()->unchangeAll();
                $resource->getResource()->close();
                throw new RuntimeException(sprintf('unable to delete %s', $file));
            }
        }
        $this->flush($resource->getResource());

        return $files;
    }

    /**
     * @inheritdoc
     */
    public function add(ResourceInterface $resource, $files, $recursive = true)
    {
        $files=(array) $files;
        if (empty($files)) {
            $resource->getResource()->close();
            throw new InvalidArgumentException("no files provided");
        }
        $this->addEntries($resource,$files,$recursive);

        return $files;
    }

    /**
     * @inheritdoc
     */
    public function create($path, $files = null, $recursive = true)
    {
        $files=(array) $files;
        if (empty($files)) {
            throw new NotSupportedException("Cannot create an empty zip");
        }
        $resource=new ZipArchiveResource($this->_open($path,\ZIPARCHIVE::CREATE));
        $this->addEntries($resource,$files,$recursive);

        return new Archive($path, $this,$resource);
    }

    // HELPER METHODS
    private function _open($path,$mode=\ZIPARCHIVE::CHECKCONS)
    {
        $zip=new \ZipArchive();
        $res=$zip->open($path,$mode);
        if ($res!==true) {
            throw new RuntimeException($this->errorCodesMapping[$res]);
        }

        return $zip;
    }

    private function addEntries(ZipArchiveResource $resource,array $files, $recursive)
    {
        $stack=new \SplStack();

        foreach ($files as $file) {
            $this->checkReadability($resource->getResource(),$file);
            if (is_dir($file)) {
                if ($recursive) {
                    $stack->push($file.((substr($file,-1)==DIRECTORY_SEPARATOR)?'':DIRECTORY_SEPARATOR ));
                } else {
                    $this->addEmptyDir($resource->getResource(),$file);
                }
            } else {
                $this->addFileToZip($resource->getResource(),$file);
            }
        }

        while (!$stack->isEmpty()) {
            $dir=$stack->pop();
            $files=array_diff(scandir($dir),array(".","..")); // removes . and ..
            if (count($files)>0) {
                foreach ($files as $file) {
                    $file=$dir.$file;
                    $this->checkReadability($resource->getResource(),$file);
                    if (is_dir($file)) {
                        $stack->push($file.DIRECTORY_SEPARATOR);
                    } else {
                        $this->addFileToZip($resource->getResource(),$file);
                    }
                }
            } else {
                $this->addEmptyDir($resource->getResource(),$dir);
            }
        }
        $this->flush($resource->getResource());
    }

    private function checkReadability(\ZIPARCHIVE $zip,$file)
    {
        if (!is_readable($file)) {
            $zip->unchangeAll();
            $zip->close();
            throw new InvalidArgumentException(sprintf('could not read %s', $file));
        }
    }

    private function addFileToZip(\ZIPARCHIVE $zip,$file)
    {
        if (!$zip->addFile($file)) {
            $zip->unchangeAll();
            $zip->close();
            throw new RuntimeException(sprintf('unable to add %s to the zip file', $file));
        }
    }

    private function addEmptyDir(\ZIPARCHIVE $zip,$dir)
    {
        if (!$zip->addEmptyDir($dir)) {
            $zip->unchangeAll();
            $zip->close();
            throw new RuntimeException(sprintf('unable to add %s to the zip file', $dir));
        }
    }

    private function flush(\ZIPARCHIVE $zip) // flush changes by reopening the file
    {
        $path=$zip->filename;
        $zip->close();
        $zip->open($path,\ZIPARCHIVE::CHECKCONS);
    }
}
