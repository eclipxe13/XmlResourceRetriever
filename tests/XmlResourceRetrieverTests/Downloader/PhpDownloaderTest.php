<?php

declare(strict_types=1);

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
        /** @var resource $isNotResource */
        $isNotResource = null;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided context is not a resource');
        $downloader->setContext($isNotResource);
    }

    /**
     * This test is made to check if can download from https://rdc.sat.gob.mx/ since it is
     * failing from Sept/2018 delivering an expired certificate
     */
    public function testDownloadFromRdcSatGobMxInsecure()
    {
        $url = 'https://rdc.sat.gob.mx/rccf/000010/000004/06/25/80/00001000000406258094.cer';
        $downloader = new PhpDownloader();
        $context = stream_context_create([
            'ssl' => ['verify_peer' => false],
        ]);
        $downloader->setContext($context);
        $downloadPath = tempnam('', '') ?: (__DIR__ . '/deleteme.txt');
        $downloader->downloadTo($url, $downloadPath);
        $this->assertContains('00001000000406258094', file_get_contents($downloadPath));
        unlink($downloadPath);
    }
}
