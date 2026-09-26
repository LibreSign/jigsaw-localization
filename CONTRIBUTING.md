# Contributing

Contributions should preserve the package's role as a generic Jigsaw localization library.

## Development setup

Install dependencies:

```bash
composer install
```

Development tools are isolated with Composer Bin Plugin under `vendor-bin/`.

## Validation

Run the complete validation suite:

```bash
composer ci
```

Or run checks individually:

```bash
composer test
composer analyse
composer complexity
vendor-bin/pint/vendor/bin/pint --test
```

Changes should include tests for modified behavior. Public API compatibility should be preserved unless a breaking release is intentional.

Translation-platform-specific workflow belongs in consuming projects rather than this package.

Commits must comply with the repository's DCO requirements.
