<?php

namespace app\components\checkers;

/**
 * Class ClassChecker
 * @package app\components\checkers
 */
class ClassChecker extends CheckingAbstract
{
    /**
     * @var string  Class name
     */
    private $_class;

    /**
     * @var bool
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
        if ($token[0] == T_CLASS) {
            $this->_class = '';
            $this->_notFinished = true;
            return true;
        }

        if ($this->_notFinished && in_array($token[0], [T_WHITESPACE, T_STRING])) {
            $this->_class .= trim($token[1]);

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
        return $this->_class;
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_class = null;
        $this->_notFinished = false;
    }
}