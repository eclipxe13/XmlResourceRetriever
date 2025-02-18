<?php

declare(strict_types=1);

namespace Eclipxe\XmlResourceRetriever\Tests\Unit;

use Eclipxe\XmlResourceRetriever\XsdRetriever;

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
        $pathSatUrls = $this->publicPath('sat-urls.txt');
        if (! is_dir($this->publicPath('www.sat.gob.mx')) || ! is_file($pathSatUrls)) {
            $this->markTestSkipped('Download complex structures from www.sat.gob.mx to run this test');
        }
        $localPath = $this->buildPath('SATXSD');
        $this->pathToClear($localPath);
        $remotePrefix = 'http://localhost:8999/www.sat.gob.mx/sitio_internet/';
        $remote = $remotePrefix . 'cfd/3/cfdv33.xsd';
        $retriever = new XsdRetriever($localPath);
        $expectedRemotes = array_map(
            /** @phpstan-ignore-next-line */
            function (string $url): string {
                return str_replace('http://www.sat.gob.mx/sitio_internet/', '', trim($url));
            },
            preg_grep('/xsd$/', explode(PHP_EOL, file_get_contents($pathSatUrls) ?: '')) ?: []
        );
        // verify path of downloaded file
        $retriever->retrieve($remote);
        // verify file exists
        foreach ($expectedRemotes as $expectedRemote) {
            $this->assertFileExists($retriever->buildPath($remotePrefix . $expectedRemote));
        }
    }
}
