<?php
namespace XmlResourceRetrieverTests\Downloader;

use PHPUnit\Framework\TestCase;
use XmlResourceRetriever\Downloader\PhpDownloader;

class PhpDownloaderTest extends TestCase
{
    public function testConstructorWithoutAttributes()
    {
        $downloader = new PhpDownloader();
        $this->assertInternalType('resource', $downloader->getContext());
    }

    public function testConstructorWithAttributes()
    {
        $context = stream_context_create();
        $downloader = new PhpDownloader($context);
        $this->assertSame($context, $downloader->getContext());
    }

    public function testSetContextThrowsExceptionWithInvalidParameter()
    {
        $downloader = new PhpDownloader();
        /** @var resource|false $isNotResource */
        $isNotResource = false;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided context is not a resource');
        $downloader->setContext($isNotResource);
    }
}
