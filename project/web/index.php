<?php
error_reporting ( E_ALL ^ E_NOTICE );
ini_set ( 'display_errors', true );
ini_set ( 'html_errors', false );
ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );
include_once __DIR__ . '/../core/autoload.php';

$app = \app\core\App::getInstance();
$request = new \app\core\Request();
$app->setRequest($request);
echo $app->runAction($request->getPathInfo());
