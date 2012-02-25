<?php

namespace Sengin\Definition;

use Sengin\Definition;

class GoogleSearch implements Definition
{
    protected $_query;

    protected $_domain;

    protected $_onPage;

    public function setQuery($query)
    {
        $this->_query = (string) trim($query);
    }

    public function getQuery()
    {
        if (empty($this->_query) && $this->_query != 0)
        {
            $message = 'Search query have to be set';
            throw new Exception\InvalidArgumentException($message);
        }
        return $this->_query;
    }

    public function getUrl()
    {
        return sprintf(
            'http://%s/search?hl=pl&q=%s&num=%d',
            $this->getDomain(),
            urlencode($this->getQuery()),
            $this->getOnPage()
        );
    }

    public function setOnPage($onPage)
    {
        if ($onPage < 10 || $onPage > 100)
        {
            $message = 'On page should be bettwen 10 and 100. Given value is %s';
            $message = sprintf($message, $onPage);
            throw new Exception\InvalidArgumentException($message);
        }

        $this->_onPage = $onPage;
    }

    public function getOnPage()
    {
        if (null === $this->_onPage) {
            $this->_onPage = 10;
        }
        return $this->_onPage;
    }

    public function setDomain($domain)
    {
        $this->_domain = $domain;
    }

    public function getDomain()
    {
        if (null === $this->_domain) {
            $this->_domain = 'www.google.pl';
        }
        return $this->_domain;
    }
}