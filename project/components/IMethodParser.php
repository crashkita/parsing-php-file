<?php

namespace app\components;

/**
 * Interface IMethodParser
 * @package app\components
 */
interface IMethodParser
{
    /**
     * Get attribute by method name
     *
     * @param $methodName
     * @return array
     */
    public function getAttributesByMethodName($methodName):array;

    /**
     * Return true if method is public
     *
     * @param $methodName
     * @return bool
     */
    public function isPublicMethod($methodName):bool;

    /**
     * Get type of return value
     *
     * @param $methodName
     * @return string
     */
    public function getReturnTypeMethod($methodName):string;

    /**
     * Return true if method is static
     *
     * @param $methodName
     * @return bool
     */
    public function isStaticMethod($methodName):bool;
}