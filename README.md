# AspirePress Updater Plugin

This plugin allows a WordPress user to automatically rewrite certain URLs and URL paths to a new URL. This is
helpful because it allows for the rewriting of `api.wordpress.org` to some other repository that contains the plugins
the user wants.

The plugin supports multiple rewrites, and also supports rewriting the URL paths of the requests on a per-host basis.
This improves the capacity of the plugin to adequately support newer or different repositories.

## Requirements

This plugin requires:

* WordPress 6.0 or later
* PHP 7.0 or later
* The ability to upload files to your WordPress installation
* The ability to modify your configuration in wp-config.php

## Installation

To install this plugin, follow these steps:

1. Download a copy of this plugin as a ZIP file.
2. Go to your wp-admin section and log in.
3. Go to the plugins section and click on "Add New Plugin".
4. Select "Upload Plugin" and upload the plugin's ZIP file.
5. Activate the plugin.
6. Configure the plugin by updating your `wp-config.php` file with the configuration options in the next section.
7. Test.

## Configuration

The plugin requires the following configuration options:

```php
<?php

define('AP_UPDATER_HOST_REWRITES', [
    'api.wordpress.org' => 'your-repo.com',
]);
```

There are other options for defining the plugin's functionality, as well. They are:

* **AP_UPDATER_API_KEY** - Provides an API key for repositories that may require authentication.
* **AP_UPDATER_REWRITE_WPORG_API** - Uses the built-in WordPress API rewrite rules. Must be configured with `AP_UPDATER_API_URL`.
* **AP_UPDATER_REWRITE_WPORG_DL** - Uses the built-in WordPress download rewrite rules. Must be configured with `AP_UPDATER_DL_URL`.
* **AP_UPDATER_API_URL** - The URL to use for the third-party plugin API. Must be configured with `AP_UPDATER_REWRITE_WPORG_API`.
* **AP_UPDATER_DL_URL** - The URL to use for the third-party plugin download API. Must be configured with `AP_UPDATER_REWRITE_WPORG_DL`.
* **AP_UPDATER_DEBUG** - Enables debug mode for the plugin.
* **AP_UPDATER_DEBUG_TYPES** - Defines the types of messages you want output as an array. Presently supports `request`, `response` and `string`
* **AP_UPDATER_DEBUG_TYPES_EXCLUDE** - Defines any types you DON'T WANT displayed. This runs AFTER the `AP_UPDATER_DEBUG_TYPES` does, so it will remove anything you previously added if both are defined.
* **AP_UPDATER_DEBUG_LOG_PATH** - Defines where to write the log. The log file name is hard-coded, but the path is up to you. File must be writable.
* **AP_UPDATER_DEBUG_SSL** - Disables the verification of SSL to allow local testing.
* **AP_UPDATER_EXAMINE_RESPONSES** - Examines the response and logs it as a debug value when set to true.
## Authentication

Authentication is provided by way of a randomly generated token combined with the `WP_SITEURL` constant. This token is
then Base64-encoded with the separate parts of the credentials separated by a colon. It's added to the `Authorization`
header.

If no API key is supplied, the API key is omitted.

## Debugging

The plugin supports debugging. To enable debugging, define the `AP_UPDATER_DEBUG` constant in your `wp-config.php` file.

There are three output options, all enabled by default:

* **request** - Outputs the request URL and headers.
* **response** - Outputs the response headers and body.
* **string** - Outputs the string that is being rewritten.

These are turned on by default but you can remove ones you don't need by defining them in the `AP_UPDATER_DEBUG_TYPES_EXCLUDE` constant.

## License

This plugin is licensed under the GPLv2, as it is a WordPress plugin and that is the license required.

## Contributing

Contributions are welcome. Here's a short to-do list:

* Add support for more complex rewrites. Right now we only support simple string matching, but it would be nice to support pattern matching, too.
* Add support for more complex debugging. Right now we only support outputting strings, but it would be nice to support more complex debugging.
* Add administrator panel support. We don't have support right now for configuring this plugin through the UI, which will limit its reach.
* Add support for verifying that the repository can be reached, and if not, reverting back to the original repository.
* Add support for multiple repositories, in a priority order. This would allow for multiple fallbacks.
* Add support for additional header management. Right now the plugin is designed to add a simple Authentication header, which is not always needed. Other repositories might have different authentication requirements.

## Support

If you need help with this plugin, please file an issue explaining the following:

* What you did
* What you expected
* What actually happened
* Why you think this is wrong

Issues that are not filed with this information will be closed. We will do our best to assist, but we cannot guarantee a response.
