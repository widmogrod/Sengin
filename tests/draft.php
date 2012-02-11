<?php
namespace
{
    require_once (__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');
}


namespace Sengin
{
    $file = 'resources/googlesearch.html';

    $definition = new Definition\GoogleSearch();
    $definition->setQuery('test');
//    $definition->setOnPage(100);
//    $definition->setPage(1);
//    $definition->getUrl();

    $source = new DataSource\File($file);
//    $source->setUrl($definition->getUrl());
//    $request = $source->getRequest();

    $extractor = new Extractor\GoogleSearch($source);
    $extraction = $extractor->extract();

    $searchResults = $extraction->getSponsoredResult();
    $it = new \ArrayIterator($searchResults);

    while($it->valid())
    {
        /** @var $result \Sengin\Extraction\SearchResult */
        $result = $it->current();

        echo sprintf(
            'pos:%s, url:%s, title:%s'."\n",
            $result->getPosition(),
            $result->getUrl(),
            $result->getTitle()
        );

        $it->next();
    }
}