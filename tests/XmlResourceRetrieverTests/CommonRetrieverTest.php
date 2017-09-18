<?php
namespace XmlResourceRetrieverTests;

use XmlResourceRetriever\Downloader\DownloaderInterface;

/**
 * This test case is using CommonRetriever as base to
 * test the methods on AbstractRetriever
 *
 * @package XmlResourceRetrieverTests
 */
class CommonRetrieverTest extends RetrieverTestCase
{
    public function testConstructMinimal()
    {
        $retriever = new CommonRetriever('foo');
        $this->assertEquals('foo', $retriever->getBasePath());
        $this->assertInstanceOf(DownloaderInterface::class, $retriever->getDownloader());
    }

    public function testBasePath()
    {
        $retriever = new CommonRetriever(__DIR__);
        $this->assertEquals(__DIR__, $retriever->getBasePath());
    }

    public function testBuildPath()
    {
        $retriever = new CommonRetriever('..');
        $url = 'http://example.org/some/file.txt';
        $expectedPath = '../example.org/some/file.txt';
        $this->assertEquals($expectedPath, $retriever->buildPath($url));
    }

    public function testDownloadSimpleCase()
    {
        $localPath = $this->buildPath('foo');
        $this->pathToClear($localPath);
        $remote = 'http://localhost:8999/xsd/simple.xsd';
        $public = $this->publicPath('xsd/simple.xsd');

        // create retriever
        $retriever = new CommonRetriever($localPath);

        // check for location
        $destination = $retriever->buildPath($remote);

        // download
        $downloaded = $retriever->download($remote);

        // check that the returned path is the same as the expected destination
        $this->assertEquals($destination, $downloaded);
        $this->assertXmlFileEqualsXmlFile($public, $downloaded);
    }

    public function testDownloadNotAnXmlFileThrowsAnExceptionAndRemoveTheFile()
    {
        $localPath = $this->buildPath('other');
        $this->pathToClear($localPath);
        $remote = 'http://localhost:8999/other/sample.txt';
        $retriever = new CommonRetriever($localPath);

        /*
         * This test does not use expectedException since has to do more asserts after
         * the exception was thrown
         */
        // raise exception
        $raisedException = false;
        try {
            $retriever->download($remote);
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\RuntimeException::class, $ex);
            $this->assertContains('is not an xml file', $ex->getMessage());
            $raisedException = true;
        }
        $this->assertTrue($raisedException, 'The exception on download was not raised');

        // assert that the file does not exists (even if it was downloaded)
        $local = $retriever->buildPath($remote);
        $this->assertFileNotExists($local);
    }

    public function testDownloadNonExistent()
    {
        $localPath = $this->buildPath('non-existent');
        $this->pathToClear($localPath);
        $remote = 'http://localhost:8999/non-existent-resource.txt';
        $retriever = new CommonRetriever($localPath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to download');
        $retriever->download($remote);
    }

    public function testDownloadToNonWritable()
    {
        $localPath = '/bin/bash';
        $remote = 'http://localhost:8999/other/sample.xml';
        $retriever = new CommonRetriever($localPath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to create directory');
        $retriever->download($remote);
    }

    public function testDownloadMalformed()
    {
        $localPath = $this->buildPath('malformed');
        $this->pathToClear($localPath);
        $retriever = new CommonRetriever($localPath);
        $remote = 'http://localhost:8999/other/malformed.xml';
        $this->assertNotEmpty($retriever->download($remote));
    }

    public function testRetrieveMalformed()
    {
        $localPath = $this->buildPath('malformed');
        $this->pathToClear($localPath);
        $retriever = new CommonRetriever($localPath);
        $remote = 'http://localhost:8999/other/malformed.xml';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('malformed.xml');
        $retriever->retrieve($remote);
    }

    public function providerBuildPathWithInvalidUrl()
    {
        return [
            ['scheme://host'],
            ['host/path'],
            ['not-an-url'],
        ];
    }

    /**
     * @param string $url
     * @dataProvider providerBuildPathWithInvalidUrl
     */
    public function testBuildPathWithInvalidUrl(string $url)
    {
        $retriever = new CommonRetriever('basepath');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL');
        $retriever->buildPath($url);
    }
}
