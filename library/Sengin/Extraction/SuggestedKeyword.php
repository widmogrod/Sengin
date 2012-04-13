<?php
/**
 * @author gabriel
 */

namespace Sengin\Extraction;

class SuggestedKeyword
{
    protected $_keyword;

    public function setKeyword($keyword)
    {
        $this->_keyword = $keyword;
    }

    public function getKeyword()
    {
        return $this->_keyword;
    }
}