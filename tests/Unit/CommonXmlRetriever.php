<?php

declare(strict_types=1);

namespace Eclipxe\XmlResourceRetriever\Tests\Unit;

use Eclipxe\XmlResourceRetriever\AbstractXmlRetriever;

final class CommonXmlRetriever extends AbstractXmlRetriever
{
    protected function searchNamespace(): string
    {
        return 'http://example.com/ns';
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function searchElements(): array
    {
        return [
            ['element' => 'resource', 'attribute' => 'href'],
        ];
    }
}
