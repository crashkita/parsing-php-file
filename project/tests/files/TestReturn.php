<?php

namespace app\test;


use app\components\checkers\CheckingAbstract;

class TestReturn
{
    public function constFunction($var = self::CONST_VAL):string
    {
        return self::class;
    }

    public function classFunction($var = self::CONST_VAL):CheckingAbstract
    {
        return self::class;
    }

    public function namespaceFunction($var = self::CONST_VAL):Prophecy\Doubler\CachedDoubler
    {
        return self::class;
    }

    public function emptyFunction()
    {

    }
}