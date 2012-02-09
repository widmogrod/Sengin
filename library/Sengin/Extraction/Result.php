<?php
namespace Sengin\Extraction;

use Sengin\Extraction;

class Result implements Extraction
{
    protected $_indexCount;

    protected $_sponsoredResult;

    protected $_searchResults;

    protected $_suggestedKeywords;

    public function setIndexCount($indexCount)
    {
        $this->_indexCount = $indexCount;
    }

    public function getIndexCount()
    {
        return $this->_indexCount;
    }

    public function setSponsoredResult(\ArrayObject $sponsoredResult)
    {
        $this->_sponsoredResult = $sponsoredResult;
    }

    public function getSponsoredResult()
    {
        return $this->_sponsoredResult;
    }

    public function setSearchResults(\ArrayObject $searchResults)
    {
        $this->_searchResults = $searchResults;
    }

    public function getSearchResults()
    {
        return $this->_searchResults;
    }

    public function setSuggestedKeywords(\ArrayObject $suggestedKeywords)
    {
        $this->_suggestedKeywords = $suggestedKeywords;
    }

    public function getSuggestedKeywords()
    {
        return $this->_suggestedKeywords;
    }
}