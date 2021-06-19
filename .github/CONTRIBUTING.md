# Contributing to Nexus Tachycardia

Nexus Tachycardia is an open source project. We welcome all forms of contributions.

## Code of Conduct

To help make Nexus Tachycardia open and inclusive for all, we ask that you read and follow the [Code of Conduct](.github/CODE_OF_CONDUCT.md).

## Reporting a Bug

- Before opening a bug report, please check in the [Issues](https://github.com/NexusPHP/tachycardia/issues)
panel if the issue is neither opened nor addressed (and fixed) yet.
- Submit an [issue](https://github.com/NexusPHP/tachycardia/issues/new/choose). Describe clearly what the
issue is and the steps to reproduce.
- If the fix can be easily made for the issue, please open a pull request instead.

## Submitting Changes

- Fork the repository to your GitHub account.
- Clone your fork into your local environment.
- Create a new branch for each set of changes you want to implement.
- Before committing code, please check for unnecessary whitespaces using `git diff --check`.
- Please make commit messages fully descriptive of what you've done.
- Useless commits, like fixing of human errors, should not be having a separate commit.
Use `git commit --amend` for this purpose.
- Add test cases for your bug fixes or new features. It is recommended to add new tests
instead of amending existing ones.
- Run static analysis: `vendor/bin/phpstan analyse`.
- Run test suite: `vendor/bin/phpunit`.
- When static analysis and tests all pass, push your changes to your remote branch.
- Submit a pull request.

## Which Branch?

- All bug fixes and new features that fully backwards compatible should target the `develop` branch.
<!-- - Bug fixes or features that break BC should target the next release branch. -->

## PHP Compatibility

All PHP code submitted should be compatible with PHP 7.3+.

## Coding Style

Contributions should always adhere to the coding style standards defined in the
[`nexusphp/cs-config`](https://github.com/NexusPHP/cs-config). Before submitting a pull request,
make sure you have run the CS check: `vendor/bin/php-cs-fixer fix --verbose`.

## Signing your Commits

Make sure all commits submitted to be digitally signed by GPG. To learn more, see [GPG signing](https://help.github.com/categories/gpg/).
