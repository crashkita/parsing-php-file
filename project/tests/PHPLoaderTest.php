<?php

use Prophecy\Doubler\CachedDoubler;
use SebastianBergmann\CodeCoverage\Report\PHP;
use PHPUnit\Framework\TestCase;
use app\components\PhpLoader;

/**
 * Testing PhpLoader class
 * Class PHPLoaderTest
 */
class PHPLoaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PhpLoader
     */
    protected $loader;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->loader = new PhpLoader();
    }

    /**
     * Test for generate tokens
     */
    public function testNotEmpty()
    {
        $tokens = $this->loader->getTokens($this->getFilePath('TestClass.php'));
        $this->assertNotEmpty($tokens);
    }

    /**
     * Test parsing
     */
    public function testGlobalParsing()
    {
        $phpInfo = $this->loader->getClassInfo($this->getFilePath('TestClass.php'));
        $this->assertNotEmpty($phpInfo);

        $this->assertArrayHasKey('namespace', $phpInfo);
        $this->assertNotEmpty($phpInfo['namespace']);
        $this->assertEquals('app\test', $phpInfo['namespace']);

        $this->assertArrayHasKey('use', $phpInfo);
        $this->assertNotEmpty($phpInfo['use']);
        $this->assertContains(TestCase::class, $phpInfo['use']);
        $this->assertContains(PHP::class, $phpInfo['use']);

        $this->assertArrayHasKey('class', $phpInfo);
        $this->assertNotEmpty($phpInfo['class']);
        $this->assertEquals('TestClass', $phpInfo['class']);

        $this->assertArrayHasKey('extend', $phpInfo);
        $this->assertNotEmpty($phpInfo['extend']);
        $this->assertEquals('TestCase', $phpInfo['extend']);

        $this->assertArrayHasKey('implements', $phpInfo);
        $this->assertNotEmpty($phpInfo['implements']);
        $this->assertContains('IncompleteTest', $phpInfo['implements']);
        $this->assertContains('InputInterface', $phpInfo['implements']);

        $this->assertArrayHasKey('methods', $phpInfo);
        $this->assertNotEmpty($phpInfo['methods']);
        $this->assertCount(15, $phpInfo['methods']);

        $phpInfo = $this->loader->getClassInfo($this->getFilePath('EmptyClass2.php'));
        $this->assertEmpty($phpInfo);
        $this->assertEmpty($this->loader->getFileContent());

        $this->assertEmpty($this->loader->getInfoByName('test'));
    }

    public function testNamespace()
    {
        $phpInfo = $this->loader->getClassInfo($this->getFilePath('TestNamespace.php'));
        $this->assertNotEmpty($phpInfo);

        $this->assertArrayHasKey('extend', $phpInfo);
        $this->assertNotEmpty($phpInfo['extend']);
        $this->assertEquals('\PHPUnit\Framework\TestCase', $phpInfo['extend']);

        $extend = $this->loader->getInfoByName('extend');
        $this->assertNotEmpty($extend);
        $this->assertEquals('\PHPUnit\Framework\TestCase', $extend);

        $this->assertArrayHasKey('implements', $phpInfo);
        $this->assertNotEmpty($phpInfo['implements']);
        $this->assertContains('\Symfony\Component\Console\Input\InputInterface', $phpInfo['implements']);
        $this->assertContains('\PHPUnit\Framework\IncompleteTest', $phpInfo['implements']);

        $implements = $this->loader->getInfoByName('implements');
        $this->assertNotEmpty($implements);
        $this->assertContains('\Symfony\Component\Console\Input\InputInterface', $implements);
        $this->assertContains('\PHPUnit\Framework\IncompleteTest', $implements);

        $attributes = $this->loader->getAttributesByMethodName('bind');
        $this->assertNotEmpty($attributes);
        $this->assertContains('\Symfony\Component\Console\Input\InputDefinition $definition', $attributes);

        $phpInfo = $this->loader->getClassInfo($this->getFilePath('EmptyClass.php'));
        $this->assertNotEmpty($phpInfo);

        $this->assertArrayNotHasKey('extend', $phpInfo);

        $extend = $this->loader->getInfoByName('extend');
        $this->assertEmpty($extend);
        $this->assertEquals('', $extend);

        $this->assertArrayNotHasKey('implements', $phpInfo);

        $extend = $this->loader->getInfoByName('implements');
        $this->assertEmpty($extend);
        $this->assertEquals([], $extend);

        $attributes = $this->loader->getAttributesByMethodName('getFirstArgument');
        $this->assertEmpty($attributes);

        $this->assertArrayNotHasKey('namespace', $phpInfo);

        $namespace = $this->loader->getInfoByName('namespace');
        $this->assertEmpty($namespace);
        $this->assertEquals('', $namespace);
    }

    public function testDefaultValue()
    {
        $phpInfo = $this->loader->getClassInfo($this->getFilePath('TestDefault.php'));
        $this->assertNotEmpty($phpInfo);

        $attributes = $this->loader->getAttributesByMethodName('arrayFunction');
        $this->assertNotEmpty($attributes);
        $this->assertContains('$array = []', $attributes);

        $attributes = $this->loader->getAttributesByMethodName('array2Function');
        $this->assertNotEmpty($attributes);
        $this->assertContains('$array = array()', $attributes);

        $attributes = $this->loader->getAttributesByMethodName('stringFunction');
        $this->assertNotEmpty($attributes);
        $this->assertContains('$string = \'qweqwe\'', $attributes);

        $attributes = $this->loader->getAttributesByMethodName('intFunction');
        $this->assertNotEmpty($attributes);
        $this->assertContains('$int = 1', $attributes);

        $attributes = $this->loader->getAttributesByMethodName('typeFunction');
        $this->assertNotEmpty($attributes);
        $this->assertContains('string $string = \'qwe\'', $attributes);

        $attributes = $this->loader->getAttributesByMethodName('floatFunction');
        $this->assertNotEmpty($attributes);
        $this->assertContains('$float = 2.2', $attributes);

        $attributes = $this->loader->getAttributesByMethodName('nullFunction');
        $this->assertNotEmpty($attributes);
        $this->assertContains('$null = null', $attributes);

        $attributes = $this->loader->getAttributesByMethodName('bind');
        $this->assertNotEmpty($attributes);
        $this->assertContains('$definition = array()', $attributes);

        $attributes = $this->loader->getAttributesByMethodName('constFunction');
        $this->assertNotEmpty($attributes);
        $this->assertContains('$var = self::CONST_VAL', $attributes);

        $this->assertEmpty($this->loader->getAttributesByMethodName('method1'));

        $phpInfo = $this->loader->getClassInfo($this->getFilePath('EmptyClass.php'));
        $this->assertNotEmpty($phpInfo);
        $this->assertEmpty($this->loader->getAttributesByMethodName('method1'));
    }

    public function testStatic()
    {
        $phpInfo = $this->loader->getClassInfo($this->getFilePath('TestStatic.php'));
        $this->assertNotEmpty($phpInfo);

        $this->assertTrue($this->loader->isStaticMethod('method1'));
        $this->assertTrue($this->loader->isStaticMethod('method2'));
        $this->assertFalse($this->loader->isStaticMethod('method3'));
        $this->assertFalse($this->loader->isStaticMethod('method4'));

        $phpInfo = $this->loader->getClassInfo($this->getFilePath('EmptyClass.php'));
        $this->assertNotEmpty($phpInfo);
        $this->assertFalse($this->loader->isStaticMethod('method1'));
    }

    public function testReturn()
    {
        $phpInfo = $this->loader->getClassInfo($this->getFilePath('TestReturn.php'));
        $this->assertNotEmpty($phpInfo);

        $return = $this->loader->getReturnTypeMethod('constFunction');
        $this->assertNotEmpty($return);
        $this->assertEquals('string', $return);

        $return = $this->loader->getReturnTypeMethod('classFunction');
        $this->assertNotEmpty($return);
        $this->assertEquals('CheckingAbstract', $return);

        $return = $this->loader->getReturnTypeMethod('namespaceFunction');
        $this->assertNotEmpty($return);
        $this->assertEquals(CachedDoubler::class, $return);

        $return = $this->loader->getReturnTypeMethod('emptyFunction');
        $this->assertEmpty($return);
        $this->assertEquals('', $return);

        $return = $this->loader->getReturnTypeMethod('notExistFunction');
        $this->assertEmpty($return);
        $this->assertEquals('', $return);

        $phpInfo = $this->loader->getClassInfo($this->getFilePath('EmptyClass.php'));
        $this->assertNotEmpty($phpInfo);
        $this->assertEmpty($this->loader->getReturnTypeMethod('method1'));
    }
    
    public function testVisibility()
    {
        $phpInfo = $this->loader->getClassInfo($this->getFilePath('TestVisibility.php'));
        $this->assertNotEmpty($phpInfo);

        $this->assertTrue($this->loader->isPublicMethod('publicStaticMethod'));
        $this->assertFalse($this->loader->isPublicMethod('privateStaticMethod'));
        $this->assertTrue($this->loader->isPublicMethod('publicMethod'));
        $this->assertFalse($this->loader->isPublicMethod('privateMethod'));
        $this->assertFalse($this->loader->isPublicMethod('protectedStaticMethod'));
        $this->assertFalse($this->loader->isPublicMethod('protectedMethod'));
        $this->assertFalse($this->loader->isPublicMethod('someMethod'));

        $phpInfo = $this->loader->getClassInfo($this->getFilePath('EmptyClass.php'));
        $this->assertNotEmpty($phpInfo);
        $this->assertEmpty($this->loader->isPublicMethod('method1'));
    }

    /**
     * @param $file
     * @return string
     */
    protected function getFilePath($file):string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $file;
    }
}