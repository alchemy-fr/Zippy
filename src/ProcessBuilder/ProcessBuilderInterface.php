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

use phpDocumentor\Reflection\Types\String_;
use Symfony\Component\Process\Process;

interface ProcessBuilderInterface
{
    /**
     * Creates a Process instance and returns it
     *
     * @return Process
     */
    public function getProcess();

    /**
     * Adds an argument to the command string
     *
     * @param String_ $argument
     *
     * @return ProcessBuilder
     */
    public function add(String_ $argument);

    /**
     * Sets the working directory
     *
     * @param String_ $directory
     *
     * @return ProcessBuilder
     */
    public function setWorkingDirectory(String_ $directory);
}
