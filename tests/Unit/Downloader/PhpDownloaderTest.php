<?php

declare(strict_types=1);

namespace Eclipxe\XmlResourceRetriever\Tests\Unit\Downloader;

use Eclipxe\XmlResourceRetriever\Downloader\PhpDownloader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PhpDownloaderTest extends TestCase
{
    public function testConstructorWithoutAttributes(): void
    {
        $downloader = new PhpDownloader();
        $context = $downloader->getContext();
        $this->assertTrue(is_resource($context));
        $this->assertSame('stream-context', get_resource_type($context));
    }

    public function testConstructorWithAttributes(): void
    {
        $context = stream_context_create();
        $downloader = new PhpDownloader($context);
        $this->assertSame($context, $downloader->getContext());
    }

    public function testSetContextThrowsExceptionWithInvalidParameter(): void
    {
        $downloader = new PhpDownloader();
        /** @var resource $isNotResource */
        $isNotResource = null;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided context is not a resource');
        $downloader->setContext($isNotResource);
    }

    public function testSetContextWithInvalidContextType(): void
    {
        $invalidContext = fopen(__FILE__, 'r');
        $downloader = new PhpDownloader();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Provided context is not a stream-context resource, given: \S+/');
        $downloader->setContext($invalidContext);
    }

    /**
     * This test is made to check if can download from https://rdc.sat.gob.mx/
     * It is using an insecure connection since it is failing Sept/2018 delivering an expired certificate
     */
    public function testDownloadFromRdcSatGobMxInsecure(): void
    {
        $url = 'https://rdc.sat.gob.mx/rccf/000010/000004/06/25/80/00001000000406258094.cer';
        $downloader = new PhpDownloader();
        $context = stream_context_create([
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);
        $downloader->setContext($context);
        $downloadPath = tempnam('', '') ?: (__DIR__ . '/deleteme.txt');
        $downloader->downloadTo($url, $downloadPath);
        $this->assertStringContainsString('00001000000406258094', (string) file_get_contents($downloadPath));
        unlink($downloadPath);
    }
}
