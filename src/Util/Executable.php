<?php

namespace Ahc\Phint\Util;

use Ahc\Cli\IO\Interactor;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

abstract class Executable
{
    /** @var Interactor */
    protected $io;

    /** @var string The binary executable */
    protected $binary;

    /** @var string */
    protected $workDir;

    public function __construct($binary = null)
    {
        $this->workDir = \getcwd();
        $this->binary  = $binary ? '"' . $binary . '"' : $this->binary;
    }

    public function withWorkDir($workDir = null)
    {
        if (!\is_dir($workDir)) {
            throw new \InvalidArgumentException('Not a valid working dir: ' . $workDir);
        }

        $this->workDir = $workDir;

        return $this;
    }

    protected function findBinary($binary)
    {
        if (\is_executable($binary)) {
            return $binary;
        }

        $finder = new ExecutableFinder();

        return $finder->find($binary) ?: $binary;
    }

    /**
     * Runs the command using underlying binary.
     *
     * @param string $command
     *
     * @return string|null The output of command.
     */
    protected function runCommand($command)
    {
        $self = $this;
        $proc = new Process($this->binary . ' ' . $command, $this->workDir, null, null, null);

        $proc->run();

        if ($proc->isSuccessful()) {
            return $proc->getOutput();
        }
    }
}
