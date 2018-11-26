<?php

namespace Alchemy\Zippy\ProcessBuilder;

use Symfony\Component\Process\Process;

class ZippyProcess extends Process
{
    /**
     * @param string $argument The argument to append to the commandline string
     * @return $this
     */
    public function add($argument) {
        $commandline = explode(' ', $this->getCommandLine());
        $commandline[] = $argument;
        $this->setCommandLine($commandline);
        return $this;
    }
}
