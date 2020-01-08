<?php

declare(strict_types=1);

namespace XmlResourceRetrieverTests;

use PHPUnit\Framework\TestCase;
use XmlResourceRetriever\Utils;

class UtilsSimplifyPathTest extends TestCase
{
    public function providerExpectedBehavior()
    {
        return [
            ['a/b/c/d/e', ['a', 'b', 'c', 'd', 'e']],
            ['/a/b/c/d/e', ['', 'a', 'b', 'c', 'd', 'e']],
            ['/a/b/c/x/../d', ['', 'a', 'b', 'c', 'd']],
            ['/a/b/c/d/e/..', ['', 'a', 'b', 'c', 'd']],
            ['a///b', ['a', 'b']],
            ['a/./b', ['a', 'b']],
            ['./a/b/c', ['a', 'b', 'c']],
            ['./a///./../b/c', ['b', 'c']],
            ['x/./././.', ['x']],
            ['../../../x', ['..', '..', '..', 'x']],
            ['x', ['x']],
            ['', ['']],
            ['.', ['']],
            ['/', ['', '']],
            ['./', ['']],
            ['./.', ['']],
        ];
    }

    /**
     * @param string $source
     * @param string[] $expected
     * @dataProvider providerExpectedBehavior
     */
    public function testExpectedBehavior(string $source, array $expected)
    {
        $this->assertEquals($expected, Utils::simplifyPath($source));
    }
}
