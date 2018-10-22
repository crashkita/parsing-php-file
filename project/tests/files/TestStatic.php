<?php

namespace app\test;

/**
 * Class TestStatic
 * @package app\test
 */
class TestStatic
{
    protected static $variable = 2;

    const CONST_VAR = 's';

    public static function method1($var = self::CONST_VAR)
    {
        static $test;
        return $test;
    }

    public static function method2()
    {
        static $test2;
        return $test2;
    }

    public function method3()
    {
        static $test2;
        return $test2;
    }
}