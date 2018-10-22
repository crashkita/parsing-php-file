<?php

namespace app\components\checkers;

/**
 * Class StaticChecker
 * @package app\components\checkers
 */
class StaticChecker extends CheckingAbstract
{
    /**
     * @var string
     */
    private $_static;

    /**
     * Check correct token
     *
     * @param array $token Current token
     *
     * @return bool
     */
    public function check($token): bool
    {
        if ($token[0] == T_STATIC) {
            $this->_static = true;
            return false;
        }

        $isStaticToken = !empty($this->_static) && in_array($token[0], [T_WHITESPACE, T_FUNCTION, T_VARIABLE]);

        if (!$isStaticToken) {
            $this->_static = false;
        }

        return false;
    }

    /**
     * Get current information
     *
     * @return mixed
     */
    function getInfo()
    {
        return $this->_static;
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_static = false;
    }
}