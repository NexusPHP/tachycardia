# Tabulating results instead of plain render

If you want to have the console report displayed in tables, you can set the `format` option to `table`
in the `phpunit.xml.dist` file.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php">
...
    <extensions>
        <bootstrap class="Nexus\PHPUnit\Tachycardia\TachycardiaExtension">
            ...
            <parameter name="format" value="table" />
        </bootstrap>
    </extensions>
</phpunit>
```

Running `vendor/bin/phpunit` will now yield the report similar to this:

```console
$ vendor/bin/phpunit
PHPUnit 9.5.3 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.10 with Xdebug 3.2.2
Configuration: /var/www/tachycardia/phpunit.xml.dist
Random Seed:   1698146158

................................................................. 65 / 96 ( 67%)
...............................                                   96 / 96 (100%)

Nexus\PHPUnit\Tachycardia\TachycardiaExtension identified this sole slow test:
+---------------------------------------------------------------------------------------------+---------------+---------------+
| Test Case                                                                                   | Time Consumed | Time Limit    |
+---------------------------------------------------------------------------------------------+---------------+---------------+
| Nexus\\PHPUnit\\Tachycardia\\Tests\\Renderer\\GithubRendererTest::testRendererWorksProperly | 00:00:07.0053 | 00:00:01.0000 |
+---------------------------------------------------------------------------------------------+---------------+---------------+

Slow tests: Time: 00:00:01.710 (2.54%)

Time: 00:58.737, Memory: 16.00 MB

OK (96 tests, 265 assertions)

Generating code coverage report in Clover XML format ... done [00:00.391]

Generating code coverage report in HTML format ... done [00:01.930]
```
