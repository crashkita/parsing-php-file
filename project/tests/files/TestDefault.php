<?php
namespace app\user;

use PHPUnit\Framework\TestCase;

/**
 * Class TestUse
 * @package app\user
 */
class TestDefault
{
    const CONST_VAL = 'val';

    public function arrayFunction($array = [])
    {
        return $array;
    }

    public function array2Function($array = array())
    {
        return $array;
    }

    public function stringFunction($string = 'qweqwe')
    {
        return $string;
    }

    public function intFunction($int = 1)
    {
        return $int;
    }

    public function typeFunction(string $string = 'qwe')
    {
        return $string;
    }

    public function floatFunction($float = 2.2)
    {
        return $float;
    }

    public function nullFunction($null = null)
    {
        return $null;
    }

    public function bind($definition = array() ) {
        // TODO: Implement bind() method.
    }

    public function constFunction($var = self::CONST_VAL):string
    {
        return self::class;
    }
}