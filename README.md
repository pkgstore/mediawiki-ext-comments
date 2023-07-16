# Information

Интеграция комментариев в статью.

## Install

1. Загрузите папки и файлы в директорию `extensions/MW_EXT_Comments`.
2. В самый низ файла `LocalSettings.php` добавьте строку:

```php
wfLoadExtension( 'MW_EXT_Comments' );
```

## Syntax

```html
{{#comments: [TYPE]|[ID]}}
```
