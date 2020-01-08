<?php

declare(strict_types=1);

namespace XmlResourceRetrieverTests;

use PHPUnit\Framework\TestCase;

abstract class RetrieverTestCase extends TestCase
{
    private $pathToClear = '';

    public function tearDown()
    {
        if ($this->pathToClear()) {
            $this->deleteDir($this->pathToClear());
        }
        parent::tearDown();
    }

    protected function pathToClear($path = '')
    {
        if (! $path) {
            return $this->pathToClear;
        }
        if (0 !== strpos($path, $this->buildPath(''))) {
            throw new \LogicException('Unable to set a path to clear that is not in the build path');
        }
        $previousPath = $this->pathToClear;
        $this->pathToClear = $path;
        return $previousPath;
    }

    protected function buildPath(string $path)
    {
        return dirname(__DIR__, 2) . '/build/tests/' . $path;
    }

    protected function publicPath(string $path)
    {
        return dirname(__DIR__, 1) . '/public/' . $path;
    }

    protected function assetPath(string $path)
    {
        return dirname(__DIR__, 1) . '/assets/' . $path;
    }

    private function deleteDir($dirname)
    {
        if (! is_dir($dirname)) {
            return;
        }
        $contents = dir($dirname);
        if (! $contents instanceof \Directory) {
            throw new \RuntimeException("Unable to open directory $dirname");
        }
        while (false !== $location = $contents->read()) {
            if ('..' === $location || '.' === $location) {
                continue;
            }
            $location = $dirname . '/' . $location;
            if (is_dir($location)) {
                $this->deleteDir($location);
            } else {
                unlink($location);
            }
        }
        $contents->close();
        rmdir($dirname);
    }
}
