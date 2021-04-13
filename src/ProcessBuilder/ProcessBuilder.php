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

class ProcessBuilder implements ProcessBuilderInterface
{
    /**
     * The command to run and its arguments listed as separate entries
     *
     * @var array
     */
    private $command;

    /**
     * The working directory or null to use the working dir of the current PHP process
     *
     * @var string|null
     */
    private $cwd;

    /**
     * ProcessBuilder constructor.
     * @param array $command
     */
    public function __construct($command)
    {
        $this->command = $command;
        $this->cwd = null;
    }

    /**
     * Creates a Process instance and returns it
     *
     * @return Process
     */
    public function getProcess()
    {
        $process =  new Process($this->command, $this->cwd);
        $process->setTimeout(null);

        return $process;
    }

    /**
     * @inheritdoc
     */
    public function add($argument)
    {
        $this->command = array_merge($this->command, array($argument));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setWorkingDirectory($directory)
    {
        $this->cwd = $directory;

        return $this;
    }

    /**
     * The command to run or a binary path and its arguments listed as separate entries
     *
     * @param array $command
     *
     * @return static
     */
    public static function create(array $command)
    {
        return new static($command);
    }

}
