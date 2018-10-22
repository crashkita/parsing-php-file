<?php

namespace app\components\checkers;

/**
 * Class ImplementsChecker
 * @package app\components\checkers
 */
class ImplementsChecker extends CheckingAbstract
{
    /**
     * @var array
     */
    private $_implements = [];

    /**
     * @var bool not finished implement interface name
     */
    private $_notFinishedName;

    /**
     * Check correct token
     *
     * @param array $token Current token
     *
     * @return bool
     */
    public function check($token): bool
    {
        if ($token[0] == T_IMPLEMENTS) {
            $this->_implements = [''];
            $this->_notFinishedName = true;
            return true;
        }

        if ($this->_notFinishedName && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {
            $lastImplementIndex = count($this->_implements) - 1;
            $this->_implements[$lastImplementIndex] .= $token[1];

            return true;
        }

        if ($this->_notFinishedName && $token[0] == T_WHITESPACE) {
            $this->_implements[] = '';
            return true;
        }

        if ($this->_notFinishedName) {
            $implements = array_filter(array_map('trim', $this->_implements));
            $this->_implements = $implements;
            $this->_notFinishedName = false;
        }

        return false;
    }

    /**
     * Get current information
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->_implements;
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_implements = [];
        $this->_notFinishedName = false;
    }
}