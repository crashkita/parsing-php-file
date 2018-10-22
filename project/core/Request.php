<?php

namespace app\core;

/**
 * Class Request
 * @package app\core
 */
class Request
{
    /**
     * @var string PathInfo from request url
     */
    private $_pathInfo;

    /**
     * @return string
     */
    public function getPathInfo(): string
    {
        if (empty($this->_pathInfo)) {
            $pathInfo = $_SERVER['REQUEST_URI'];
            if (($pos = strpos($pathInfo, '?')) !== false) {
                $pathInfo = substr($pathInfo, 0, $pos);
            }

            $this->_pathInfo = urldecode($pathInfo);
        }

        return $this->_pathInfo;
    }

    /**
     * @param $name
     * @return null|array
     */
    public function getFileInfoByName($name)
    {
        if (isset($_FILES[$name])) {
            return $_FILES[$name];
        }
        return null;
    }
}