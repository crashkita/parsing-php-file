<?php
namespace app\components\checkers;

/**
 * Class VisibilityChecker
 * @package app\components\checking
 */
class VisibilityChecker extends CheckingAbstract
{
    private $_visibility; // public|protection|private

    /**
     * Check correct token
     *
     * @param array $token Current token
     *
     * @return bool
     */
    public function check($token):bool
    {
        if (in_array($token[0], [T_PRIVATE, T_PUBLIC, T_PROTECTED])) {
            $this->_visibility = $token[1];
            return false;
        }

        $isVisibilityToken = !empty($this->_visibility)
            && in_array($token[0], [T_WHITESPACE, T_FUNCTION, T_VARIABLE, T_STATIC]);

        if (!$isVisibilityToken) {
            $this->_visibility = null;
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
        return $this->_visibility ?? 'public';
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_visibility = null;
    }
}