<?php

declare(strict_types=1);

namespace XmlResourceRetrieverTests;

use XmlResourceRetriever\AbstractXmlRetriever;

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
