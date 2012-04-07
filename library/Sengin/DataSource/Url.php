<?php

namespace Sengin\DataSource;

use Sengin\Definition;
use Sengin\DataSource;
use Sengin\DataSource\Exception\Exception;
use Sengin\DataSource\Exception\InvalidArgumentException;

class Url implements DataSource, Cachable
{
    const USER_AGENT = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

    /**
     * @var \Sengin\Definition
     */
    protected $_definition;

    protected $_proxy;

    protected $_timeout = 5;

    protected $_url;

    protected $_referer;

    protected $_userAgent;

    public function __construct($data)
    {
        if (is_string($data))
        {
            $this->setUrl($data);
        }
        elseif ($data instanceof Definition)
        {
            $this->setUrl($data->getUrl());
            $this->setReferer($data->getDomain());
        }
    }

    public function getData()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());

        if(strtolower(parse_url($this->getUrl(), PHP_URL_SCHEME)) == 'https')
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
        }
        else
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        if ($referer = $this->getReferer()) {
            curl_setopt($curl, CURLOPT_REFERER, $referer);
        }

        if (null !== ($proxy = $this->getProxy())) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }

        curl_setopt($curl, CURLOPT_URL, $this->getUrl());
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getTimeout());

        $result = curl_exec($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if (0 != $errno)
        {
            $message = 'CURL Error: %s';
            $message = sprintf($message, $error);
            throw new Exception($message);
        }

        return $result;
    }

    public function getCacheKey()
    {
        return sha1($this->getUrl());
    }

    public function setProxy($proxy)
    {
        $this->_proxy = $proxy;
    }

    public function getProxy()
    {
        return $this->_proxy;
    }

    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
    }

    public function getTimeout()
    {
        return $this->_timeout;
    }

    public function setReferer($referer)
    {
        $this->_referer = $referer;
    }

    public function getReferer()
    {
        return $this->_referer;
    }

    public function setUrl($url)
    {
        $filtredUrl = $url;
//        if (!($filtredUrl = filter_var($url, FILTER_VALIDATE_URL))) {
//            $message = 'Given url "%s" is not valid.';
//            $message = sprintf($message, $url);
//            throw new InvalidArgumentException($message);
//        }

        $this->_url = $filtredUrl;
    }

    public function getUrl()
    {
        if (!$this->_url)
        {
            $message = 'The url is not specified. Use method "" to set url.';
            $message = sprintf($message, __CLASS__ . '::setUrl()');
            throw new InvalidArgumentException($message);
        }
        return $this->_url;
    }

    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;
    }

    public function getUserAgent()
    {
        if (null === $this->_userAgent) {
            return self::USER_AGENT;
        }
        return $this->_userAgent;
    }
}