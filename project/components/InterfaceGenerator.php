<?php

namespace app\components;

/**
 * Class InterfaceGenerator
 * @package app\components
 */
class InterfaceGenerator implements IInterfaceGenerator
{
    /**
     * Current PhpLoader
     *
     * @var IPhpParser|IMethodParser
     */
    private $_parser;

    public $template = <<<EOD
<?php
{namespace}

{uses}

/**
 * Interface {name}
 * {package}
 */
interface {name}
{
   {methods}
}
EOD;
    /**
     * Array of uses class
     *
     * @var array
     */
    private $_uses = [];

    /**
     * InterfaceGenerator constructor.
     *
     * @param $parser IPhpParser PhpParser
     */
    public function __construct($parser)
    {
        $this->_parser = $parser;
    }

    /**
     * Generate content for php file
     *
     * @return string
     */
    public function getContentInterface(): string
    {
        if (empty($this->_parser->getInfoByName('class'))) {
            return '<?php';
        }

        $methods = $this->_getMethodsContent();

        if (empty($methods)) {
            return '<?php';
        }

        $interfaceName = $this->getInterfaceName();
        $namespace = $this->_parser->getInfoByName('namespace') ?? '';
        $uses = $this->_getUseContent();
        $params = [
            'namespace' => !empty($namespace) ? 'namespace ' . $namespace . ';' : '',
            'uses' => $uses,
            'name' => $interfaceName,
            'methods' => $methods,
            'package' => !empty($namespace) ? '@package ' . $namespace : '',
        ];

        $content = $this->_applyTemplateParams($params);
        $content = preg_replace('/[\n]{2,}/ui', "\n\n", $content);
        return $content;
    }

    /**
     * Generate interface name
     *
     * @return string
     */
    public function getInterfaceName(): string
    {
        return 'I' . trim($this->_parser->getInfoByName('class')) . 'Interface';
    }

    /**
     * Changed special variable in template
     *
     * @param $params
     *
     * @return mixed
     */
    private function _applyTemplateParams($params)
    {
        $templateParams = array_map(
            function ($param) {
                return '{' . $param . '}';
            },
            array_keys($params)
        );

        return str_replace($templateParams, array_values($params), $this->template);
    }

    /**
     * Return content with all methods
     *
     * @return string
     */
    private function _getMethodsContent(): string
    {
        $methodsContent = [];
        foreach ($this->getMethods() as $function) {
            $variables = [];
            if (!empty($function['variables'])) {
                foreach ($function['variables'] as $variable) {
                    $variableInfo = $this->parsingVariable($variable);

                    $variableContent = $this->_getVariableContent($variableInfo);
                    $variables[] = $variableContent;
                }
            }

            $methodContent = '    ' . $function['visibility'] . ' function ' . $function['name']
                . '(' . implode(', ', $variables) . ')';
            if (!empty($function['return'])) {
                $methodContent .= ':' . $function['return'];
            }
            $methodContent .= ';';
            $methodsContent[] = $methodContent;
        }

        return implode("\n", $methodsContent);
    }

    /**
     * Filter method from implements interface
     *
     * @return array
     */
    public function getImplementsMethods(): array
    {
        $methodNames = [];
        $implementClasses = $this->_parser->getInfoByName('implements');

        foreach ($implementClasses as $implementClass) {

            try {
                $useClass = $this->_findUse($implementClass);
                if (empty($useClass)) {
                    $className = $implementClass;
                } else {
                    $className = $useClass;
                }

                $reflection = new \ReflectionClass($className);
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $methodNames[] = $method->name;
                }
            } catch (\ReflectionException $exception) {
                // class does not exist in current system
            }
        }

        return $methodNames;
    }

    /**
     * Filter method by class
     *
     * @return mixed
     */
    public function getMethods(): array
    {
        $functions = [];
        $implementsMethods = $this->getImplementsMethods();
        foreach ($this->_parser->getInfoByName('methods') as $function) {
            if (in_array($function['name'], $implementsMethods)) {
                continue;
            }

            $excludeFunction = $function['visibility'] != 'public'
                || $function['static']
                || 0 === strpos($function['name'], '__');

            if ($excludeFunction) {
                continue;
            }
            $functions[$function['name']] = $function;
        }

        return $functions;
    }

    /**
     * Parsing string with variable for method
     *
     * @param  $variable
     * @return array
     */
    public function parsingVariable($variable): array
    {
        $patternClassName = '(?<className>[a-zA-Z\\0-9_\x7f-\xff]*)';
        $patternName = '(?<name>\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+)';
        $patternDefault = '(?<default>[a-zA-Z0-9_\x7f-\xff\(\)\[\]\.\'\:]*)';
        $patter = "/{$patternClassName}[\s]*{$patternName}[\s\=]*$patternDefault/um";
        preg_match($patter, $variable, $match);

        return [
            'class' => trim($match['className']),
            'name' => trim($match['name']),
            'default' => trim($match['default'])
        ];
    }

    /**
     * Find class name if exist in use classes
     *
     * @param $className
     *
     * @return string
     */
    private function _findUse($className): string
    {
        $useClasses = $this->_parser->getInfoByName('use');
        $pattern = '~' . preg_quote($className, null) . '$~i';
        if (!empty($useClasses)) {
            foreach ($useClasses as $use) {
                if (preg_match($pattern, $use)) {
                    return $use;
                }
            }
        }
        return '';
    }

    /**
     * Generate content for use block
     *
     * @return string
     */
    private function _getUseContent(): string
    {
        $usesClasses = $this->getUseClasses();
        $usesClassLines = [];
        foreach ($usesClasses as $usesClass) {
            $usesClassLines[] = 'use ' . $usesClass . ';';
        }
        return implode("\n", $usesClassLines);
    }

    /**
     * Return use classes
     *
     * @return array
     */
    public function getUseClasses(): array
    {
        return array_unique($this->_uses);
    }

    /**
     * Prepare variable information for render
     *
     * @param array $variableInfo
     *
     * @return string
     */
    private function _getVariableContent(array $variableInfo): string
    {
        $variableContent = '';
        if (!empty($variableInfo['className'])) {
            $variableContent .= $variableInfo['className'];

            $useClass = $this->_findUse($variableInfo['className']);
            if (!empty($useClass)) {
                $this->_uses[] = $useClass;
            }
        }

        $variableContent .= ' ' . $variableInfo['name'];

        if (!empty($variableInfo['default'])) {
            $variableInfo['default'] = $this->_checkDefaultConstant($variableInfo['default']);
            $variableContent .= ' = ' . $variableInfo['default'];
        }

        return trim($variableContent);
    }

    /**
     * Prepare variable content if default value contains class constant
     *
     * @param $default
     *
     * @return string
     */
    private function _checkDefaultConstant($default): string
    {
        $patternClassConstant = '/(?<className>[a-zA-Z\\0-9_\x7f-\xff]*)\:\:(?<constant>[a-zA-Z0-9_\x7f-\xff]*)/ui';
        if (preg_match($patternClassConstant, $default, $match)) {
            if (in_array($match['className'], ['static', 'self'])) {
                $parser = $this->_parser;
                $fullClassName = '\\' . $parser->getInfoByName('namespace')
                    . '\\' . $parser->getInfoByName('class');
                return $fullClassName . '::' . $match['constant'];
            }

            $useClass = $this->_findUse($match['className']);
            if (!empty($useClass)) {
                $this->_uses[] = $useClass;
            }
        }

        return $default;
    }
}