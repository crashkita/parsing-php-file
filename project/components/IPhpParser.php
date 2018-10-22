<?php

namespace app\components;

/**
 * Interface IPhpParser
 * @package app\components
 */
interface IPhpParser
{
    /**
     * Get all class information
     *
     * @param $filePath
     * @return array
     */
    public function getClassInfo($filePath):array;

    /**
     * Get information for current class by type
     *
     * @param $name
     * @return mixed|null
     */
    public function getInfoByName($name);

    /**
     * Return current information class
     *
     * @return array
     */
    public function getInfo():array;

    /**
     * Get current file content
     *
     * @return string
     */
    public function getFileContent():string;

}