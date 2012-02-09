<?php
namespace Sengin;

interface Extraction
{
    public function setSponsoredResult(\ArrayObject $sponsoredResult);

    public function getSponsoredResult();

    public function setSearchResults(\ArrayObject $searchResults);

    public function getSearchResults();

    public function setSuggestedKeywords(\ArrayObject $suggestedKeywords);

    public function getSuggestedKeywords();

    public function setIndexCount($indexCount);

    public function getIndexCount();
}