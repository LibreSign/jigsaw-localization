# Jigsaw localization

![Packagist Version](https://img.shields.io/packagist/v/elaborate-code/jigsaw-localization?label=Version&style=plastic)
![Packagist Downloads](https://img.shields.io/packagist/dt/elaborate-code/jigsaw-localization?label=Downloads&style=plastic)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/elaborate-code/jigsaw-localization/run-tests?label=Tests)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/elaborate-code/jigsaw-localization/Fix%20PHP%20code%20style%20issues?label=Code%20Style)

![banner](https://banners.beyondco.de/Jigsaw%20Localization.png?theme=dark&packageManager=composer+require&packageName=elaborate-code%2Fjigsaw-localization&pattern=jigsaw&style=style_1&description=Brings+localization+feature+to+%22tightenco%2Fjigsaw%22+using+JSON+files&md=1&showWatermark=0&fontSize=100px&images=globe)

This package is built on top of [PHP JSON tongue](https://github.com/elaborate-code/php-json-tongue) to bring localization feature to [tightenco/jigsaw](https://jigsaw.tighten.com/) using JSON files.

## Get started

### Requirements

- PHP 8.0 or higher.

### Setup

Install the package using composer:

```text
composer require elaborate-code/jigsaw-localization
```

Plug `LoadLocalization` to the builder by registering it in `bootstrap.php`:

```php
<?php

// bootstrap.php

use ElaborateCode\JigsawLocalization\LoadLocalization;

$events->beforeBuild([LoadLocalization::class]);
```

### Simple usage

#### Defining Translation Strings

1. Create a `lang` folder in the root of your project.
2. Create subfolders for each language/locale.
3. Populate the subfolders with JSON files that hold translations using the `original text` as a `key`, and the `translation` as a `value`.

File structure example:

![example](https://raw.githubusercontent.com/elaborate-code/php-json-tongue/main/illustration.png)

#### Retrieving Translation Strings

Source example:

```php
<h2> {{ __($page, "Good morning", 'en') }} </h2>

<h2> {{ __($page, "programmer", 'es') }} </h2>

<h2> {{ __($page, "Good morning", 'fr') }} </h2>
```

The output:

```html
<h2> Good morning </h2>

<h2> programador </h2>

<h2> Bonjour </h2>
```

#### Locale code format

`two or three lowercase letters` for the language code + **optionally** `a dash (-) with two uppercase letters` for the region code. For example, all the following codes `ar`, `es`, `fr-CA`, `haw-US` are considered valid.

- [ISO 639-1](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) or [ISO 639-2](https://www.loc.gov/standards/iso639-2/php/English_list.php) for language codes.
- [ISO 3166](https://www.iso.org/obp/ui/#search) for region codes.

## The multi folder

For organizational purpose you can group internationalized translations in one JSON using many `locale` keys.

```text
/lang
    ...
    /multi
        greetings.json
        projects_short_descriptions.json
        ...
```

`greetings.json` example:

```json
{
    "fr": {
        "Hello": "Salut",
        "Goodbye": "Au revoir"
    },
    "es": {
        "Hello": "Hola",
        "Goodbye": "Adiós"
    }
}
```

> First level keys must be locale codes!

## Using folder structure for locale code prefix

### The default locale

First you need to define `defaultLocale` in `config.php`. If not set, the package will take `en` as a default.

```php
<?php

// config.php

return [
    // ...
    'defaultLocale' => 'es',
    // ...
];
```

### The translation helper

If you call the `__` helper without providing a `locale` parameter, it will try to resolve it from the page path.

```php
echo __($page, $text);
```

> If you provide the `__` helper with the `locale` parameter it will proceed with it and ignore the folder structure.

### The folder structure

> domain.com/{locale}/path

Pages that reside in the web root folder `source` are assumed to be rendered using the `defaultLocale`. Other pages that reside in **subfolders named after a locale code** have their **locale** set to the **subfolder name**

```text
/source
    /fr
        index.blade.php
        contact.blade.php
        about.blade.php
        ...
    /es
        index.blade.php
        contact.blade.php
        about.blade.php
        ...
    ...
    index.blade.php
    contact.blade.php
    about.blade.php
    ...
```

## The included page trick

You may find your self creating a fully coded `source/index.blade.php` and repeating the same code in `source/fr/index.blade.php` and for other locales. To avoid that we suggest the following approach:

1. Create a `source/_pages` directory which will contain the master pages.
2. A master page will look like any other ordinary page, _it will have the HTML structure and calls to `__` but no hardcoded `$current_locale` value_ .For example You may directly copy the content of `source/index.blade.php` to `source/_pages/index.blade.php`.
3. **Include** the master page into other pages that are locale aware.
4. The included content will be able to know which **locale** to apply on the translation helper `__` calls as a `$current_locale`.

```text
/source
    /_pages
        index.blade.php
        contact.blade.php
        ...
    /fr
        index.blade.php
        contact.blade.php
        ...
    index.blade.php
    contact.blade.php
    ...
```

```php
// Both /source/index.blade.php and /source/fr/index.blade.php
@include('_pages.index')
```

## Helpers

> IMPORTANT: All the following helpers will try to resolve the locale code from the path if needed!

> Setting `baseUrl` in the **config** is essential if your site root URL isn't 'domain.com/index.html'

### current_path_locale

Returns the **current page locale** deduced from its path.

```php
current_path_locale($page) // ar | es | fr-CA | haw-US
```

Usage example:

```php
<!DOCTYPE html>
<html lang="{{ current_path_locale($page) }}">
    <head>
    <!-- ... -->
```

### translate_path

When you have a page that is available in many locales. `translate_path` helps you get the equivalent translated `path`.

```php
translate_path($page, $target_locale)
```

input/output examples:

| current path  | translated path  | current_locale to target_locale |
| ------------- | ---------------- | ------------------------------- |
| "/"           | "/fr"            | default -> fr                   |
| "/contact"    | "/fr/contact"    | default -> fr                   |
| "/fr"         | "/"              | fr -> default                   |
| "/fr/contact" | "/contact"       | fr -> default                   |
| "/es/contact" | "/fr-CA/contact" | es -> fr-CA                     |
| "/es"         | "/fr-CA"         | es -> fr-CA                     |

Usage example:

```php
<nav>
    @foreach(['en', 'es', 'fr'] as $locale)
        <a href="{{ translate_path($page, $locale) }}"> {{ $locale }} </a>
    @endforeach
</nav>
```

### translate_url

Just like the `translate_path` helper, but it prepends the `baseUrl` if set in the config.

```php
translate_url($page, $target_locale)
```

### locale_path

To avoid hard coding the `current_locale` into `paths`, input only the partial path that comes after the `locale code` part into this helper and it will handle the rest for you.

```php
locale_path($page, $partial_path)
```

| $partial_path | current_locale | href          |
| ------------- | -------------- | ------------- |
| "/"           | DEFAULT        | "/"           |
| "/"           | "fr"           | "/fr"         |
| "/contact"    | DEFAULT        | "/contact"    |
| "/contact"    | "fr"           | "/fr/contact" |

### locale_url

Just like the `locale_path` helper, but it prepends the `baseUrl` if set in the config.

```php
locale_url($page, $partial_path)
```

## Live test

Wanna see a project that is up and running with this library? checkout this [repo](https://github.com/elaborate-code/it-company-website)

## TODO

- Test behavior with non A-Z languages.
- Add a router with named routes
  - Allow custom route patterns (for example set /blog/{locale}/)

## Contributing

Any help is very welcomed, feel free to fork and PR :)


## Working with translation catalogs

The package also provides small, tool-agnostic building blocks for projects that need to collect, persist, or validate JSON translation catalogs. These APIs do not require any translation service and work equally well with manually maintained files or external localization tools.

### Collecting canonical source strings

```php
use ElaborateCode\JigsawLocalization\Catalog\SourceStringCollector;

$collector = new SourceStringCollector('en');

$collector->collect('en', 'Hello');
$collector->collect('pt-BR', 'Olá'); // ignored because it is not the source locale

$sourceStrings = $collector->all();
// ['Hello' => 'Hello']
```

The collector only accepts strings from the configured source locale, deduplicates them, and returns a deterministic, sorted source catalog.

### Reading and writing JSON catalogs

```php
use ElaborateCode\JigsawLocalization\Catalog\JsonTranslationCatalog;

$catalog = new JsonTranslationCatalog('lang/en/main.json');

$catalog->write([
    'Hello' => 'Hello',
    'Goodbye' => 'Goodbye',
]);

$translations = $catalog->read();
```

Writes are deterministic, preserve Unicode and slashes, use pretty-printed JSON, and avoid rewriting an unchanged file.

### Validating translation placeholders

```php
use ElaborateCode\JigsawLocalization\Catalog\TranslationCatalogValidator;

$validator = new TranslationCatalogValidator();

$errors = $validator->validatePlaceholders(
    ['%s signed %d documents' => '%s signed %d documents'],
    ['%s signed %d documents' => '%2$d documents signed by %1$s'],
);
```

Positional printf placeholders are supported, so translators can safely reorder arguments. Missing and additional translation keys are intentionally left to project policy.

### Custom translation directory

The default `LoadLocalization` behavior remains unchanged and loads `/lang`. Projects can now point it at another directory:

```php
use ElaborateCode\JigsawLocalization\LoadLocalization;

$loader = new LoadLocalization('/path/to/translations');
$translations = $loader->load();
```

When registered as a Jigsaw listener, the default usage remains backward compatible:

```php
$events->beforeBuild([LoadLocalization::class]);
```


### Extracting source strings

Extraction is split into two layers:

1. a `TranslationSource` describes where content came from and which locale it belongs to;
2. one or more `TranslationStringExtractor` implementations know how to find strings inside that source.

The `ExtractionPipeline` owns the source-locale boundary. Sources from any other locale are ignored before an extractor is invoked, which prevents translated content from being fed back into the canonical source catalog.

```php
use ElaborateCode\JigsawLocalization\Extraction\CallbackStringExtractor;
use ElaborateCode\JigsawLocalization\Extraction\ExtractionPipeline;
use ElaborateCode\JigsawLocalization\Extraction\TranslationSource;

$extractor = new CallbackStringExtractor(
    fn (TranslationSource $source): bool => str_ends_with($source->identifier(), '.md'),
    fn (TranslationSource $source): array => [$source->contents()],
);

$pipeline = new ExtractionPipeline('en', [$extractor]);

$catalog = $pipeline->catalog([
    new TranslationSource('en', 'posts/hello.md', 'Hello'),
    new TranslationSource('pt-BR', 'posts/ola.md', 'Olá'),
]);

// ['Hello' => 'Hello']
```

For reusable integrations, implement `TranslationStringExtractor` directly. The callback adapter is intended for small project-specific adapters where adding a dedicated class would add little value.

An extractor may yield plain strings or `ExtractedString` instances. `ExtractedString` can retain source path, line number, and an optional context label for diagnostics without changing the catalog format.

The package intentionally does not hardcode Blade, Markdown, Weblate, Transifex, or any other translation workflow. A Jigsaw project can extract from templates during a build, statically inspect source files, consume CMS content, or combine several extractors in the same pipeline.

### Synchronizing source and translated catalogs

```php
use ElaborateCode\JigsawLocalization\Catalog\TranslationCatalogSynchronizer;

$synchronizer = new TranslationCatalogSynchronizer();

$source = $synchronizer->source([
    'Hello',
    'Goodbye',
]);

$ptBr = $synchronizer->translation(
    $source,
    [
        'Hello' => 'Olá',
        'Old key' => 'Tradução antiga',
    ],
);
```

Source catalogs are rebuilt canonically as `source text => source text`. Translation catalogs preserve existing translations and obsolete keys by default. New source keys receive the source text as a fallback.

Removing obsolete translation keys is an explicit project decision:

```php
$ptBr = $synchronizer->translation($source, $ptBr, pruneObsolete: true);
```

This distinction is intentional: rebuilding the source catalog and pruning translated catalogs are different operations with different data-loss risks.

### Safe catalog paths

`TranslationCatalogLocator` builds catalog file paths while rejecting directory traversal and nested path segments. It does not enforce a particular locale standard, so projects may use names such as `pt-BR`, `zh_Hant_TW`, or their own locale convention.

```php
use ElaborateCode\JigsawLocalization\Catalog\TranslationCatalogLocator;

$locator = new TranslationCatalogLocator('lang');

$sourcePath = $locator->path('en');
$messagesPath = $locator->path('pt-BR', 'messages');
```

