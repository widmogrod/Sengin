<?php

defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(__DIR__.'/../library/'));

set_include_path(implode(';', array(
    LIBRARY_PATH
)));

spl_autoload_register(function($className){
    require_once str_replace('\\','/', $className) . '.php';
});