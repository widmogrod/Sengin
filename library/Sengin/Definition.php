<?php

namespace Sengin;

interface Definition
{
    public function setQuery($query);

    public function getUrl();

    public function getDomain();
}