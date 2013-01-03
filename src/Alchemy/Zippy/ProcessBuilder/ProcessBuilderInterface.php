<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\ProcessBuilder;

use Symfony\Component\Process\Process;

interface ProcessBuilderInterface
{
    /**
     * Returns a process to add files to an archive
     * 
     * @param String    $path   The archive path
     * @param array     $files  An array of files
     * 
     * @return Process
     *
     * @throws InvalidArgumentException In case no files could be added
     */
    public function getAddFileProcess($path, array $files);
    
    /**
     * Returns a process to list archive members
     * 
     * @param String $path  The archive path
     * 
     * @return Process
     */
    public function getListMembersProcess($path);
    
    /**
     * Returns a process to create an archive
     * 
     * @param String    $path       The archive path
     * @param array     $files      An array of files
     * @param Boolean   $recursive  Recurse into directories
     * 
     * @return Process
     *
     * @throws InvalidArgumentException In case no files could be added
     */
    public function getCreateArchiveProcess($path, array $files = null, $recursive = true);
    
    /**
     * Returns a process to get binary version
     * 
     * @return Process
     */
    public function getVersionProcess();
    
    /**
     * Returns a process to get binary help
     * 
     * @return Process
     */
    public function getHelpProcess();
    
    /**
     * Returns the binary path
     * 
     * @return String
     */
    public function getBinary();

    /**
     * Stes the binary path
     * 
     * @param ProcessBuilderInterface
     */
    public function setBinary($binary);
}
