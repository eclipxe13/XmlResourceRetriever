# eclipxe/XmlResourceRetriever To Do List

**Remove AbstractRetriever class, already deprecated**

- [ ] It may be nice to move file system operations (like is_dir, mkdir and finfo) to another object.
      In this way it would be possible to use flysystem inside the Retriever.
- [ ] Create script to run in common line interface `bin/xml-resource-retriever`.
- [ ] Add docblocks to all public methods and create docs based on that.
- [ ] Deprecate unsupported versions of PHP.
    - Travis-CI: Run without checking versions.
    - Scrutinizer-CI: Do not remove developnment dependences.
