<?php

declare(strict_types=1);

namespace Eclipxe\XmlResourceRetriever\Tests\Unit;

use Directory;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

abstract class RetrieverTestCase extends TestCase
{
    /** @var string */
    private $pathToClear = '';

    protected function tearDown(): void
    {
        if ('' !== $this->pathToClear()) {
            $this->deleteDir($this->pathToClear());
        }
        parent::tearDown();
    }

    protected function pathToClear(string $path = ''): string
    {
        if ('' === $path) {
            return $this->pathToClear;
        }
        if (0 !== strpos($path, $this->buildPath(''))) {
            throw new LogicException('Unable to set a path to clear that is not in the build path');
        }
        $previousPath = $this->pathToClear;
        $this->pathToClear = $path;
        return $previousPath;
    }

    protected function buildPath(string $path): string
    {
        return dirname(__DIR__, 2) . '/build/tests/' . $path;
    }

    protected function publicPath(string $path): string
    {
        return dirname(__DIR__) . '/public/' . $path;
    }

    protected function assetPath(string $path): string
    {
        return __DIR__ . '/../_files/' . $path;
    }

    private function deleteDir(string $dirname): void
    {
        if (! is_dir($dirname)) {
            return;
        }
        $contents = dir($dirname);
        if (! $contents instanceof Directory) {
            throw new RuntimeException("Unable to open directory $dirname");
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
