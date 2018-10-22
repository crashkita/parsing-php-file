<?php
use app\components\InterfaceGenerator;
/**
 * Class InterfaceGeneratorTest
 */
class InterfaceGeneratorTest extends \PHPUnit\Framework\TestCase
{

    public function testParsingVariable()
    {
        $parser = $this->getMockBuilder(\app\components\PhpLoader::class)->setMethods(['getInfoByName'])->getMock();
        $use = [
            \PHPUnit\Framework\TestCase::class
        ];

        $phpInfo = [
            'use' => $use,
            'namespace' => '',
            'class' => 'ClassName'
        ];

        $parser->expects($this->any())
            ->method('getInfoByName')
            ->with($this->logicalOr(
                $this->equalTo('use'),
                $this->equalTo('namespace'),
                $this->equalTo('class')
            ))
            ->will($this->returnCallback(function ($type) use ($phpInfo) {
                return $phpInfo[$type] ?? '';
            }));

        $generator = new InterfaceGenerator($parser);
        $variableInfo = $generator->parsingVariable('$onlyParams = false');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$onlyParams', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('false', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$values');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$values', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('InputDefinition $definition');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('InputDefinition', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$definition', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$array = []');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$array', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('[]', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$array = array()');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$array', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('array()', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$string = \'qweqwe\'');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$string', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('\'qweqwe\'', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$int = 1');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$int', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('1', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('string $string = \'qwe\'');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('string', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$string', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('\'qwe\'', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$float = 2.2');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$float', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('2.2', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$null = null');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$null', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('null', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$definition = array()');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$definition', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('array()', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$var = self::CONST_VAL');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$var', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('self::CONST_VAL', $variableInfo['default']);

        $variableInfo = $generator->parsingVariable('$var = TestCase::CONST_VAL');
        $this->assertArrayHasKey('class', $variableInfo);
        $this->assertEquals('', $variableInfo['class']);
        $this->assertArrayHasKey('name', $variableInfo);
        $this->assertEquals('$var', $variableInfo['name']);
        $this->assertArrayHasKey('default', $variableInfo);
        $this->assertEquals('TestCase::CONST_VAL', $variableInfo['default']);
    }

    public function testMethods()
    {

        $methods = [
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => true,
                'name' => 'publicStaticMethod'
            ],
            [
                'visibility' => 'private',
                'abstract' => false,
                'static' => true,
                'name' => 'privateStaticMethod'
            ],
            [
                'visibility' => 'protected',
                'abstract' => false,
                'static' => true,
                'name' => 'protectedStaticMethod'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'publicMethod'
            ],
            [
                'visibility' => 'private',
                'abstract' => false,
                'static' => false,
                'name' => 'privateMethod'
            ],
            [
                'visibility' => 'protected',
                'abstract' => false,
                'static' => false,
                'name' => 'protectedMethod'
            ],

            [
                'visibility' => 'public',
                'abstract' => true,
                'static' => true,
                'name' => 'abstractPublicStaticMethod'
            ],
            [
                'visibility' => 'private',
                'abstract' => true,
                'static' => true,
                'name' => 'abstractPrivateStaticMethod'
            ],
            [
                'visibility' => 'protected',
                'abstract' => true,
                'static' => true,
                'name' => 'abstractProtectedStaticMethod'
            ],
            [
                'visibility' => 'public',
                'abstract' => true,
                'static' => false,
                'name' => 'abstractPublicMethod'
            ],
            [
                'visibility' => 'private',
                'abstract' => true,
                'static' => false,
                'name' => 'abstractPrivateMethod'
            ],
            [
                'visibility' => 'protected',
                'abstract' => true,
                'static' => false,
                'name' => 'abstractProtectedMethod'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => '__constructor'
            ],

            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'getAttributesByMethodName'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'isPublicMethod'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'getReturnTypeMethod'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'isStaticMethod'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'getClassInfo'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'getInfoByName'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'getInfo'
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'getFileContent'
            ],
        ];

        $phpInfo = [
            'methods' => $methods,
            'implements' => [
                'IPhpParser',
                '\app\components\IMethodParser'
            ],
            'use' => [
                'app\components\IPhpParser'
            ]
        ];

        $parser = $this->getMockBuilder(\app\components\PhpLoader::class)->setMethods(['getInfoByName'])->getMock();
        $parser->expects($this->any())
            ->method('getInfoByName')
            ->with($this->logicalOr(
                $this->equalTo('methods'),
                $this->equalTo('implements'),
                $this->equalTo('use')
            ))
            ->will($this->returnCallback(function ($type) use ($phpInfo) {
                return $phpInfo[$type] ?? '';
            }));

        $generator = new InterfaceGenerator($parser);
        $generateMethods = $generator->getMethods();
        $this->assertNotEmpty($generateMethods);
        $this->assertArrayNotHasKey('publicStaticMethod', $generateMethods);
        $this->assertArrayNotHasKey('privateStaticMethod', $generateMethods);
        $this->assertArrayNotHasKey('protectedStaticMethod', $generateMethods);
        $this->assertArrayHasKey('publicMethod', $generateMethods);
        $this->assertArrayNotHasKey('privateMethod', $generateMethods);
        $this->assertArrayNotHasKey('protectedMethod', $generateMethods);
        $this->assertArrayNotHasKey('abstractPublicStaticMethod', $generateMethods);
        $this->assertArrayNotHasKey('abstractPrivateStaticMethod', $generateMethods);
        $this->assertArrayNotHasKey('abstractProtectedStaticMethod', $generateMethods);
        $this->assertArrayHasKey('abstractPublicMethod', $generateMethods);
        $this->assertArrayNotHasKey('abstractPrivateMethod', $generateMethods);
        $this->assertArrayNotHasKey('abstractProtectedMethod', $generateMethods);
        $this->assertArrayNotHasKey('__constructor', $generateMethods);

        $implementsMethods = $generator->getImplementsMethods();
        $this->assertNotEmpty($implementsMethods);
        $this->assertContains('getAttributesByMethodName', $implementsMethods);
        $this->assertContains('isPublicMethod', $implementsMethods);
        $this->assertContains('getReturnTypeMethod', $implementsMethods);
        $this->assertContains('isStaticMethod', $implementsMethods);

        $this->assertContains('getClassInfo', $implementsMethods);
        $this->assertContains('getInfoByName', $implementsMethods);
        $this->assertContains('getInfo', $implementsMethods);
        $this->assertContains('getFileContent', $implementsMethods);

        $this->assertArrayNotHasKey('getFileContent', $generateMethods);
        $this->assertArrayNotHasKey('getInfo', $generateMethods);
        $this->assertArrayNotHasKey('getInfoByName', $generateMethods);
        $this->assertArrayNotHasKey('getClassInfo', $generateMethods);
        $this->assertArrayNotHasKey('isStaticMethod', $generateMethods);
        $this->assertArrayNotHasKey('getReturnTypeMethod', $generateMethods);
        $this->assertArrayNotHasKey('isPublicMethod', $generateMethods);
        $this->assertArrayNotHasKey('getAttributesByMethodName', $generateMethods);
    }

    public function testInterfaceName()
    {
        $parser = $this->getMockBuilder(\app\components\PhpLoader::class)->setMethods(['getInfoByName'])->getMock();
        $parser->expects($this->any())
            ->method('getInfoByName')
            ->with($this->equalTo('class'))
            ->will($this->returnValue('ClassName'));

        $generator = new InterfaceGenerator($parser);
        $className = $generator->getInterfaceName();
        $this->assertNotEmpty($className);
        $this->assertEquals('IClassNameInterface', $className);
    }

    public function testUseClasses()
    {
        $parser = $this->getMockBuilder(\app\components\PhpLoader::class)->setMethods(['getInfoByName'])->getMock();
        $useClasses = [
            'app\components\EmptyClass'
        ];

        $phpInfo = [
            'namespace' => '',
            'use' => $useClasses,
            'class' => 'ClassName'
        ];

        $parser->expects($this->any())
            ->method('getInfoByName')
            ->with($this->logicalOr(
                $this->equalTo('use'),
                $this->equalTo('namespace'),
                $this->equalTo('class')
            ))
            ->will($this->returnCallback(function ($type) use ($phpInfo) {
                return $phpInfo[$type] ?? '';
            }));

        $generator = new InterfaceGenerator($parser);
        $variableInfo = [
            'name' => '$var',
            'className' => '\Symfony\Component\Console\Input\InputDefinition',
            'default' => ''
        ];
        $this->invokeMethod($generator, '_getVariableContent', [$variableInfo]);

        $variableInfo = [
            'name' => '$var',
            'className' => '',
            'default' => 'self::CONSTANT_NAME'
        ];
        $this->invokeMethod($generator, '_getVariableContent', [$variableInfo]);
        $useClasses = $generator->getUseClasses();
        $this->assertEmpty($useClasses);

        $variableInfo = [
            'name' => '$var',
            'className' => 'EmptyClass',
            'default' => ''
        ];
        $this->invokeMethod($generator, '_getVariableContent', [$variableInfo]);
        $useClasses = $generator->getUseClasses();
        $this->assertNotEmpty($useClasses);
        $this->assertContains('app\components\EmptyClass', $useClasses);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testEmptyFile()
    {
        $parser = $this->getMockBuilder(\app\components\PhpLoader::class)->setMethods(['getInfoByName'])->getMock();

        $parser->expects($this->any())
            ->method('getInfoByName')
            ->with($this->equalTo('class'))
            ->will($this->returnValue(''));

        $generator = new InterfaceGenerator($parser);
        $content = $generator->getContentInterface();
        $this->assertEquals('<?php', $content);
    }

    public function testEmptyClass()
    {
        $parser = $this->getMockBuilder(\app\components\PhpLoader::class)->setMethods(['getInfoByName'])->getMock();

        $phpInfo = [
            'class' => 'ClassName',
            'methods' => [],
            'implements' => []
        ];
        $parser->expects($this->any())
            ->method('getInfoByName')
            ->with($this->logicalOr(
                $this->equalTo('class'),
                $this->equalTo('methods'),
                $this->equalTo('implements')
            ))
            ->will($this->returnCallback(function ($type) use ($phpInfo){
                return $phpInfo[$type] ?? '';
            }));

        $generator = new InterfaceGenerator($parser);
        $content = $generator->getContentInterface();
        $this->assertEquals('<?php', $content);
    }

    public function testClassWithPrivateFunctions()
    {
        $parser = $this->getMockBuilder(\app\components\PhpLoader::class)->setMethods(['getInfoByName'])->getMock();

        $methods = [
            [
                'visibility' => 'private',
                'abstract' => false,
                'static' => false,
                'name' => 'getInfoByName'
            ],
            [
                'visibility' => 'protected',
                'abstract' => false,
                'static' => false,
                'name' => 'getInfoByName2'
            ]
        ];

        $phpInfo = [
            'class' => 'ClassName',
            'methods' => $methods,
            'implements' => []
        ];
        $parser->expects($this->any())
            ->method('getInfoByName')
            ->with($this->logicalOr(
                $this->equalTo('class'),
                $this->equalTo('methods'),
                $this->equalTo('implements')
            ))
            ->will($this->returnCallback(function ($type) use ($phpInfo){
                return $phpInfo[$type] ?? '';
            }));

        $generator = new InterfaceGenerator($parser);
        $content = $generator->getContentInterface();
        $this->assertEquals('<?php', $content);
    }

    public function testCorrectClass()
    {
        $parser = $this->getMockBuilder(\app\components\PhpLoader::class)->setMethods(['getInfoByName'])->getMock();

        $methods = [
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'getInfoByName',
                'variables' => [
                    'string $string = \'qwe\'',
                    '$array = array()'
                ]
            ],
            [
                'visibility' => 'public',
                'abstract' => false,
                'static' => false,
                'name' => 'getInfoByName2',
                'variables' => [
                    '\Symfony\Component\Console\Input\InputDefinition $definition'
                ]
            ]
        ];

        $phpInfo = [
            'class' => 'ClassName',
            'methods' => $methods,
            'implements' => [
                'IPhpParser'
            ],
            'use' => [
                'app\components\IPhpParser'
            ],
            'namespace' => 'app\components'
        ];
        $parser->expects($this->any())
            ->method('getInfoByName')
            ->with($this->logicalOr(
                $this->equalTo('class'),
                $this->equalTo('methods'),
                $this->equalTo('implements'),
                $this->equalTo('use'),
                $this->equalTo('namespace')
            ))
            ->will($this->returnCallback(function ($type) use ($phpInfo){
                return $phpInfo[$type] ?? '';
            }));

        $generator = new InterfaceGenerator($parser);
        $content = $generator->getContentInterface();

        $expectedContent = <<<EOD
<?php
namespace app\components;

/**
 * Interface IClassNameInterface
 * @package app\components
 */
interface IClassNameInterface
{
       public function getInfoByName2(\$definition);
}
EOD;
        $this->assertEquals($expectedContent,$content);
    }
}