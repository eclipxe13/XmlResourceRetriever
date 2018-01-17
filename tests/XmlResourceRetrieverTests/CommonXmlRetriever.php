<?php
namespace XmlResourceRetrieverTests;

use XmlResourceRetriever\AbstractXmlRetriever;

class CommonXmlRetriever extends AbstractXmlRetriever
{
    protected function searchNamespace(): string
    {
        return 'http://example.com/ns';
    }

    protected function searchElements(): array
    {
        return [
            ['element' => 'resource', 'attribute' => 'href'],
        ];
    }
}
