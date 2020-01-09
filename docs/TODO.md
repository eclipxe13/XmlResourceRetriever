# eclipxe/XmlResourceRetriever To Do List

**Remove AbstractRetriever class, already deprecated**

- [ ] It may be nice to move file system operations (like is_dir, mkdir and finfo) to another object.
      In this way it would be possible to use flysystem inside the Retriever.
- [ ] Create script to run in common line interface `bin/xml-resource-retriever`.
- [ ] Add docblocks to all public methods and create docs based on that.
- [ ] Enable strict types on every file? `declare(strict_types=1)`
- [ ] When using resources validate that they are of specific type using `get_resource_type`
- [ ] Deprecate unsupported versions of PHP.
    - Travis-CI: Run without checking versions.
    - Scrutinizer-CI: Do not remove developnment dependences.
