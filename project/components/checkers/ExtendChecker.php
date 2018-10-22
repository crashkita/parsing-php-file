<?php

namespace app\components\checkers;

/**
 * Class ExtendChecker
 * @package app\components\checkers
 */
class ExtendChecker extends CheckingAbstract
{
    private $_extend;

    /**
     * @var bool true if information not finished
     */
    private $_notFinished;

    /**
     * Check correct token
     *
     * @param array $token Current token
     *
     * @return bool
     */
    public function check($token): bool
    {
        if ($token[0] == T_EXTENDS) {
            $this->_extend = '';
            $this->_notFinished = true;
            return true;
        }

        if ($this->_notFinished && in_array($token[0], [T_WHITESPACE, T_STRING, T_NS_SEPARATOR])) {
            $this->_extend .= trim($token[1]);

            return true;
        }

        $this->_notFinished = false;

        return false;
    }

    /**
     * Get current information
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->_extend;
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_extend = null;
        $this->_notFinished = false;
    }
}