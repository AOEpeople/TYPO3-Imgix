<?php
namespace Aoe\Imgix\Utils;

class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldFilterNullValue()
    {
        $actual = ArrayUtils::filterEmptyValues(['foo' => null, 'bar' => 'baz']);
        $this->assertSame(['bar' => 'baz'], $actual);
    }

    /**
     * @test
     */
    public function shouldFilterEmptyStringValue()
    {
        $actual = ArrayUtils::filterEmptyValues(['foo' => '', 'bar' => 'baz']);
        $this->assertSame(['bar' => 'baz'], $actual);
    }

    /**
     * @test
     */
    public function shouldNotFilterZeroNumbers()
    {
        $actual = ArrayUtils::filterEmptyValues(['foo' => 0, 'bar' => 'baz']);
        $this->assertSame($actual, $actual);
    }
}
