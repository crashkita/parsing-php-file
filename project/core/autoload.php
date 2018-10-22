<?php
require_once __DIR__ . '/../vendor/autoload.php';
function my_autoloader($class)
{
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (strpos($class, 'app\\') === 0) {
        $relativePath =str_replace('\\', DIRECTORY_SEPARATOR, preg_replace('~^app\\\~ui', '', $class));
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . $relativePath . '.php';
    }

    if (file_exists($filePath)) {
        include_once $filePath;
    }
}
spl_autoload_register('my_autoloader');