<?php

namespace app\components;

use app\components\checkers\AbstractChecker;
use app\components\checkers\CheckingAbstract;
use app\components\checkers\ClassChecker;
use app\components\checkers\ExtendChecker;
use app\components\checkers\MethodChecker;
use app\components\checkers\ImplementsChecker;
use app\components\checkers\NamespaceChecker;
use app\components\checkers\StaticChecker;
use app\components\checkers\UseChecker;
use app\components\checkers\VisibilityChecker;

/**
 * Class PhpLoader
 * @package app\components
 */
class PhpLoader implements IPhpParser, IMethodParser
{
    /**
     * @var string File content
     */
    private $_content = '';

    /**
     * Checkers
     *
     * @var array
     */
    private $_checking = [];

    /**
     * PhpLoader constructor.
     */
    public function __construct()
    {
        $this->_checking['visibility'] = new VisibilityChecker($this);
        $this->_checking['abstract'] = new AbstractChecker($this);
        $this->_checking['static'] = new StaticChecker($this);
        $this->_checking['namespace'] = new NamespaceChecker($this);
        $this->_checking['use'] = new UseChecker($this);
        $this->_checking['class'] = new ClassChecker($this);
        $this->_checking['extend'] = new ExtendChecker($this);
        $this->_checking['implements'] = new ImplementsChecker($this);
        $this->_checking['methods'] = new MethodChecker($this);
    }

    /**
     * Parsing file and return tokens
     *
     * @param $filePath
     * @return array
     */
    public function getTokens($filePath): array
    {
        $this->_content = '';
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $this->_content = $content;
            return token_get_all($content);
        }

        return [];
    }

    /**
     * set default value for checkers
     */
    public function setDefault()
    {
        foreach ($this->_checking as $key => $checking) {
            /** @var $checking AbstractChecker */
            $checking->setEmptyInfo();
        }
    }

    /**
     * Parsing all tokens and return all information about class
     *
     * @param $tokens
     * @return array
     */
    public function parsingTokens($tokens): array
    {
        $this->setDefault();

        foreach ($tokens as $token) {
            if (is_array($token)) {
                $this->_check($token);
            }
        }
        return $this->getInfo();
    }

    /**
     * Get current information by class
     *
     * @return array
     */
    public function getInfo(): array
    {
        $info = [];
        $notInformation = ['abstract', 'static', 'visibility'];
        foreach ($this->_checking as $key => $checker) {
            if (!in_array($key, $notInformation)) {
                /* @var $checker CheckingAbstract */
                $info[$key] = $checker->getInfo();
            }
        }
        return array_filter($info);
    }

    /**
     * Get information by type
     *
     * @param $name
     * @return mixed|null
     */
    public function getInfoByName($name)
    {
        if (isset($this->_checking[$name])) {
            /* @var $checker CheckingAbstract */
            $checker = $this->_checking[$name];
            return $checker->getInfo();
        }
        return null;
    }

    /**
     * Check current token
     *
     * @param $token
     */
    private function _check($token)
    {
        foreach ($this->_checking as $key => $checker) {
            /* @var $checker CheckingAbstract */
            if ($checker->check($token)) {
                return;
            }
        }
    }

    /**
     * Get all class information
     *
     * @param $filePath
     * @return array
     */
    public function getClassInfo($filePath): array
    {
        return $this->parsingTokens($this->getTokens($filePath));
    }

    /**
     * Get attribute by method name
     *
     * @param $methodName
     * @return array
     */
    public function getAttributesByMethodName($methodName): array
    {
        $methods = $this->getInfoByName('methods');
        if (empty($methods)) {
            return [];
        }

        foreach ($methods as $function) {
            if ($function['name'] == $methodName) {
                return $function['variables'];
            }
        }

        return [];
    }

    /**
     * Return true if method is public
     *
     * @param $methodName
     * @return bool
     */
    public function isPublicMethod($methodName): bool
    {
        $methods = $this->getInfoByName('methods');
        if (empty($methods)) {
            return false;
        }

        foreach ($methods as $function) {
            if ($function['name'] == $methodName) {
                return $function['visibility'] == 'public';
            }
        }
        return false;
    }

    /**
     * Get type of return value
     *
     * @param $methodName
     * @return string
     */
    public function getReturnTypeMethod($methodName): string
    {
        $methods = $this->getInfoByName('methods');
        if (empty($methods)) {
            return '';
        }

        foreach ($methods as $function) {
            if ($function['name'] == $methodName) {
                return $function['return'];
            }
        }
        return '';
    }

    /**
     * Get current file content
     *
     * @return string
     */
    public function getFileContent(): string
    {
        return $this->_content;
    }

    /**
     * Return true if method is static
     *
     * @param $methodName
     * @return bool
     */
    public function isStaticMethod($methodName): bool
    {
        $methods = $this->getInfoByName('methods');
        if (empty($methods)) {
            return false;
        }

        foreach ($methods as $function) {
            if ($function['name'] == $methodName) {
                return $function['static'];
            }
        }
        return false;
    }
}