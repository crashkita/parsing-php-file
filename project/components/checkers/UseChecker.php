<?php

namespace app\components\checkers;

/**
 * Class UseChecker
 * @package app\components\checkers
 */
class UseChecker extends CheckingAbstract
{
    /**
     * @var array
     */
    private $_useClasses;

    /**
     * @var bool
     */
    private $_notFinishedLine;

    /**
     * Check correct token
     *
     * @param array $token Current token
     *
     * @return bool
     */
    public function check($token): bool
    {
        if ($token[0] == T_USE) {
            $this->_useClasses[] = '';
            $this->_notFinishedLine = true;
            return true;
        }

        if ($this->_notFinishedLine && in_array($token[0], [T_WHITESPACE, T_STRING, T_NS_SEPARATOR])) {
            $this->_useClasses[count($this->_useClasses) - 1] .= trim($token[1]);

            return true;
        }

        $this->_notFinishedLine = false;

        return false;
    }

    /**
     * Get current information
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->_useClasses;
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_useClasses = [];
        $this->_notFinishedLine = false;
    }
}