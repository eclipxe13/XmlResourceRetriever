<?php
namespace XmlResourceRetriever\Downloader;

class PhpDownloader implements DownloaderInterface
{
    public function downloadTo(string $source, string $destination)
    {
        if (! @copy($source, $destination)) {
            $previousException = null;
            if (null !== $lastError = error_get_last()) {
                $previousException = new \Exception($lastError['message']);
            }
            throw new \RuntimeException("Unable to download $source to $destination", 0, $previousException);
        }
    }
}
