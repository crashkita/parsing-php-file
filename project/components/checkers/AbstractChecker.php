<?php

namespace app\components\checkers;

/**
 * Class AbstractChecker
 *
 * @package app\components\checking
 */
class AbstractChecker extends CheckingAbstract
{
    /**
     * Abstract information
     *
     * @var string
     */
    private $_abstract;

    /**
     * Check correct token
     *
     * @param array $token Current token
     *
     * @return bool
     */
    public function check($token): bool
    {
        if ($token[0] == T_ABSTRACT) {
            $this->_abstract = $token[1];
            return false;
        }

        $isAbstract = !empty($this->_abstract) && in_array($token[0], [T_WHITESPACE, T_FUNCTION, T_CLASS, T_PRIVATE, T_PUBLIC, T_PROTECTED]);

        if (!$isAbstract) {
            $this->_abstract = null;
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
        return $this->_abstract;
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_abstract = null;
    }
}