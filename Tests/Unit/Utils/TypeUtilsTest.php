<?php
namespace Aoe\Imgix\Utils;

class TypeUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldMapTypeString()
    {
        $actual = TypeUtils::castTypesByMap(
            ['foo' => TypeUtils::TYPE_STRING, 'bar' => TypeUtils::TYPE_STRING],
            ['foo' => 0, 'bar' => null]
        );
        $this->assertSame(['foo' => '0', 'bar' => ''], $actual);
    }

    /**
     * @test
     */
    public function shouldMapTypeInteger()
    {
        $actual = TypeUtils::castTypesByMap(
            ['foo' => TypeUtils::TYPE_INTEGER, 'bar' => TypeUtils::TYPE_INTEGER],
            ['foo' => 0, 'bar' => null]
        );
        $this->assertSame(['foo' => 0, 'bar' => 0], $actual);
    }

    /**
     * @test
     */
    public function shouldMapTypeBoolean()
    {
        $actual = TypeUtils::castTypesByMap(
            ['foo' => TypeUtils::TYPE_BOOLEAN, 'bar' => TypeUtils::TYPE_BOOLEAN, 'baz' => TypeUtils::TYPE_BOOLEAN],
            ['foo' => 0, 'bar' => null, 'baz' => 1]
        );
        $this->assertSame(['foo' => false, 'bar' => false, 'baz' => true], $actual);
    }

    /**
     * @test
     */
    public function shouldDoNothingOnMissingMap()
    {
        $actual = TypeUtils::castTypesByMap(
            [],
            ['foo' => 0, 'bar' => null, 'baz' => 1]
        );
        $this->assertSame(['foo' => 0, 'bar' => null, 'baz' => 1], $actual);
    }
}
