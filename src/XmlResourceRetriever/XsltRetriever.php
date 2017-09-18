<?php
namespace XmlResourceRetriever;

class XsltRetriever extends AbstractRetriever
{
    protected function searchNamespace(): string
    {
        return 'http://www.w3.org/1999/XSL/Transform';
    }

    protected function searchElements(): array
    {
        return [
            ['element' => 'import', 'attribute' => 'href'],
            ['element' => 'include', 'attribute' => 'href'],
        ];
    }
}
