<?php

declare(strict_types=1);

namespace Eclipxe\XmlResourceRetriever\Tests\Unit;

use Eclipxe\XmlResourceRetriever\Downloader\DownloaderInterface;
use Eclipxe\XmlResourceRetriever\RetrieverInterface;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * This test case is using CommonXmlRetriever as base to
 * test the methods on AbstractXmlRetriever
 *
 * @package Eclipxe\XmlResourceRetriever\Tests\Unit
 */
final class CommonXmlRetrieverTest extends RetrieverTestCase
{
    public function testConstructMinimal(): void
    {
        $retriever = new CommonXmlRetriever('foo');
        $this->assertInstanceOf(RetrieverInterface::class, $retriever);
        $this->assertEquals('foo', $retriever->getBasePath());
        $this->assertInstanceOf(DownloaderInterface::class, $retriever->getDownloader());
    }

    public function testBasePath(): void
    {
        $retriever = new CommonXmlRetriever(__DIR__);
        $this->assertEquals(__DIR__, $retriever->getBasePath());
    }

    public function testBuildPath(): void
    {
        $retriever = new CommonXmlRetriever('..');
        $url = 'http://example.org/some/file.txt';
        $expectedPath = '../example.org/some/file.txt';
        $this->assertEquals($expectedPath, $retriever->buildPath($url));
    }

    public function testDownloadThrowsExceptionOnEmptyString(): void
    {
        $retriever = new CommonXmlRetriever('foo');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The argument to download is empty');

        $retriever->download('');
    }

    public function testDownloadSimpleCase(): void
    {
        $localPath = $this->buildPath('foo');
        $this->pathToClear($localPath);
        $remote = 'http://localhost:8999/xsd/simple.xsd';
        $public = $this->publicPath('xsd/simple.xsd');

        // create retriever
        $retriever = new CommonXmlRetriever($localPath);

        // check for location
        $destination = $retriever->buildPath($remote);

        // download
        $downloaded = $retriever->download($remote);

        // check that the returned path is the same as the expected destination
        $this->assertEquals($destination, $downloaded);
        $this->assertXmlFileEqualsXmlFile($public, $downloaded);
    }

    public function testDownloadThrowsExceptionOnEmptyFile(): void
    {
        $localPath = $this->buildPath('empty');
        $this->pathToClear($localPath);
        $remote = 'http://localhost:8999/other/empty.xml';
        $retriever = new CommonXmlRetriever($localPath);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('it is empty');
        $retriever->download($remote);
    }

    public function testDownloadNotAnXmlFileThrowsAnExceptionAndRemoveTheFile(): void
    {
        $localPath = $this->buildPath('other');
        $this->pathToClear($localPath);
        $remote = 'http://localhost:8999/other/sample.gz';
        $retriever = new CommonXmlRetriever($localPath);

        /*
         * This test does not use expectedException since has to do more asserts after
         * the exception was thrown
         */
        // raise exception
        $raisedException = false;
        try {
            $retriever->download($remote);
        } catch (Exception $ex) {
            $this->assertInstanceOf(RuntimeException::class, $ex);
            $this->assertStringContainsString('is not an xml file', $ex->getMessage());
            $raisedException = true;
        }
        $this->assertTrue($raisedException, 'The exception on download was not raised');

        // assert that the file does not exists (even if it was downloaded)
        $local = $retriever->buildPath($remote);
        $this->assertFileDoesNotExist($local);
    }

    public function testDownloadNonExistent(): void
    {
        $localPath = $this->buildPath('non-existent');
        $this->pathToClear($localPath);
        $remote = 'http://localhost:8999/non-existent-resource.txt';
        $retriever = new CommonXmlRetriever($localPath);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to download');
        $retriever->download($remote);
    }

    public function testDownloadToNonWritable(): void
    {
        $localPath = '/bin/bash';
        $remote = 'http://localhost:8999/other/sample.xml';
        $retriever = new CommonXmlRetriever($localPath);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to create directory');
        $retriever->download($remote);
    }

    /** @return array<string, array{0: string}> */
    public function providerRetrieveXmlValidCases(): array
    {
        return [
            'xml correct' => ['http://localhost:8999/other/sample.xml'],
            'xml without header' => ['http://localhost:8999/other/xml-without-header.xml'],
        ];
    }

    /**
     * @param string $remote
     * @dataProvider providerRetrieveXmlValidCases
     */
    public function testRetrieveXmlValidCases(string $remote): void
    {
        $localPath = $this->buildPath('sample');
        $this->pathToClear($localPath);
        $retriever = new CommonXmlRetriever($localPath);
        $this->assertNotEmpty($retriever->retrieve($remote));
    }

    /** @return array<string, array{0: string}> */
    public function providerRetrieveXmlWithErrors(): array
    {
        return [
            'xml with just header' => ['http://localhost:8999/other/xml-just-header.xml'],
            'xml malformed' => ['http://localhost:8999/other/malformed.xml'],
        ];
    }

    /**
     * A malformed XML can be downloaded but not retrieved
     *
     * @param string $remote
     * @dataProvider providerRetrieveXmlWithErrors
     */
    public function testRetrieveXmlWithErrors(string $remote): void
    {
        $localPath = $this->buildPath('malformed');
        $this->pathToClear($localPath);
        $retriever = new CommonXmlRetriever($localPath);

        $raisedException = false;
        try {
            $retriever->retrieve($remote);
        } catch (Exception $ex) {
            $this->assertInstanceOf(RuntimeException::class, $ex);
            $this->assertStringContainsString('contains errors', $ex->getMessage());
            $raisedException = true;
        }
        $this->assertTrue($raisedException, 'The exception on download was not raised');
        $local = $retriever->buildPath($remote);
        $this->assertFileDoesNotExist($local, "The file $local must not exists");
    }

    /**
     * @return array<int, array<string>>
     */
    public function providerBuildPathWithInvalidUrl(): array
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
    public function testBuildPathWithInvalidUrl(string $url): void
    {
        $retriever = new CommonXmlRetriever('basepath');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL');
        $retriever->buildPath($url);
    }

    /*
     * The files under http://localhost:8999/other/common/ cover multiple cases:
     * - recursive: foo requires bar, bar requires foo
     * - recursive: baz require baz
     * - download: related attribute is relative instead of absolute
     * - empty attribute
     * - missing attribute
     * - attribute exists but tag is different
     */
    public function testRetrieverWithHistory(): void
    {
        $localPath = $this->buildPath('common');
        $this->pathToClear($localPath);

        $remoteParent = 'http://localhost:8999/other/common/parent.xml';
        $expectedRetrievedFiles = [
            'parent.xml',
            'child.xml',
            'recursive-self.xml',
            'foo.xml',
        ];
        $expectedCountRetrievedFiles = count($expectedRetrievedFiles);

        $this->assertDirectoryDoesNotExist($localPath, "The path $localPath must not exists to run this test");

        $retriever = new CommonXmlRetriever($localPath);
        $expectedDestination = dirname($retriever->buildPath($remoteParent));
        $retriever->retrieve($remoteParent);

        $history = $retriever->retrieveHistory();
        $this->assertCount($expectedCountRetrievedFiles, $history);

        $retrievedFiles = glob($expectedDestination . '/*.xml') ?: [];
        $this->assertCount($expectedCountRetrievedFiles, $retrievedFiles);
        foreach ($retrievedFiles as $retrievedFile) {
            $this->assertContains($retrievedFile, $history);
            $this->assertContains(basename($retrievedFile), $expectedRetrievedFiles);
        }
    }
}
