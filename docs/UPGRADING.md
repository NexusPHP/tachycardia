# UPGRADING GUIDE FROM 1.x to 2.0

This is guide for upgrading from version 1.x to 2.0.

## PHP and PHPUnit versions

v2.0 requires PHP 8.1+ and PHPUnit 10.3+

## Renamed classes

| 1.x                                 | 2.x                                            | Description         |
|-------------------------------------|------------------------------------------------|---------------------|
| Nexus\PHPUnit\Extension\Tachycardia | Nexus\PHPUnit\Tachycardia\TachycardiaExtension | The extension class |

## Renamed options

| 1.x          | 2.x            | Description                   |
|--------------|----------------|-------------------------------|
| `timeLimit`  | `time-limit`   | Default time limit in seconds |
| `reportable` | `report-count` | Number to report on console   |

## Removed options

| 1.x           | Description                                             |
|---------------|---------------------------------------------------------|
| `tabulate`    | Use `format` set to `table` instead                     |
| `collectBare` | Enabled by default due to PHPUnit's event system design |

## Removed trait/class

The following trait/class were removed as the extension now, by default, collects the time without the setup
due to the design of PHPUnit 10's event system being so granular.

- `Nexus\PHPUnit\Extension\Expeditable` trait
- `Nexus\PHPUnit\Extension\ExpeditableTestCase` abstract test case

## Change in phpunit.xml.dist `extensions` element

After migrating your configuration file to PHPUnit 10's schema using `--migrate-configuration`, you should also
change the extension registration.

- If used as-is without customization of parameters:
```diff
 <extensions>
-    <extension class="Nexus\PHPUnit\Extension\Tachycardia" />
+    <bootstrap class="Nexus\PHPUnit\Tachycardia\TachycardiaExtension" />
 </extensions>

```

- If used with changing the parameters:
```diff
 <extensions>
-    <extension class="Nexus\PHPUnit\Extension\Tachycardia">
-       <arguments>
-           <array>
-               <element key="timeLimit">
-                   <double>1.00</double>
-               </element>
-               <element key="reportable">
-                   <integer>10</integer>
-               </element>
-               <element key="precision">
-                   <integer>4</integer>
-               </element>
-               <element key="tabulate">
-                   <boolean>false</boolean>
-               </element>
-               <element key="collectBare">
-                   <boolean>false</boolean>
-               </element>
-           </array>
-       </arguments>
-    </extension>
+    <bootstrap class="Nexus\PHPUnit\Tachycardia\TachycardiaExtension">
+       <parameter name="time-limit" value="2.00" />
+       <parameter name="report-count" value="30" />
+       <parameter name="precision" value="2" />
+       <parameter name="format" value="table" />
+    </bootstrap>
 </extensions>

```
