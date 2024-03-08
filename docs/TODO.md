# eclipxe/XmlResourceRetriever To Do List

- It may be nice to move file system operations (like is_dir, mkdir and finfo) to another object.
  In this way it would be possible to use flysystem inside the Retriever.
- Create script to run in common line interface `bin/xml-resource-retriever`.
- Add docblocks to all public methods and create docs based on that.

## Backward compatibility changes

The following changes produces a BC break. Requires a new major version.

- Add `DownloaderInterface::downloadTo` return type `void`.
- Remove `'void_return' => false,` rule in `php-cs-fixer` configuration file.
