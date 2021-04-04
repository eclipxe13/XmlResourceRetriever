<?php

declare(strict_types=1);

namespace XmlResourceRetrieverTests;

use PHPUnit\Framework\TestCase;
use XmlResourceRetriever\Utils;

final class UtilsRelativePathTest extends TestCase
{
    /**
     * @return array<string, string[]>
     */
    public function providerExpectedBehavior(): array
    {
        return [
            'absolutes' => ['/h/u/schemas/structs/foo.xml', '/h/u/schemas/entity/money.xml', '../entity/money.xml'],
            'relative 0 up' => ['a1/a2/a3', 'a1/a2/b1', 'b1'],
            'relative 1 up' => ['a1/a2/a3', 'a1/b1', '../b1'],
            'relative 2 up' => ['a1/a2/a3', 'b1', '../../b1'],
            'both absolute' => ['/foo/bar/baz', '/root', '../../root'],
            'both relative' => ['foo/bar/baz', 'root', '../../root'],
            'absolute to relative' => ['/foo/bar/baz', 'root', '../../../root'],
            'relative to absolute' => ['foo/bar/baz', '/root', '/root'],
        ];
    }

    /**
     * @param string $source
     * @param string $destination
     * @param string $expected
     * @dataProvider providerExpectedBehavior
     */
    public function testExpectedBehavior(string $source, string $destination, string $expected): void
    {
        $this->assertEquals($expected, Utils::relativePath($source, $destination));
    }
}
