<?php

declare(strict_types=1);

namespace Eclipxe\XmlResourceRetriever\Downloader;

use RuntimeException;

interface DownloaderInterface
{
    /**
     * @param string $source
     * @param string $destination
     * @throws RuntimeException if an error occurs
     * @return void
     */
    public function downloadTo(string $source, string $destination);
}
