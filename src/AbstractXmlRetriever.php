<?php

declare(strict_types=1);

namespace Eclipxe\XmlResourceRetriever;

use DOMDocument;
use DOMElement;
use finfo;
use RuntimeException;

/**
 * This is an abstract implementation of Retriever interface when working with XML contents
 * Both, XsdRetriever and XsltRetriver depends on this class
 */
abstract class AbstractXmlRetriever extends AbstractBaseRetriever implements RetrieverInterface
{
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
     * @return array<array<string, string>>
     */
    abstract protected function searchElements(): array;

    public function retrieve(string $url): string
    {
        $this->clearHistory();
        return $this->doRetrieve($url);
    }

    /**
     * @param string $resource
     * @return string
     */
    private function doRetrieve(string $resource): string
    {
        $localFilename = $this->download($resource);
        $this->addToHistory($resource, $localFilename);

        $document = new DOMDocument();
        // this error silenced call is intentional,
        // don't need to change the value of libxml_use_internal_errors for this
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $documentLoad = @$document->load($localFilename);
        if (false === $documentLoad) {
            unlink($localFilename);
            throw new RuntimeException("The source $resource contains errors");
        }

        // call recursive get searching on specified the elements
        $changed = false;
        foreach ($this->searchElements() as $search) {
            $recursiveRetrieve = $this->recursiveRetrieve(
                $document,
                $search['element'],
                $search['attribute'],
                $resource,
                $localFilename
            );
            if ($recursiveRetrieve) {
                $changed = true;
            }
        }

        if ($changed) {
            $document->save($localFilename);
        }
        return $localFilename;
    }

    private function recursiveRetrieve(
        DOMDocument $document,
        string $tagName,
        string $attributeName,
        string $currentUrl,
        string $currentFile
    ): bool {
        $modified = false;
        /** @var iterable<DOMElement> $elements */
        $elements = $document->getElementsByTagNameNS($this->searchNamespace(), $tagName);
        foreach ($elements as $element) {
            if (! $element->hasAttribute($attributeName)) {
                continue;
            }
            $location = $element->getAttribute($attributeName);
            if ('' === $location) {
                continue;
            }
            $location = $this->relativeToAbsoluteUrl($location, $currentUrl);
            if (array_key_exists($location, $this->retrieveHistory())) {
                continue;
            }
            $downloadedChild = $this->doRetrieve($location);
            $relative = Utils::relativePath($currentFile, $downloadedChild);
            $element->setAttribute($attributeName, $relative);
            $modified = true;
        }
        return $modified;
    }

    /**
     * This method checks if the recently downloaded file from $source located at $path
     * is a valid resource, if not will remove the file and throw an exception
     *
     * @param string $source
     * @param string $localpath
     * @return void
     * @throws RuntimeException when the source is not valid
     */
    protected function checkIsValidDownloadedFile(string $source, string $localpath): void
    {
        // check content is not empty
        if (0 === (int) filesize($localpath)) {
            unlink($localpath);
            throw new RuntimeException("The source $source is not an xml file because it is empty");
        }
        // check content is xml
        $mimetype = (new finfo())->file($localpath, FILEINFO_MIME_TYPE) ?: '';
        if ('application/xml' !== $mimetype && 'text/' !== substr($mimetype, 0, 5)) {
            unlink($localpath);
            throw new RuntimeException("The source $source ($mimetype) is not an xml file");
        }
    }

    private function relativeToAbsoluteUrl(string $url, string $currentUrl): string
    {
        if (false !== $this->urlParts($url)) {
            return $url;
        }
        $currentParts = $this->urlParts($currentUrl) ?: [];
        $currentParts['port'] = $currentParts['port'] ?? '';
        $currentParts['port'] = ('' !== $currentParts['port']) ? ':' . $currentParts['port'] : '';
        return implode('', [
            $currentParts['scheme'],
            '://',
            $currentParts['host'],
            $currentParts['port'],
            implode('/', Utils::simplifyPath(dirname($currentParts['path']) . '/' . $url)),
        ]);
    }
}
