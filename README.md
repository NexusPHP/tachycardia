# NexusPHP Tachycardia

[![PHP version](https://img.shields.io/packagist/php-v/nexusphp/tachycardia)](https://php.net)
![build](https://github.com/NexusPHP/tachycardia/actions/workflows/build.yml/badge.svg?branch=develop)
[![Coverage Status](https://coveralls.io/repos/github/NexusPHP/tachycardia/badge.svg?branch=develop)](https://coveralls.io/github/NexusPHP/tachycardia?branch=develop)
[![PHPStan Level](https://img.shields.io/badge/PHPStan%20Level-max-brightgreen)](phpstan.neon.dist)
[![Latest Stable Version](https://poser.pugx.org/nexusphp/tachycardia/v)](//packagist.org/packages/nexusphp/tachycardia)
[![License](https://img.shields.io/github/license/nexusphp/tachycardia)](LICENSE)

**Tachycardia** is a PHPUnit extension that detects and reports slow running tests and prints them
right in your console. It can also optionally inline annotate the specific tests in the files
during pull requests.

**NOTE:** Tachycardia will only detect the slow tests in your test suites but will offer no explanation
as to why these identified are slow. You should use a dedicated profiler for these instead.

```sh
$ vendor/bin/phpunit
PHPUnit 9.5.3 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.0.3 with Xdebug 3.0.3
Configuration: /var/www/tachycardia/phpunit.xml.dist

....S.........                                                    14 / 14 (100%)

Nexus\PHPUnit\Extension\Tachycardia identified these 7 slow tests:
⚠  Took 7.0181s from 1.0000s limit to run Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testWithProvider with data set \"slowest\"
⚠  Took 6.0154s from 1.0000s limit to run Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testWithProvider with data set \"slower\"
⚠  Took 5.0216s from 1.0000s limit to run Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testWithProvider with data set \"slow\"
⚠  Took 4.0104s from 1.0000s limit to run Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testSlowestTest
⚠  Took 3.0170s from 1.0000s limit to run Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testSlowerTest
⚠  Took 2.0107s from 1.0000s limit to run Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testSlowTest
⚠  Took 1.0150s from 0.5000s limit to run Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testCustomLowerLimit


Time: 00:31.109, Memory: 8.00 MB

There was 1 skipped test:

1) Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testWithGithubActionReporting
This should be tested in Github Actions.

/var/www/tachycardia/tests/TachycardiaTest.php:95

OK, but incomplete, skipped, or risky tests!
Tests: 14, Assertions: 21, Skipped: 1.

Generating code coverage report in Clover XML format ... done [00:00.065]

Generating code coverage report in HTML format ... done [00:01.408]
```

## Installation

Tachycardia should only be installed as a development-time dependency to aid in
running your project's test suite. You can install using [Composer](https://getcomposer.org):

    composer require --dev nexusphp/tachycardia

## Configuration

Tachycardia supports these parameters:

- **timeLimit** - Time limit in seconds to be enforced for all tests. All tests exceeding
    this amount will be considered as slow. ***Default: 1.00s***
- **reportable** - Number of slow tests to be displayed in the console report. This is ignored
    on Github Actions report. ***Default: 10***
- **precision** - Degree of precision of the decimals of the test's consumed time and allotted
    time limit. ***Default: 4***
- **tabulate** - Boolean flag whether the console report should be displayed in a tabular format
    or just displayed as plain. ***Default: false***

To use the extension with its default configuration options, you can simply add the following
into your `phpunit.xml.dist` or `phpunit.xml` file.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         cacheResultFile="build/.phpunit.cache/test-results"
         colors="true"
         executionOrder="depends,defects"
         beStrictAboutOutputDuringTests="true"
         beStrictAboutTodoAnnotatedTests="true"
         failOnRisky="true"
         failOnWarning="true"
         verbose="true">

    <!-- Your other phpunit configurations here -->

    <extensions>
        <extension class="Nexus\PHPUnit\Extension\Tachycardia" />
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
         executionOrder="depends,defects"
         beStrictAboutOutputDuringTests="true"
         beStrictAboutTodoAnnotatedTests="true"
         failOnRisky="true"
         failOnWarning="true"
         verbose="true">

    <!-- Your other phpunit configurations here -->

    <extensions>
        <extension class="Nexus\PHPUnit\Extension\Tachycardia">
            <arguments>
                <array>
                    <element key="timeLimit">
                        <double>1.00</double>
                    </element>
                    <element key="reportable">
                        <integer>10</integer>
                    </element>
                    <element key="precision">
                        <integer>4</integer>
                    </element>
                    <element key="tabulate">
                        <boolean>false</boolean>
                    </element>
                </array>
            </arguments>
        </extension>
    </extensions>
</phpunit>
```

## Documentation

### Enable/disable console reporting using environment variable

Tachycardia is configured to hook into PHPUnit once it is included in your XML file. You can, however,
control this behavior by introducing the `TACHYCARDIA_MONITOR` environment variable.

#### 1. Disable in development but enable on Github Actions

Add the `env` element to your `phpunit.xml.dist` file disabling Tachycardia then enable this on Actions:

```xml
<!-- phpunit.xml.dist -->
<phpunit bootstrap="vendor/autoload.php">
    <!-- Other configurations -->

    <php>
        <env name="TACHYCARDIA_MONITOR" value="disabled" />
    </php>

    <extensions>
        <extension class="Nexus\PHPUnit\Extension\Tachycardia" />
    </extensions>
</phpunit>
```

```yaml
# your build workflow
- name: Run test suite
  run: vendor/bin/phpunit --color=always
  env:
    TACHYCARDIA_MONITOR: enabled
```

#### 2. Enable in development but disable in Github Actions

```xml
<!-- phpunit.xml.dist -->
<phpunit bootstrap="vendor/autoload.php">
    <!-- Other configurations -->

    <extensions>
        <extension class="Nexus\PHPUnit\Extension\Tachycardia" />
    </extensions>
</phpunit>
```

```yaml
# your build workflow
- name: Run test suite
  run: vendor/bin/phpunit --color=always
  env:
    TACHYCARDIA_MONITOR: disabled
```

#### 3. Disable profiling and enable only on demand

```xml
<!-- phpunit.xml.dist -->
<phpunit bootstrap="vendor/autoload.php">
    <!-- Other configurations -->

    <php>
        <env name="TACHYCARDIA_MONITOR" value="disabled" />
    </php>

    <extensions>
        <extension class="Nexus\PHPUnit\Extension\Tachycardia" />
    </extensions>
</phpunit>
```

When running `vendor/bin/phpunit` either from the terminal or from Github Actions, just pass the variable
like this:

```sh
$ TACHYCARDIA_MONITOR=enabled vendor/bin/phpunit
```

### Setting custom time limits per test

There will be instances that execution times of some tests will definitely exceed the configured time limit.
To prevent false positives, it is possible to provide these long-running tests with their own time limits.

You can simply annotate the test method with `@timeLimit` followed by the number of seconds (figures only).
This can be higher or lower than the default time limit and will be used instead.

```php
/**
 * This test will have a time limit of 5 seconds instead of
 * the default 1 second.
 *
 * @timeLimit 5.0
 */
public function testLongRunningCodeBeingTested(): void
{
    // Logic of long running code
}

```

### Enable/disable profiling in Github Actions

Profiling in development for the Github Actions is **disabled** by default because the console cannot
interpret the special workflow commands used by Github Actions. Using the `TACHYCARDIA_MONITOR_GA`
variable, you can enable it by exporting `TACHYCARDIA_MONITOR_GA=enabled`. To disable, just export
`TACHYCARDIA_MONITOR_GA=disabled`.

The steps here are similar to above procedures for setting `TACHYCARDIA_MONITOR` variable.

### Tabulating results instead of plain render

If you want to have the console report displayed in tables, you can set the `tabulate` option to true
in the `phpunit.xml.dist` file.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php">
...
    <extensions>
        <extension class="Nexus\PHPUnit\Extension\Tachycardia">
            <arguments>
                <array>
                    ...
                    <element key="tabulate">
                        <boolean>true</boolean>
                    </element>
                </array>
            </arguments>
        </extension>
    </extensions>
</phpunit>
```

Running `vendor/bin/phpunit` will now yield the report similar to this:

```sh
$ vendor/bin/phpunit
PHPUnit 9.5.3 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.0.3 with Xdebug 3.0.3
Configuration: /var/www/tachycardia/phpunit.xml.dist

....S.........                                                    14 / 14 (100%)

Nexus\PHPUnit\Extension\Tachycardia identified these 7 slow tests:
+-----------------------------------------------------------------------------------------------+---------------+---------------+
| Test Case                                                                                     | Time Consumed | Time Limit    |
+-----------------------------------------------------------------------------------------------+---------------+---------------+
| Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testWithProvider with data set \"slowest\" | 00:00:07.0053 | 00:00:01.0000 |
| Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testWithProvider with data set \"slower\"  | 00:00:06.0110 | 00:00:01.0000 |
| Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testWithProvider with data set \"slow\"    | 00:00:05.0114 | 00:00:01.0000 |
| Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testSlowestTest                            | 00:00:04.0176 | 00:00:01.0000 |
| Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testSlowerTest                             | 00:00:03.0104 | 00:00:01.0000 |
| Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testSlowTest                               | 00:00:02.0107 | 00:00:01.0000 |
| Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testCustomLowerLimit                       | 00:00:01.0186 | 00:00:00.5000 |
+-----------------------------------------------------------------------------------------------+---------------+---------------+


Time: 00:31.574, Memory: 8.00 MB

There was 1 skipped test:

1) Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testWithGithubActionReporting
This should be tested in Github Actions.

/var/www/tachycardia/tests/TachycardiaTest.php:95

OK, but incomplete, skipped, or risky tests!
Tests: 14, Assertions: 21, Skipped: 1.

Generating code coverage report in Clover XML format ... done [00:00.526]

Generating code coverage report in HTML format ... done [00:10.317]
```

### Rerunning slow tests to see if these are fast now

After Tachycardia exposes the slow tests in your test suite, you can now start to investigate further
on why these tests run so slow. You may optionally use a profiler like XDebug for this purpose. After
_fixing_ these slow tests, you may check directly whether these tests now run within your set time limits.

Simply copy the name of the test case and the associated data set, if any. Then paste and pass this as the
value to PHPUnit's `--filter` option.

1. Copy the name of the test class and method and the associated data set, if it uses data providers.

```
⚠  Took 5.0216s from 1.0000s limit to run Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testWithProvider with data set \"slow\"
```

2. Paste it as the value to the `--filter` option:

```
vendor/bin/phpunit --filter 'Nexus\\PHPUnit\\Extension\\Tests\\TachycardiaTest::testWithProvider with data set \"slow\"'
```

Note that PHPUnit uses single quotes for the value of the `--filter` option. Read more on
the [`--filter` option documentation](https://phpunit.readthedocs.io/en/9.5/textui.html?highlight=filter)
for all supported matching patterns.

## Contributing

Contributions are very much welcome. If you see an improvement or bug fix,
open a [PR](https://github.com/NexusPHP/tachycardia/pulls) now!

## Inspiration

Tachycardia was inspired from [`johnkary/phpunit-speedtrap`](https://github.com/johnkary/phpunit-speedtrap),
but injected with anabolic steroids.

Tachycardia is actually a [medical term](https://www.webmd.com/heart-disease/atrial-fibrillation/what-are-the-types-of-tachycardia)
referring to a heart rate that exceeds the normal resting rate in general of over 100 beats per minute.

## License

This library is licensed under the [MIT License](LICENSE).
