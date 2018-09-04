<?php
namespace XmlResourceRetriever;

use XmlResourceRetriever\Downloader\DownloaderInterface;
use XmlResourceRetriever\Downloader\PhpDownloader;

/**
 * This is an abstract base imlementation of RetrieverInterface
 *
 * It contains basic helper functions and implement for construct, getters, setters, history, buildPath and download
 * There are other type of resources that could be retrieved and might use all this logic
 */
abstract class AbstractBaseRetriever implements RetrieverInterface
{
    /** @var string */
    private $basePath;

    /** @var DownloaderInterface */
    private $downloader;

    /**
     * This variable stores the list of retrieved resources to avoid infinite recursion
     * @var array
     */
    private $history = [];

    /**
     * This method checks if the recently downloaded file from $source located at $path
     * is a valid resource, if not will remove the file and throw an exception
     *
     * @param string $source
     * @param string $localpath
     * @throws \RuntimeException when the source is not valid
     */
    abstract protected function checkIsValidDownloadedFile(string $source, string $localpath);

    /**
     * Retriever constructor.
     *
     * @param string $basePath
     * @param DownloaderInterface $downloader
     */
    public function __construct($basePath, DownloaderInterface $downloader = null)
    {
        $this->basePath = $basePath;
        $this->setDownloader($downloader ? : new PhpDownloader());
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getDownloader(): DownloaderInterface
    {
        return $this->downloader;
    }

    public function setDownloader(DownloaderInterface $downloader)
    {
        $this->downloader = $downloader;
    }

    public function buildPath(string $url): string
    {
        if (false === $parts = $this->urlParts($url)) {
            throw new \InvalidArgumentException("Invalid URL: $url");
        }
        return $this->basePath . '/' . $parts['host'] . '/' . ltrim($parts['path'], '/');
    }

    public function download(string $resource): string
    {
        // validate resource
        if ('' === $resource) {
            throw new \UnexpectedValueException('The argument to download is empty');
        }

        // set destination
        $localPath = $this->buildPath($resource);

        // create local path
        $dirname = dirname($localPath);
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        if (! is_dir($dirname) && ! @mkdir($dirname, 0777, true)) {
            throw new \RuntimeException("Unable to create directory $dirname");
        }

        // download the file into its final destination
        $this->downloader->downloadTo($resource, $localPath);

        // check content is valid
        $this->checkIsValidDownloadedFile($resource, $localPath);

        return $localPath;
    }

    public function retrieveHistory(): array
    {
        return $this->history;
    }

    protected function clearHistory()
    {
        $this->history = [];
    }

    protected function addToHistory(string $source, string $localpath)
    {
        $this->history[$source] = $localpath;
    }

    protected function urlParts(string $url)
    {
        $options = FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED | FILTER_FLAG_PATH_REQUIRED;
        if (false === filter_var($url, FILTER_VALIDATE_URL, $options)) {
            return false;
        }
        return parse_url($url);
    }
}
