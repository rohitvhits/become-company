<?php

namespace App\Exceptions;

use Exception;

class GhostscriptException extends Exception
{
    protected $command;
    protected $output;

    public function __construct($message = "Ghostscript execution failed", $command = null, $output = [], $code = 0)
    {
        parent::__construct($message, $code);

        $this->command = $command;
        $this->output  = $output;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getOutput()
    {
        return $this->output;
    }
}