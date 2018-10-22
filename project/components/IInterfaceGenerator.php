<?php

namespace app\components;

/**
 * Interface IInterfaceGenerator
 *
 * @package app\components
 */
interface IInterfaceGenerator
{
    /**
     * Generate content for php file
     *
     * @return string
     */
    public function getContentInterface():string;

    /**
     * Generate interface name
     *
     * @return string
     */
    public function getInterfaceName():string;

    /**
     * Filter method by class
     *
     * @return mixed
     */
    public function getMethods();

    /**
     * Return use classes
     *
     * @return array
     */
    public function getUseClasses():array;
}