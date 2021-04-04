<?php

declare(strict_types=1);

namespace Eclipxe\XmlResourceRetriever\Tests\Unit;

use Eclipxe\XmlResourceRetriever\Utils;
use PHPUnit\Framework\TestCase;

final class UtilsSimplifyPathTest extends TestCase
{
    /**
     * @return array<int, array<string[]|string>>
     */
    public function providerExpectedBehavior(): array
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
    public function testExpectedBehavior(string $source, array $expected): void
    {
        $this->assertEquals($expected, Utils::simplifyPath($source));
    }
}
