# Joomla Help Site

This application is the code powering the [help.joomla.org](https://help.joomla.org) website. Its purpose is to render the help screens used in the Joomla! CMS and display a landing page for the now retired version of this site.

## Requirements

* PHP 7.3+
* Composer
* Apache with mod_rewrite enabled and configured to allow the .htaccess file to be read

## Installation

1. Clone this repo on your web server
2. Run the `composer install` command to install all dependencies
3. Copy `etc/config.dist.json` to `etc/config.json` and configure your environment

## Additional Configuration

The application optionally supports several additional configuration values which affect the application's behavior, to include:

* Caching - The `symfony/cache` package is used to provide a caching API to store data. The supported configuration values are under the `cache` key in the configuration and include:
    * `enabled` - Is the cache enabled?
    * `lifetime` - The lifetime (in seconds) of the cache data
    * `adapter` - The cache adapter to use; the currently supported values can be found in the [CacheProvider](src/Service/CacheProvider.php)
    * `namespace` - A unique namespace (or key prefix) for the application's cache, useful if using a shared cache source with other systems
* Debug - The `system.debug` configuration key can be set to true or false to enable the application's debug mode
* Error Reporting - The `system.error_reporting` configuration key can be set to a valid bitmask to be passed into the `error_reporting()` function
* Logging - The application's logging levels can be fine tuned by adjusting the `log` configuration keys:
    * `log.level` - The default logging level to use for all application loggers
    * `log.application` - The logging level to use specifically for the `monolog.handler.application` logger; defaults to the `log.level` value
* Template - The `twig/twig` package is used for the application's templates
    * `template.debug` - Flag to enable Twig's debug functionality, when enabled the caching functionality is not available
    * `template.cache.enabled` - Flag to enable Twig's caching functionality, defaults to false
    * `template.cache.path` - The path relative to the repo root where cached Twig files should be stored, defaults to `cache/twig`
* Wiki Integration - Some behaviors for the remote MediaWiki integration can be configured with the following configuration keys:
    * `help.wiki` - The base URL of the MediaWiki instance to interface with, defaults to `https://docs.joomla.org`
    * `help.wiki_max_redirects` - The number of wiki page redirects that will be followed, defaults to 5
