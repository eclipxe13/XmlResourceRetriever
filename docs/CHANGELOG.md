# eclipxe/XmlResourceRetriever Changelog

Notice: This library follows [SEMVER 2.0.0](https://semver.org/spec/v2.0.0.html) convention.

## Unreleased changes 2024-03-08

- Fix changelog date for version 2.0.2.
- Move `tests/assets/` to `tests/_files/`.
- Avoid GitHub language detection on folder `tests/_files/`.

## Version 2.0.2 2024-03-08

- Improve code and fix issues from psalm avoiding falsy comparisons.
- Update license year to 2024 and add owner URL.
- Fix build bagde.
- Update coding standards.
- For GitHub workflow:
    - Run jobs using PHP 8.3.
    - Add PHP 8.2 and PHP 8.3 to test matrix.
    - Remove composer tool installation where is not required.
    - Update GitHub actions to version 4.
    - Replace GitHub directive `::set-output` with `$GITHUB_OUTPUT`.
    - Rename php-version matrix variable name (singular).
    - Display PSalm version before run.
- Update development tools.

## Unreleased 2022-03-08

- Fix build because PHPStan needs type specification on `DOMNodeList`.
- Update development tools as of 2022-03-08.

## Version 2.0.1 2022-02-25

- Update license year. Happy 2022.
- Rename git main branch from `master` to `main`.
- Improve GitHub Workflow CI:
  - Split steps into jobs.
  - Install SAT files to test complex structures.
  - Add PHP 8.1 to test matrix.
- Update grammar and add PHP version.
- Update development tools:
  - Move tools from `composer` and `vendor/bin/` to `phive` and `tools/`.
  - Upgrade `php-cs-fixer` to version 3.6.
  - Remove deprecated attibute `totallyTyped` on `psalm.xml.dist`.
- Update contributing guide.
- Update files included on distribution package.
- Update guide to update `tests/public/www.sat.gob.mx`.

## Version 2.0.0 2021-04-03

- This version changes the namespace to `Eclipxe\XmlResourceRetriever`.
- The deprecated class `AbstractRetriever` has been removed.
- On `AbstractBaseRetriever` file types `text/` are downloaded and removed if they fail to load.

## Version 1.3.1 2020-01-08

This is a development maintenance version.

- Add `ext-filter` to dependences.
- Update license year.
- Add Travis-CI PHP versions 7.3 & 7.4.
- Add development scripts
- Change `phpstan/phpstan-shim` to `phpstan/phpstan` and add `^0.12`. Minimal code changes to fix minor issues.
- Add `vimeo/psalm` using level 1. Minimal code changes to fix minor issues.
- Reformat document files.
- Verify resource type is `stream-context` when set to `PhpDownloader`.
- Test cases are abstract, test classes are final.

## Version 1.3.0 2018-10-18

- Improve `Eclipxe\XmlResourceRetriever\AbstractBaseRetriever::urlParts`
    - Remove deprecated useless constants `FILTER_FLAG_SCHEME_REQUIRED` or `FILTER_FLAG_HOST_REQUIRED`
    - Add docblocks
- Add stream context features to `Eclipxe\XmlResourceRetriever\Downloader\PhpDownloader` and tests
- Add strict_types to all project
- Improve configuration file for php-cs-fixer tool

## Version 1.2.1 2018-09-04

- An XML resource that is an empty file is declared as not valid
- Include missing extensions in composer.json
- Allow PHPUnit ^7.3 if PHP => 7.1
- Fix phpunit.xml.dist settings
- Add phpstan-shim to build dependences, fix issues found
- Fix travis build to only create coverage on one PHP version
- Move from parallel-lint to phplint

## Version 1.2.0 2018-01-17

- Split `AbstractRetriever` into `AbstractBaseRetriever` and `AbstractXmlRetriever`
- Deprecate `AbstractRetriever`, will be removed in next version that break compatibility
- Use `AbstractBaseRetriever` if you need the logic for `download`, `buildPath`, `history`, getters and setters.
- Use `AbstractXmlRetriever` to work with strictly xml resources
- `XsdRetriever` & `XsltRetriever` now extends `AbstractXmlRetriever`
- The testing class `CommonRetriever` is renamed to `CommonXmlRetriever`

## Version 1.1.0 2017-10-03

- Created a new interface `RetrieverInterface` that defines the contract for a retriever object
- Both `XsltRetriever` and `XsdRetriver` implements that interface since `AbstractXmlRetriever` implements it.
- Add new method `RetrieverInterface::retrieveHistory(): array` that must return the list or urls and paths
  returned to the last invocation of `RetrieverInterface::retrieve` method. 
- `AbstractXmlRetriever::download` throws an exception if empty string is provided.
- Fix infinite recursion problems:
    foo requires bar and bar requires foo
    baz require baz
- Fix malformation issues on recursive downloads:
    - element.attribute does not exists 
    - element.attribute exists but is empty
    - element.attribute is a relative path instead of absolute
- With this release a 100% code coverage is reached!

## Version 1.0.0 2017-09-18

- Initial release
