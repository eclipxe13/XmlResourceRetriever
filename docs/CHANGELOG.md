# Version 1.1.0 2017-10-03
- Created a new interface `RetrieverInterface` that defines the contract for a retriever object
- Both `XsltRetriever` and `XsdRetriver` implements that interface since `AbstractRetriever` implements it.
- Add new method `RetrieverInterface::retrieveHistory(): array` that must return the list or urls and paths
  returned in the last invocation of `RetrieverInterface::retrieve` method. 
- `AbstractRetriever::download` throws an exception if empty string is provided.
- Fix infinite recursion problems:
    foo requires bar and bar requires foo
    baz require baz
- Fix malformation issues on recursive download:
    - element.attribute does not exists 
    - element.attribute exists but is empty
    - element.attribute is a relative path instead of absolute
- With this release a 100% code coverage is reached!

# Version 1.0.0 2017-09-18
- Initial release
