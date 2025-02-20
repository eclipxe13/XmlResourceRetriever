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
        $isNotResource = null; /** @phpstan-ignore-line */
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
}
