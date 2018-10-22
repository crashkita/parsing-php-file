<?php
namespace app\test;

/**
 * Class TestVisibility
 * @package app\test
 */
class TestVisibility
{
    protected static $variable = 2;

    const CONST_VAR = 's';

    public static function publicStaticMethod($var = self::CONST_VAR)
    {
        static $test;
        return $test;
    }

    private static function privateStaticMethod()
    {
        static $test2;
        return $test2;
    }

    protected static function protectedStaticMethod()
    {
        static $test2;
        return $test2;
    }

    public  function publicMethod($var = self::CONST_VAR)
    {
        $this->privateMethod();
        return 'test';
    }

    private function privateMethod()
    {
        static $test2;
        return $test2;
    }

    protected function protectedMethod()
    {
        self::privateStaticMethod();
        return null;
    }
}