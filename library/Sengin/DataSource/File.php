<?php

namespace Sengin\DataSource;

use Sengin\DataSource;
use Sengin\DataSource\Exception\InvalidArgumentException;

class File implements DataSource
{
    protected $_file;

    public function __construct($file)
    {
        if (!is_file($file))
        {
            $message = 'File "%s" dosen\'t exists';
            $message = sprintf($message, $file);
            throw new InvalidArgumentException($message);
        }

        if (!is_readable($file))
        {
            $message = 'File "%s" isn\'t readable';
            $message = sprintf($message, $file);
            throw new InvalidArgumentException($message);
        }

        $this->_file = $file;
    }

    public function getData()
    {
        return file_get_contents($this->_file);
    }
}