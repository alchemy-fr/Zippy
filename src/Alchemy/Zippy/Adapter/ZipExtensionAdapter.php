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
use Alchemy\Zippy\Archive\Member;

/**
 * ZipExtensionAdapter allows you to create and extract files from archives using PHP Zip extension
 *
 * @see http://www.php.net/manual/en/book.zip.php
 */
class ZipExtensionAdapter extends AbstractAdapter 
{
    private $zip;
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
        try{
            return new ZipExtensionAdapter();
        }catch(RuntimeException $e){
            throw $e;
        }
    }
   
    public function __construct()
    {
        if (!$this->isSupported()){
            throw new RuntimeException("Zip Extension not available");
        }
        $this->zip=new \ZipArchive();
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
    public function listMembers($path)
    {
        $this->_open($path);
        $members = array();
        for ($i=0;$i<$this->zip->numFiles;$i++){
            $stat=$this->zip->statIndex($i);
            $members[] = new Member(
             $path,
             $this,
             $stat['name'],
             $stat['size'],
             new \DateTime('@'.$stat['mtime']), 
             strlen($this->zip->getFromIndex($i,1))==0
            );
        }
        $this->zip->close();
        return $members;
    }
    
    /**
     * @inheritdoc
     */
    public static function getName()
    {
        return 'zip';
    }
    
    /**
     * @inheritdoc
     */
    public function extract($path, $to = null)
    {
        return $this->extractMembers($path, null, $to);
    }
   
    /**
     * @inheritdoc
     */
    public function extractMembers($path, $members, $to = null)
    {
        if (null === $to ) {
            $to=dirname(realpath($path)); // if no destination is given, will extract to zip current folder
        }
        if (!is_dir($to)){
            throw new InvalidArgumentException(sprintf("%s is not a directory", $to));
        }
        if (!is_writable($path)){
            throw new InvalidArgumentException(sprintf("%s is not writable", $to));
        }
       
        $this->_open($path);
        if (null!==$members){
           $membersTemp=(array) $members;
           if (empty($membersTemp)){
               $this->zip->close();
               throw new InvalidArgumentException("no members provided");
           }
           $members=array();
           foreach ($membersTemp as $member){ // allows $members to be an array of strings or array of Members
               if ($member instanceof Member){
                   $member=$member->getLocation();
               }
               if ($this->zip->locateName($member)===false){
                   $this->zip->close();
                   throw new InvalidArgumentException(sprintf('%s is not on the zip file', $member));
               }
               $members[]=$member;
           }
        }
       
        if (!$this->zip->extractTo($to, $members)){
            $this->zip->close();
            throw new InvalidArgumentException($this->zip->getStatusString());
        }
        $this->zip->close();
        return new \SplFileInfo($to);
    }
    
    /**
     * @inheritdoc
     */
    public function remove($path, $files)
    {
        $files=(array)$files;
        if (empty($files)){
            throw new InvalidArgumentException("no files provided");
        }
        $this->_open($path);
        
        // either extract all files or none in case of error
        foreach ($files as $file){
            if ($this->zip->locateName($file)===false){
                $this->zip->unchangeAll();
                $this->zip->close();
                throw new InvalidArgumentException(sprintf('%s is not on the zip file', $file));
            }
            if (!$this->zip->deleteName($file)){
                $this->zip->unchangeAll();
                $this->zip->close();
                throw new RuntimeException(sprintf('unable to delete %s', $file));
            }
        }
        
        $this->zip->close();
        return $files;
    }
   
    /**
     * @inheritdoc
     */
    public function add($path, $files, $recursive = true)
    {
        $this->_open($path);
        $this->addEntries($files,$recursive);
        return $files;
    }
    
    /**
     * @inheritdoc
     */
    public function create($path, $files = null, $recursive = true)
    {
        $this->_open($path,\ZIPARCHIVE::CREATE);
        $this->addEntries($files,$recursive);
        return new Archive($path, $this);
    }
    
    // HELPER METHODS
    private function _open($path,$mode=\ZIPARCHIVE::CHECKCONS)
    {
        $res=$this->zip->open($path,$mode);
        if ($res!==true) {
            throw new RuntimeException($this->errorCodesMapping[$res]);
        }
    }
    
    private function addEntries($files, $recursive)
    {
        $files=(array)$files;
        if (empty($files)){
            throw new InvalidArgumentException("no files provided");
        }
        $stack=new \SplStack();
        
        foreach ($files as $file){
            $this->checkReadability($file);
            if (is_dir($file)){
                if ($recursive){
                    $stack->push($file.((substr($file,-1)==DIRECTORY_SEPARATOR)?'':DIRECTORY_SEPARATOR ));
                }else{
                    $this->addEmptyDir($file);
                }
            }else{
                $this->addFileToZip($file);
            }
        }
        
        while(!$stack->isEmpty()){
            $dir=$stack->pop();
            $files=array_diff(scandir($dir),array(".","..")); // removes . and ..
            if (count($files)>0){ 
                foreach ($files as $file){
                    $file=$dir.$file;
                    $this->checkReadability($file);
                    if (is_dir($file)){
                        $stack->push($file.DIRECTORY_SEPARATOR);
                    }else{
                        $this->addFileToZip($file);
                    }
                }
            }else{
                $this->addEmptyDir($dir);
            }
        }
        $this->zip->close();
    }
    
    private function checkReadability($file)
    {
        if (!is_readable($file)){
            $this->zip->unchangeAll();
            $this->zip->close();
            throw new InvalidArgumentException(sprintf('could not read %s', $file));
        }
    }

    private function addFileToZip($file)
    {
        if (!$this->zip->addFile($file)){
            $this->zip->unchangeAll();
            $this->zip->close();
            throw new RuntimeException(sprintf('unable to add %s to the zip file', $file));
        }
    }

    private function addEmptyDir($dir)
    {
        if (!$this->zip->addEmptyDir($dir)){
            $this->zip->unchangeAll();
            $this->zip->close();
            throw new RuntimeException(sprintf('unable to add %s to the zip file', $dir));
        }
    }
    
}
