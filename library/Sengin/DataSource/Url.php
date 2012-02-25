<?php

namespace Sengin\DataSource;

use Sengin\Definition;
use Sengin\DataSource;
use Sengin\DataSource\Exception\Exception;

class Url implements DataSource, Cachable
{
    /**
     * @var \Sengin\Definition
     */
    protected $_definition;

    protected $_proxy;

    protected $_timeout = 5;

    public function __construct(Definition $definition)
    {
        $this->_definition = $definition;
    }

    public function getData()
    {
        // laczenie z Google.
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_REFERER, $this->_definition->getDomain());
        curl_setopt($curl, CURLOPT_URL, $this->_definition->getUrl());
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getTimeout());

        if (null !== ($proxy = $this->getProxy())) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }

        $result = curl_exec($curl);

        if (0 != curl_errno($curl))
        {
            curl_close($curl);

            $message = 'CURL Error: %s';
            $message = sprintf($message, curl_error($curl));
            throw new Exception($message);
        }
        curl_close($curl);

        return $result;
    }

    public function getCacheKey()
    {
        return sha1($this->_definition->getUrl());
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
}