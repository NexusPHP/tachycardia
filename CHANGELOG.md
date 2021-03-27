# Changelog

All notable changes to this library will be documented in this file.

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.1.0](https://github.com/NexusPHP/tachycardia/compare/v1.0.0...v1.1.0) - 2021-03-27

- Fixed correct line number rendering in Github Actions ([\#3](https://github.com/NexusPHP/tachycardia/pull/3))
- Fixed initial release date ([\#4](https://github.com/NexusPHP/tachycardia/pull/4))
- Added ability to supply class-level time limit annotations ([\#5](https://github.com/NexusPHP/tachycardia/pull/5))
- Added ability to disable time limits on a per-class or per-method level ([\#6](https://github.com/NexusPHP/tachycardia/pull/6))

## [v1.0.0](https://github.com/NexusPHP/tachycardia/releases/tag/v1.0.0) - 2021-03-21

Initial release.

Core classes:
- `Nexus\PHPUnit\Extension\GitHubMonitor` - Accessory class to print warnings in Github Actions.
- `Nexus\PHPUnit\Extension\Tachycardia` - The actual PHPUnit extension.
