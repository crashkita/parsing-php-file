<?php

namespace app\components\checkers;

/**
 * Class MethodChecker
 * @package app\components\checkers
 */
class MethodChecker extends CheckingAbstract
{
    /**
     * @var array Methods information
     */
    private $_methods = [];

    /**
     * @var bool if information about current method not ending
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
        if ($token[0] == T_FUNCTION) {
            $this->_methods[] = [
                'visibility' => $this->_parser->getInfoByName('visibility'),
                'abstract' => $this->_parser->getInfoByName('abstract'),
                'static' => $this->_parser->getInfoByName('static')
            ];
            $this->_notFinishedLine = true;
            return true;
        }

        if (!$this->_notFinishedLine) {
            return false;
        }

        $lastMethodIndex = count($this->_methods) - 1;
        $needSetMethodName = $this->_notFinishedLine
            && empty($this->_methods[$lastMethodIndex]['name'])
            && in_array($token[0], [T_WHITESPACE, T_STRING]);

        if ($needSetMethodName) {
            $this->_methods[$lastMethodIndex]['name'] = trim($token[1]);
            return true;
        }

        $isVariable = $this->_notFinishedLine &&
            in_array($token[0],
                [T_WHITESPACE, T_STRING, T_VARIABLE, T_NS_SEPARATOR,
                T_CONSTANT_ENCAPSED_STRING, T_LNUMBER, T_DNUMBER]
            );

        if (!$isVariable) {
            $methodName = $this->_methods[$lastMethodIndex]['name'];
            $this->setVariables($methodName, $lastMethodIndex);

            $this->_notFinishedLine = false;
        }

        return $isVariable;
    }

    /**
     * Get current information
     *
     * @return mixed
     */
    public function getInfo()
    {
        $lastMethodIndex = count($this->_methods) - 1;
        if (!isset($this->_methods[$lastMethodIndex]['return']) && !empty($this->_methods[$lastMethodIndex]['name'])) {
            $this->setVariables($this->_methods[$lastMethodIndex]['name'], $lastMethodIndex);
        }
        return $this->_methods;
    }

    /**
     * Set empty info
     */
    public function setEmptyInfo()
    {
        $this->_methods = [];
        $this->_notFinishedLine = false;
    }

    /**
     * Get attributes and return type from content file
     * @param $methodName
     * @param $methodIndex
     */
    protected function setVariables($methodName, $methodIndex)
    {
        $pattern = "~{$methodName}[\s]*\(([a-z\\\A-Z\:_\x7f-\xff0-9\$\s\(\)\[\]\.\=\'\,]*)\)[\n\s\:]+([a-z\\\A-Z0-9_\x7f-\xff]*)~m";
        preg_match($pattern, $this->_parser->getFileContent(), $match);

        $variables = [];
        if (!empty($match[1])) {
            $variables = explode(',', $match[1]);
            $variables = array_filter(array_map('trim', $variables));
        }

        $return = '';
        if (!empty($match[2])) {
            $return = trim($match[2]);
        }
        $this->_methods[$methodIndex]['variables'] = $variables;
        $this->_methods[$methodIndex]['return'] = $return;
    }
}