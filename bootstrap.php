<?php


/*
function __autoload($class_name) {
    include $class_name . '.php';
}
*/

include_once('AutoLoader.php');
// Register the directory to your include files
AutoLoader::registerDirectory('./src');
