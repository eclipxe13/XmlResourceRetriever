# Version 1.2.2 2018-10-18
- Improve `XmlResourceRetriever\AbstractBaseRetriever::urlParts`
    - Remove deprecated useless constants `FILTER_FLAG_SCHEME_REQUIRED` or `FILTER_FLAG_HOST_REQUIRED`
    - Add docblocks

# Version 1.2.1 2018-09-04
- An XML resource that is an empty file is declared as not valid
- Include missing extensions in composer.json
- Allow PHPUnit ^7.3 if PHP => 7.1
- Fix phpunit.xml.dist settings
- Add phpstan-shim to build dependences, fix issues found
- Fix travis build to only create coverage on one PHP version
- Move from parallel-lint to phplint

# Version 1.2.0 2018-01-17
- Split `AbstractRetriever` into `AbstractBaseRetriever` and `AbstractXmlRetriever`
- Deprecate `AbstractRetriever`, will be removed in next version that break compatibility
- Use `AbstractBaseRetriever` if you need the logic for download, buildPath, gistory, getter and setters already made
- Use `AbstractXmlRetriever` to work with strictly xml resources
- `XsdRetriever` and `XsltRetriever` now extends `AbstractXmlRetriever`
- The testing class `CommonRetriever` is renamed to `CommonXmlRetriever`

# Version 1.1.0 2017-10-03
- Created a new interface `RetrieverInterface` that defines the contract for a retriever object
- Both `XsltRetriever` and `XsdRetriver` implements that interface since `AbstractXmlRetriever` implements it.
- Add new method `RetrieverInterface::retrieveHistory(): array` that must return the list or urls and paths
  returned in the last invocation of `RetrieverInterface::retrieve` method. 
- `AbstractXmlRetriever::download` throws an exception if empty string is provided.
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
