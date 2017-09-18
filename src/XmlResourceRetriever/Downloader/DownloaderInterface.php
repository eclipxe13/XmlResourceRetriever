<?php
namespace XmlResourceRetriever\Downloader;

interface DownloaderInterface
{
    /**
     * @param string $source
     * @param string $destination
     * @throws \RuntimeException if an error occurs
     */
    public function downloadTo(string $source, string $destination);
}
