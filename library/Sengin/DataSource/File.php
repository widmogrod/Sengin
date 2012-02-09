<?php

namespace Sengin\DataSource;

use Sengin\DataSource;
use Sengin\DataSource\Exception;

class File implements DataSource
{
    protected $_file;

    public function __construct($file)
    {
        if (!is_file($file))
        {
            $message = 'File "%s" don\'t exists';
            $message = sprintf($message, $file);
            throw new Exception($message);
        }

        if (!is_readable($file))
        {
            $message = 'File "%s" isn\'t readable';
            $message = sprintf($message, $file);
            throw new Exception($message);
        }

        $this->_file = $file;
    }

    public function getData()
    {
        return file_get_contents($this->_file);
    }
}