<?php

namespace app\components\checkers;

use app\components\IPhpParser;

/**
 * Class CheckingAbstract
 * @package app\components\checking
 */
abstract class CheckingAbstract
{
    /**
     * @var IPhpParser
     */
    protected $_parser;

    /**
     * CheckingAbstract constructor.
     *
     * @param IPhpParser $parser
     */
    public function __construct(IPhpParser $parser)
    {
        $this->_parser = $parser;
    }

    /**
     * Check correct token
     *
     * @param array $token Current token
     *
     * @return bool
     */
    abstract public function check($token):bool;

    /**
     * Get current information
     *
     * @return mixed
     */
    abstract public function getInfo();

    /**
     * Set empty info
     */
    abstract public function setEmptyInfo();
}