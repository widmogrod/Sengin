<?php

namespace Sengin\Extractor;

use Sengin\Extractor;
use Sengin\DataSource;
use Sengin\Extraction\Result;
use Sengin\Extraction\SearchResult;
use Sengin\Extraction\SuggestedKeyword;

class GoogleSearch implements Extractor
{
    /**
     * @var \DOMDocument
     */
    protected $_document;

    /**
     * @var \Sengin\DataSource
     */
    protected $_dataSource;

    /**
     * Disable libxml errors and allow user to fetch error information as needed
     * @var bool
     */
    protected $_disableLibXmlErrors = true;

    public function __construct(DataSource $dataSource)
    {
        $this->_dataSource = $dataSource;
    }

    /**
     * @return \DOMDocument
     */
    public function getDocument()
    {
        if (null === $this->_document) {
            $this->_document = new \DOMDocument;
        }
        return $this->_document;
    }

    /**
     * @param boolean $disableLibXmlErrors
     */
    public function setDisableLibXmlErrors($disableLibXmlErrors)
    {
        $this->_disableLibXmlErrors = (bool) $disableLibXmlErrors;
    }

    /**
     * @return boolean
     */
    public function getDisableLibXmlErrors()
    {
        return $this->_disableLibXmlErrors;
    }

    /**
     * @return \Sengin\Extraction\Result
     * @throws Exception\Exception
     */
    public function extract()
    {
        $data = $this->_dataSource->getData();
        $document = $this->getDocument();

        /*
        * If disable libxml errors is set to true then we see no more errors like that:
        * Warning: DOMDocument::loadHTML(): htmlParseEntityRef: expecting ';' in Entity
        */
        $previos = libxml_use_internal_errors($this->getDisableLibXmlErrors());
        $isLoaded = $document->loadHTML($data);
        libxml_use_internal_errors($previos);

        if (!$isLoaded)
        {
            $message = "Can't load html data from given source";
            throw new Exception\Exception($message);
        }

        $result = new Result();
        $this->extractSearchResults($result);
        $this->extractSponsoredResults($result);
        $this->extractSuggstions($result);

        return $result;
    }

    protected function extractSearchResults(\Sengin\Extraction $extraction)
    {
        $document = $this->getDocument();
        $xpath = new \DOMXPath($document);

        $elements = $xpath->query(
            "//li[contains(normalize-space(@class), 'g')]".
            "//h3[contains(normalize-space(@class), 'r')]".
            "//a"
        );

        if (!count($elements)) {
            return false;
        }

        $results = new \ArrayObject();
        $position = 0;

        foreach($elements as $key => /* @var $element \DOMElement */ $element)
        {
            $url = $element->getAttribute('href');

            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $queryArray);
            $url = isset($queryArray['q']) ? $queryArray['q'] : $url;

            $title = $element->textContent;
            $title = $this->filterWhitespaces($title);

            $result = new SearchResult();
            $result->setUrl($url);
            $result->setTitle($title);
            $result->setPosition(++$position);

            $results->append($result);
        }

        $extraction->setSearchResults($results);
    }

    protected function extractSponsoredResults(\Sengin\Extraction $extraction)
    {
        $document = $this->getDocument();
        $xpath = new \DOMXPath($document);

        $elements = $xpath->query(
            "//li".
            "//h3".
            "//a[starts-with(normalize-space(@href), '/aclk?sa=')]"
        );

        if (!count($elements)) {
            return false;
        }

        $results = new \ArrayObject();
        $position = 0;

        foreach($elements as $key => /* @var $element \DOMElement */ $element)
        {
            $url = $element->getAttribute('href');
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $queryArray);
            $url = isset($queryArray['c']) ? $queryArray['c'] : $queryArray['adurl'];

            $title = $element->textContent;
            $title = $this->filterWhitespaces($title);

            $result = new SearchResult();
            $result->setUrl($url);
            $result->setTitle($title);
            $result->setPosition(++$position);
            $results->append($result);
        }

        $extraction->setSponsoredResult($results);
    }

    protected function extractSuggstions(\Sengin\Extraction $extraction)
    {
        $document = $this->getDocument();
        $xpath = new \DOMXPath($document);

        /**
         * ct = broad-revision: (? ct = click through) Where was the link? Depending on the search result can “ct = title” or as “ct = Resalte” occur.
         * http://www.googlesearchblog.com/seo-universal-search.html
         */
        $elements = $xpath->query(
            "//p//a[contains(normalize-space(@href), 'ct=broad-revision')]"
        );

        if (!count($elements)) {
            return false;
        }

        $results = new \ArrayObject();

        foreach($elements as /* @var $element \DOMElement */ $element)
        {
            $keyword = $element->textContent;
            $keyword = $this->filterWhitespaces($keyword);

            $result = new SuggestedKeyword();
            $result->setKeyword($keyword);
            $results->append($result);
        }

        $extraction->setSuggestedKeywords($results);
    }

    protected function filterWhitespaces($string)
    {
        return implode(' ', array_map('trim', array_filter(explode(' ', $string))));
    }
}