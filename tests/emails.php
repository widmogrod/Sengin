<?php
namespace
{
    require_once (__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');
}

namespace Sengin
{
    $miasta = __DIR__ .'/resources/polish_cities.txt';
    $miasta = file($miasta);
    $miasta = array_map('trim', $miasta);
    $miasta = array_filter($miasta);
    
    //$miasta = array_slice($miasta, 328*2);

    $miasta = array('Żywiec');
    $baseKeyword = 'pozycjonowanie ';

    $emailFileStorage = sprintf('%s/email-%s-%s.csv', __DIR__, date('Ymd-His'), $baseKeyword);
    $handle = fopen($emailFileStorage, 'a');
    fputcsv($handle, array('city', 'email', 'title', 'url', 'position', 'facebook'));

    $cacheOptions = new DataSource\Options\Cache();
    $cacheOptions->setCacheDir(__DIR__ . '/cache');

//    register_shutdown_function(function() {
//        global $handle;
//        fclose($handle);
//    });

    foreach ($miasta as $i => $miasto)
    {
        $query = $baseKeyword .' '. $miasto .' +kontakt +email';

        echo "\ncity: $miasto ($i from ". count($miasta) .") query: $query \n";

        $definition = new Definition\GoogleSearch();
        $definition->setQuery($query);
        $definition->setOnPage(100);

        $source = new DataSource\Url($definition);
        $source = new DataSource\Cache($source, $cacheOptions);

        $extractor = new Extractor\GoogleSearch($source);
        try {
            $extraction = $extractor->extract();
        } catch (\Exception $e) {
            echo $e->getMessage() . ' '. $result->getUrl() . "\n";
            goto save;
        }

        $searchResults = $extraction->getSearchResults();
        $it = new \ArrayIterator($searchResults);

        $data = array();
        while($it->valid())
        {
            /** @var $result \Sengin\Extraction\SearchResult */
            $result = $it->current();

            $source = new DataSource\Url($result->getUrl());
            $source->setTimeout(5);
            $source = new DataSource\Cache($source, $cacheOptions);

            try {
                $content = $source->getData();
            } catch (\Exception $e) {
                echo $e->getMessage() . ' '. $result->getUrl() . "\n";
                goto next;
            }

            // in case when email adres is encoded by character entities
            $content = html_entity_decode($content);
            $content = strip_tags($content);

            preg_match_all(
                '#(\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b)#i',
                $content,
                $matches
            );

            preg_match_all(
//                '#((?:http:\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*\#\!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?)#i',
                '((?:https?://(?:\w+.)?)?facebook.\w+/[^\s">\'};]+)',
                $content,
                $matches2
            );

            $facebook = array();
            if (!empty($matches2) && !empty($matches2[0]))
            {
                $facebook = array_filter($matches2[0], function($value){

                    $result = true;
                    switch (true)
                    {
                        case false !== stripos($value, 'plugins/'): $result = false; break;
                        case false !== stripos($value, 'fbml'): $result = false; break;
                        case false !== stripos($value, 'sharer.php'): $result = false; break;
                        case false !== stripos($value, 'share.php'): $result = false; break;
                        case false !== stripos($value, 'campaign/'): $result = false; break;
                        case false !== stripos($value, 'all.js'): $result = false; break;
                        case false !== stripos($value, 'login.php'): $result = false; break;
                        case false !== stripos($value, '/picture'): $result = false; break;
                        case false !== stripos($value, 'acebook.com/js/'): $result = false; break;
                        case false !== stripos($value, 'facebook/assets/'): $result = false; break;
                        case false !== stripos($value, 'status/url'): $result = false; break;
                        case false !== stripos($value, 'facebook.pl'): $result = false; break;

                    }

                    return $result;
                });
            }

            if (!empty($matches) && !empty($matches[0]))
            {
                $emails = $matches[0];
                $emails = array_unique($emails);

                while ($email = array_pop($emails))
                {
                    if (!isset($data[$email])) {
                        $data[$email] = array();
                    }
                    $data[$email] = array(
                        'email' => $email,
                        'title' => $result->getTitle(),
                        'url' => $result->getUrl(),
                        'position' => $result->getPosition(),
                        'facebook' => implode(';', $facebook),
                    );
                }
            }

            next:

            unset($source);
            unset($result);

            $it->next();
        }

        save:

        foreach ($data as $info)
        {
            fputcsv($handle, array($miasto, $info['email'], $info['title'], $info['url'], $info['position'], $info['facebook']));
        }

        unset($data);
        unset($it);
    }

    fclose($handle);
}