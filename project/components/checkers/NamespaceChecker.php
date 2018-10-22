<?php

namespace app\components\checkers;

/**
 * Class NamespaceChecker
 * @package app\components\checkers
 */
class NamespaceChecker extends CheckingAbstract
{
    private $_namespace;

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
        if ($token[0] == T_NAMESPACE) {
            $this->_namespace = '';
            $this->_notFinished = true;
            return true;
        }

        if ($this->_notFinished && in_array($token[0], [T_WHITESPACE, T_STRING, T_NS_SEPARATOR])) {
            $this->_namespace .= trim($token[1]);

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
        return $this->_namespace;
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_namespace = null;
        $this->_notFinished = false;
    }
}