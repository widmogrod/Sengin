<?php

namespace Sengin\DataSource\Options;

use Sengin\DataSource\Exception\InvalidArgumentException;

class Cache
{
    protected $_cacheDir;

    protected $_cacheEnabled = true;

    protected $_expirationTime = 86400; // default 24hd

    public function setCacheDir($cacheDir)
    {
        if (!(is_dir($cacheDir) && is_writable($cacheDir)))
        {
            $message = 'Given cache direcotory "%s" donsen\'t exists or isn\'t writable';
            $message = sprintf($message, $cacheDir);
            throw new InvalidArgumentException($message);
        }

        $testTempFilename = $cacheDir .'/'. uniqid(mt_rand()) .'.tmp';
        if (!$this->canCreateFileInPath($testTempFilename))
        {
            $message = 'Cache direcotory "%s" permisions don\'t allow to create files';
            $message = sprintf($message, $cacheDir);
            throw new InvalidArgumentException($message);
        }
        $this->_cacheDir = $cacheDir;
    }

    /**
     * @link http://www.php.net/manual/pl/function.is-writable.php#73596
     * @param $path
     * @return bool
     */
    private function canCreateFileInPath($path)
    {
        //will work in despite of Windows ACLs bug
        //NOTE: use a trailing slash for folders!!!
        //see http://bugs.php.net/bug.php?id=27609
        //see http://bugs.php.net/bug.php?id=30931

        $rm = is_file($path);
        if (false === ($f = fopen($path, 'w'))) {
            return false;
        }

        if (false === fwrite($f, "\n")) {
            fclose($f);
            return false;
        }

        fclose($f);

        if (!$rm) {
           unlink($path);
        }

        return true;
    }

    public function getCacheDir()
    {
        if (null === $this->_cacheDir)
        {
            $message = 'Given direcotory "%s" donsen\'t exists or isn\'t writable';
            throw new InvalidArgumentException($message);
        }
        return $this->_cacheDir;
    }

    public function setCacheEnabled($flag)
    {
        $this->_cacheEnabled = (bool) $flag;
    }

    public function isCacheEnabled()
    {
        return $this->_cacheEnabled;
    }

    public function setExpirationTime($expirationTime)
    {
        $expirationTime = filter_var($expirationTime, FILTER_VALIDATE_INT);
        if (false === filter_var($expirationTime, FILTER_VALIDATE_INT))
        {
            $message = 'Given expiration time is not in seconds';
            throw new InvalidArgumentException($message);
        }
        $this->_expirationTime = (int) $expirationTime;
    }

    public function getExpirationTime()
    {
        return $this->_expirationTime;
    }
}