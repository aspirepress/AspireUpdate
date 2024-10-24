# AspirePress Update Plugin

This plugin allows a WordPress user to automatically rewrite certain URLs and URL paths to a new URL. This is
helpful because it allows for the rewriting of `api.wordpress.org` to some other repository that contains the plugins
the user wants.

The plugin supports multiple rewrites, and also supports rewriting the URL paths of the requests on a per-host basis.
This improves the capacity of the plugin to adequately support newer or different repositories.

## Requirements

This plugin requires:

- WordPress 5.3 or later
- PHP 7.4 or later
- The ability to upload files to your WordPress installation
- The ability to modify your configuration in wp-config.php

## Installation

To install this plugin, follow these steps:

1. Download a copy of this plugin as a ZIP file from GitHub or the Releases section.
2. Go to the WordPress Dashboard (/wp-admin) section and log in if necessary as an administrator.
3. Go to the Plugins menu and click on "Add New Plugin".
4. Click the button "Upload Plugin" and upload the plugin ZIP file.
5. Activate the AspireUpdate plugin.
6. Configure the plugin if necessary with the user interface.
7. Test and enjoy!

## Configuration

The plugin menu appears under the Dashboard main menu item. Don't look for it in Settings or Tools, it won't be there :).

The plugin uses the following configuration options:

| Configuration Parameter |                                                                  Description |                        Default, if any |
| :---------------------- | ---------------------------------------------------------------------------: | -------------------------------------: |
| AP_ENABLE               |                         The API Key for AspireCloud (not currently enforced) |                                   none |
| AP_API_KEY              |                                                          Enable API rewrites |                                   true |
| AP_HOST                 |                                                            an array of hosts |           array('api.aspirecloud.org') |
| AP_DEBUG                |                                                            Enable Debug Mode |                                  false |
| AP_DEBUG_TYPES          |                                                      an array of debug modes | array('string', 'request', 'response') |
| AP_DISABLE_SSL          |                                  Disabled SSL verification for local testing |                                   true |
| AP_REMOVE_UI            | Disables plugin settings, defaults to config parameters set in wp-config.php |                                  false |

NOTE: The AspireUpdate user interface settings _will_ override any plugin options set in wp-config.php unless AP_REMOVE_UI configuration parameter is set to true.

To configure any parameters that require an array you must use a short piece of code:

```php
// Works as of PHP 7
define('AP_HOSTS', array(
    'api.aspirecloud.org'
));
```

## Debug Logging

The AspireUpdate log file is located under /wp-content and named "debug-aspire-update.log".

## Authentication

Authentication is provided by way of a randomly generated token combined with the `WP_SITEURL` constant. This token is
then Base64-encoded with the separate parts of the credentials separated by a colon. It's added to the `Authorization`
header.

## License

This plugin is licensed under the GPLv2, as it is a WordPress plugin and that is the license required.

## Contributing

Contributions are welcome. Here's a short to-do list:

- Add support for more complex rewrites. Right now we only support simple string matching, but it would be nice to support pattern matching, too.
- Add support for more complex debugging. Right now we only support outputting strings, but it would be nice to support more complex debugging.
- Add administrator panel support. We don't have support right now for configuring this plugin through the UI, which will limit its reach.
- Add support for verifying that the repository can be reached, and if not, reverting back to the original repository.
- Add support for multiple repositories, in a priority order. This would allow for multiple fallbacks.
- Add support for additional header management. Right now the plugin is designed to add a simple Authentication header, which is not always needed. Other repositories might have different authentication requirements.

## Support

If you need help with this plugin, please file an issue explaining the following:

- What you did
- What you expected
- What actually happened
- Why you think this is wrong

Issues that are not filed with this information will be closed. We will do our best to assist, but we cannot guarantee a response.
