# Joomla Help Site

This application is the code powering the [help.joomla.org](https://help.joomla.org) website. Its purpose is to render the help screens used in the Joomla! CMS and display a landing page for the now retired version of this site.

## Requirements

* PHP 5.4+
* Composer
* Apache with mod_rewrite enabled and configured to allow the .htaccess file to be read

## Installation

1. Clone this repo on your web server
2. Run the `composer install` command to install all dependencies
3. Copy `etc/config.dist.json` to `etc/config.json` and configure your environment

## Additional Configuration

The application optionally supports several additional configuration values which affect the application's behavior, to include:

* Caching - The `joomla/cache` package is used to provide a caching API to store data. The supported configuration values are under the `cache` key in the configuration and include:
    * `enabled` - Is the cache enabled?
    * `lifetime` - The lifetime (in seconds) of the cache data
    * `adapter` - The cache adapter to use; the currently supported values can be found in the [CacheProvider](src/Service/CacheProvider.php) 
* Error Reporting - The `system.error_reporting` configuration key can be set to a valid bitmask to be passed into the `error_reporting()` function
* Logging - The application's logging levels can be fine tuned by adjusting the `log` configuration keys:
    * `log.level` - The default logging level to use for all application loggers
    * `log.application` - The logging level to use specifically for the `monolog.handler.application` logger; defaults to the `log.level` value
* Wiki Integration - Some behaviors for the remote MediaWiki integration can be configured with the following configuration keys:
    * `help.wiki` - The base URL of the MediaWiki instance to interface with, defaults to `https://docs.joomla.org`
    * `help.wiki_max_redirects` - The number of wiki page redirects that will be followed, defaults to 5
