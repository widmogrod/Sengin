<?php
namespace
{
    require_once (__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');
}


//namespace Sengin
//{
//    $file = 'resources/googlesearch.html';
//
//    $definition = new Definition\GoogleSearch();
//    $definition->setQuery('test');
//
//    $source = new DataSource\File($file);
//
//    $extractor = new Extractor\GoogleSearch($source);
//    $extraction = $extractor->extract();
//
//    $searchResults = $extraction->getSponsoredResult();
//    $it = new \ArrayIterator($searchResults);
//
//    while($it->valid())
//    {
//        /** @var $result \Sengin\Extraction\SearchResult */
//        $result = $it->current();
//
//        echo sprintf(
//            'pos:%s, url:%s, title:%s'."\n",
//            $result->getPosition(),
//            $result->getUrl(),
//            $result->getTitle()
//        );
//
//        $it->next();
//    }
//}

//namespace Sengin
//{
//    $definition = new Definition\GoogleSearch();
//    $definition->setQuery('Agencja Reklamowa Kraków -katalog -drukarnia + kontakt');
//    $definition->setOnPage(99);
//
//    $source = new DataSource\Url($definition);
//
//    $cacheOptions = new DataSource\Options\Cache();
//    $cacheOptions->setCacheDir(__DIR__ . '/cache');
//    //    $cacheOptions->setExpirationTime(20);
//    $source = new DataSource\Cache($source, $cacheOptions);
//
//    $extractor = new Extractor\GoogleSearch($source);
//    $extraction = $extractor->extract();
//
//    $searchResults = $extraction->getSearchResults();
//    $it = new \ArrayIterator($searchResults);
//
//    while($it->valid())
//    {
//        /** @var $result \Sengin\Extraction\SearchResult */
//        $result = $it->current();
//
//        echo sprintf(
//            'pos:%s, url:%s, title: %s'."\n",
//            $result->getPosition(),
//            str_pad($result->getUrl(), 200),
//            $result->getTitle()
//        );
//
//        $it->next();
//    }
//}

namespace Sengin
{
    $definition = new Definition\GoogleSearch();
    $definition->setQuery('tłumacz języka angielskiego darmowy');
    $definition->setOnPage(10);

    $source = new DataSource\Url($definition);
//    echo $source->getCacheKey();
    $cacheOptions = new DataSource\Options\Cache();
    $cacheOptions->setCacheDir(__DIR__ . '/cache');
    $source = new DataSource\Cache($source, $cacheOptions);


    $extractor = new Extractor\GoogleSearch($source);
    $extraction = $extractor->extract();

    $searchResults = $extraction->getSuggestedKeywords();
    $it = new \ArrayIterator($searchResults);

    while($it->valid())
    {
        /** @var $result \Sengin\Extraction\SuggestedSearch */
        $result = $it->current();

        echo $result->getKeyword() . "\n";

        $it->next();
    }
}