<?php

namespace Sengin\DataSource;

use Sengin\DataSource as DataSourceInterface;
use Sengin\DataSource\Options\Cache as CacheOptions;

class Cache implements DataSourceInterface
{
    /**
     * @var \Sengin\DataSource
     */
    protected $_dataSource;

    /**
     * @var Options\Cache
     */
    protected $_options;

    public function __construct(DataSourceInterface $dataSource, CacheOptions $options)
    {
        $this->_dataSource = $dataSource;
        $this->_options = $options;
    }

    public function getDataSource()
    {
        return $this->_dataSource;
    }

    /**
     * @return Options\Cache
     */
    public function getOptions()
    {
        return $this->_options;
    }

    public function getData()
    {
        if (!$this->_options->isCacheEnabled()
            || !($this->_dataSource instanceof Cachable))
        {
            return $this->_dataSource->getData();
        }

        $cacheDir = $this->_options->getCacheDir();
        $cacheKey = $this->_dataSource->getCacheKey();
        $cacheFile = $cacheDir .'/'. $cacheKey . '.sengincache';

        if (is_file($cacheFile))
        {
            $expirationTime = $this->_options->getExpirationTime();
            $lastModificationTime = time() - filemtime($cacheFile);
            if ($lastModificationTime <= $expirationTime) {
                // from cache
                return file_get_contents($cacheFile);
            }

            // cache expired, remove cache data
            unlink($cacheFile);
        }

        $data = $this->_dataSource->getData();

        // save cache data
        file_put_contents($cacheFile, $data);

        return $data;
    }
}