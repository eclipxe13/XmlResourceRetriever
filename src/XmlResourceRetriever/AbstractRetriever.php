<?php
namespace XmlResourceRetriever;

use DOMDocument;
use XmlResourceRetriever\Downloader\DownloaderInterface;
use XmlResourceRetriever\Downloader\PhpDownloader;

abstract class AbstractRetriever
{
    /** @var string */
    private $basePath;

    /** @var DownloaderInterface */
    private $downloader;

    /**
     * Must return a string with the namespace to search for
     *
     * @return string
     */
    abstract protected function searchNamespace(): string;

    /**
     * Must return a table with rows (array of array)
     * every row must contain the keys element and attribute
     * "element" is the tag name to search for
     * "attribute" is the attribute name that contains the url
     *
     * @return array
     */
    abstract protected function searchElements(): array;

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
        $options = FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED | FILTER_FLAG_PATH_REQUIRED;
        if (false === filter_var($url, FILTER_VALIDATE_URL, $options) || false == $parts = parse_url($url)) {
            throw new \InvalidArgumentException("Invalid URL: $url");
        }
        return $this->basePath . '/' . $parts['host'] . '/' . ltrim($parts['path'], '/');
    }

    public function download(string $resource): string
    {
        // set destination
        $localPath = $this->buildPath($resource);

        // create local path
        $dirname = dirname($localPath);
        if (! is_dir($dirname) && ! @mkdir($dirname, 0777, true)) {
            throw new \RuntimeException("Unable to create directory $dirname");
        }

        // download the file into its final destination
        $this->downloader->downloadTo($resource, $localPath);

        // check content is xml
        $mimetype = (new \finfo())->file($localPath, FILEINFO_MIME_TYPE);
        if (! in_array($mimetype, ['text/xml', 'application/xml'])) {
            unlink($localPath);
            throw new \RuntimeException("The source $resource ($mimetype) is not an xml file");
        }

        return $localPath;
    }

    public function retrieve(string $resource): string
    {
        $localFilename = $this->download($resource);

        $document = new DOMDocument();
        // this error silenced call is intentional,
        // don't need to change the value of libxml_use_internal_errors for this
        if (false === @$document->load($localFilename)) {
            unlink($localFilename);
            throw new \RuntimeException("The source $resource contains errors");
        }

        // call recursive get searching on specified the elements
        $changed = false;
        foreach ($this->searchElements() as $search) {
            if ($this->recursiveRetrieve($document, $search['element'], $search['attribute'], $localFilename)) {
                $changed = true;
            }
        }

        if ($changed) {
            $document->save($localFilename);
        }
        return $localFilename;
    }

    protected function recursiveRetrieve(
        DOMDocument $document,
        string $tagName,
        string $attributeName,
        string $currentFile
    ): bool {
        $modified = false;
        $elements = $document->getElementsByTagNameNS($this->searchNamespace(), $tagName);
        foreach ($elements as $element) {
            /** @var \DOMElement $element */
            if (! $element->hasAttribute($attributeName)) {
                continue;
            }
            $location = $element->getAttribute($attributeName);
            $downloadedChild = $this->retrieve($location);
            $relative = Utils::relativePath($currentFile, $downloadedChild);
            $element->setAttribute($attributeName, $relative);
            $modified = true;
        }
        return $modified;
    }
}
