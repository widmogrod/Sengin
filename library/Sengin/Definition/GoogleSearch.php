<?php

namespace Sengin\Definition;

use Sengin\Definition;

class GoogleSearch implements Definition
{
    protected $_query;

    public function setQuery($query)
    {
        $this->_query = (string) $query;
    }

}