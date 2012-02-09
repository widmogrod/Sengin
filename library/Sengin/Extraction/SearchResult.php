<?php
namespace Sengin\Extraction;

class SearchResult
{
    protected $_url;

    protected $_title;

    protected $_position;

    public function setUrl($url)
    {
        $this->_url = $url;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function setPosition($position)
    {
        $this->_position = $position;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function getTitle()
    {
        return $this->_title;
    }
}