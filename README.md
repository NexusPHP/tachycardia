# Nexus Tachycardia

[![PHP version](https://img.shields.io/packagist/php-v/nexusphp/tachycardia)](https://php.net)
![build](https://github.com/NexusPHP/tachycardia/actions/workflows/build.yml/badge.svg?branch=2.x)
[![Coverage Status](https://coveralls.io/repos/github/NexusPHP/tachycardia/badge.svg?branch=2.x)](https://coveralls.io/github/NexusPHP/tachycardia?branch=2.x)
[![PHPStan](https://img.shields.io/badge/PHPStan-max%20level-brightgreen)](phpstan.neon.dist)
[![Latest Stable Version](https://poser.pugx.org/nexusphp/tachycardia/v)](//packagist.org/packages/nexusphp/tachycardia)
[![License](https://img.shields.io/github/license/nexusphp/tachycardia)](LICENSE)
[![Total Downloads](https://poser.pugx.org/nexusphp/tachycardia/downloads)](//packagist.org/packages/nexusphp/tachycardia)

**Tachycardia** is a PHPUnit extension that detects and reports slow running tests and prints them
right in your console. It can also optionally inline annotate the specific tests in the files
during pull requests.

**NOTE:** Tachycardia will only detect the slow tests in your test suites but will offer no explanation
as to why these identified are slow. You should use a dedicated profiler for these instead.

```console
$ vendor/bin/phpunit
PHPUnit 10.5.5 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.3.1 with Xdebug 3.3.1
Configuration: /home/runner/work/tachycardia/tachycardia/phpunit.xml.dist
Random Seed:   1698146158

................................................................. 65 / 96 ( 67%)
...............................                                   96 / 96 (100%)

Nexus\PHPUnit\Tachycardia\TachycardiaExtension identified this sole slow test:
⚠  Took 1.3374s from 1.0000s limit to run Nexus\\PHPUnit\\Tachycardia\\Tests\\Renderer\\GithubRendererTest::testRendererWorksProperly

Slow tests: Time: 00:00:01.710 (2.54%)

Time: 00:58.737, Memory: 16.00 MB

OK (96 tests, 265 assertions)

Generating code coverage report in Clover XML format ... done [00:00.391]

Generating code coverage report in HTML format ... done [00:01.930]
```

## Installation

Tachycardia should only be installed as a development-time dependency to aid in
running your project's test suite. You can install using [Composer](https://getcomposer.org):

    composer require --dev nexusphp/tachycardia

## Configuration

Tachycardia supports these parameters:

- **time-limit** - Time limit in seconds to be enforced for all tests. All tests exceeding
    this amount will be considered as slow. ***Default: 1.00***
- **report-count** - Number of slow tests to be displayed in the console report. This is ignored
    on Github Actions report. ***Default: 10***
- **precision** - Degree of precision of the decimals of the test's consumed time and allotted
    time limit. ***Default: 4***
- **format** - The format of the renderer for the console.
- **ci-format** - The format of the renderer for the CI.

Renderer formats for both the console and CI could be any of:

| Format       | For Console? | For CI? | Remarks            |
|--------------|:------------:|:-------:|--------------------|
| **list**     | ✅︎           | ❌      | Default for console |
| **table**    | ✅︎           | ❌      |                     |
| **github**   | ✅︎           | ✅︎      | Default for CI      |
| **gitlab**   | ✅︎           | ✅︎      |                     |
| **teamcity** | ✅︎           | ✅︎      |                     |

To use the extension with its default configuration options, you can simply add the following
into your `phpunit.xml.dist` or `phpunit.xml` file.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         cacheResultFile="build/.phpunit.cache/test-results"
         colors="true"
         beStrictAboutOutputDuringTests="true"
         beStrictAboutTodoAnnotatedTests="true"
         failOnRisky="true"
         failOnWarning="true">

    <!-- Your other phpunit configurations here -->

    <extensions>
        <bootstrap class="Nexus\PHPUnit\Tachycardia\TachycardiaExtension" />
    </extensions>
</phpunit>
```

Now, run `vendor/bin/phpunit`. If there are test cases where the time consumed exceeds the configured
time limits, these will be displayed in the console after all tests have completed.

If you wish to customize one or more of the available options, you can just change the entry in your
`phpunit.xml.dist` or `phpunit.xml` file.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         cacheResultFile="build/.phpunit.cache/test-results"
         colors="true"
         beStrictAboutOutputDuringTests="true"
         beStrictAboutTodoAnnotatedTests="true"
         failOnRisky="true"
         failOnWarning="true">

    <!-- Your other phpunit configurations here -->

    <extensions>
        <bootstrap class="Nexus\PHPUnit\Tachycardia\TachycardiaExtension">
            <parameter name="time-limit" value="2.00" />
            <parameter name="report-count" value="30" />
            <parameter name="precision" value="2" />
            <parameter name="format" value="table" />
            <parameter name="ci-format" value="github" />
        </bootstrap>
    </extensions>
</phpunit>
```

## Documentation

- [Reporting Slow Tests](docs/enable_reporting.md)
    - [Enable/disable console reporting using environment variable](docs/enable_reporting.md#enabledisable-console-reporting-using-environment-variable)
    - [Enable/disable profiling in Github Actions](docs/enable_reporting.md#enabledisable-profiling-in-github-actions)
- [Custom Time Limits](docs/custom_time_limits.md)
    - [Setting custom time limits per test](docs/custom_time_limits.md#setting-custom-time-limits-per-test)
    - [Setting custom time limits per class](docs/custom_time_limits.md#setting-custom-time-limits-per-class)
    - [Disabling time limits per test or per class](docs/custom_time_limits.md#disabling-time-limits-per-test-or-per-class)
    - [Using Attributes instead](docs/custom_time_limits.md#using-attributes-instead)
- [Tabulating results instead of plain render](docs/tabulating_results.md)
- [Rerunning slow tests to see if these are fast now](docs/rerunning_tests.md)

## Upgrading

Upgrading from v1.x to v2.x? See the [UPGRADING](docs/UPGRADING.md) Guide.

## Contributing

Contributions are very much welcome. If you see an improvement or bug fix,
open a [PR](https://github.com/NexusPHP/tachycardia/pulls) now!

Read more on the [Contributing to Nexus Tachycardia](.github/CONTRIBUTING.md).

## Inspiration

Tachycardia was inspired from [`johnkary/phpunit-speedtrap`](https://github.com/johnkary/phpunit-speedtrap),
but injected with anabolic steroids.

Tachycardia is actually a [medical term](https://www.webmd.com/heart-disease/atrial-fibrillation/what-are-the-types-of-tachycardia)
referring to a heart rate that exceeds the normal resting rate in general of over 100 beats per minute.

## License

This library is licensed under the [MIT License](LICENSE).
