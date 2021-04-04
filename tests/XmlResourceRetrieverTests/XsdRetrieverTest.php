<?php

declare(strict_types=1);

namespace XmlResourceRetrieverTests;

use XmlResourceRetriever\XsdRetriever;

final class XsdRetrieverTest extends RetrieverTestCase
{
    public function testRetrieveRecursive(): void
    {
        $localPath = $this->buildPath('recursive');
        $this->pathToClear($localPath);
        $retriever = new XsdRetriever($localPath);
        $remote = 'http://localhost:8999/xsd/entities/ticket.xsd';
        $expectedRemotes = [
            $retriever->buildPath($remote),
            $retriever->buildPath('http://localhost:8999/xsd/articles/books.xsd'),
        ];

        // verify path of downloaded file
        $local = $retriever->retrieve($remote);
        $this->assertEquals($expectedRemotes[0], $local);
        // verify file exists
        foreach ($expectedRemotes as $expectedRemote) {
            $this->assertFileExists($expectedRemote);
        }
        $this->assertXmlFileEqualsXmlFile($local, $this->assetPath('expected-ticket.xsd'));
    }

    public function testRetrieveComplexStructure(): void
    {
        if (! is_dir($this->publicPath('www.sat.gob.mx'))) {
            $this->markTestSkipped('Must download complex structures from www.sat.gob.mx');
        }
        $localPath = $this->buildPath('SATXSD');
        $this->pathToClear($localPath);
        $remotePrefix = 'http://localhost:8999/www.sat.gob.mx/sitio_internet/';
        $remote = $remotePrefix . 'cfd/3/cfdv33.xsd';
        $retriever = new XsdRetriever($localPath);
        $expectedRemotes = [
            'cfd/3/cfdv33.xsd',
            'cfd/tipoDatos/tdCFDI/tdCFDI.xsd',
            'cfd/catalogos/catCFDI.xsd',
        ];
        // verify path of downloaded file
        $retriever->retrieve($remote);
        // verify file exists
        foreach ($expectedRemotes as $expectedRemote) {
            $this->assertFileExists($retriever->buildPath($remotePrefix . $expectedRemote));
        }
    }
}
