# Magento - Clean up session extension

## Overview

This extension allows you to schedule a cleanup of Magento session storage (only files and database are supported).

Convenient when you can not edit php.ini (to configure session garbage collector) or do not want to use a scheduled bash script.

## Compatibility

Tested on Magento CE 1.6 - 1.9

## Notes

* Free and open source
* Fully configurable
* Bundled with English, French and Dutch (thanks to Michel van de Wiel) translations

## Installation

No Magento files will be modified, no extended class, no overridden method.

### With modman

* ```$ modman clone https://github.com/aurmil/magento-cleanup-session.git```

### Manually

* Download the latest version of this module [here](https://github.com/aurmil/magento-cleanup-session/archive/master.zip)
* Unzip it
* Move the "app" folder into the root directory of your Magento application, it will be merged with the existing "app" folder

### With composer

* Adapt the following "composer.json" file into yours:

```
{
    "require": {
        "aurmil/magento-cleanup-session": "dev-master"
    },
    "repositories": [
        {
            "type": "composer",
            "url": "http://packages.firegento.com"
        },
        {
            "type": "vcs",
            "url": "git://github.com/aurmil/magento-cleanup-session"
        }
    ],
    "extra": {
        "magento-root-dir": "./"
    }
}
```

* Install or update your composer project dependencies

## Usage

In __System > Configuration > Advanced > System__, this extension adds a new group: __Session Cleaning__.

![](docs/images/cleanup-session-cron-config.png)

Default values enable session cleanup task, daily at midnight.

You are free to change these settings and set an email address to receive log email if an error occurs.

Note: when removing this module, the scheduled task remains. Remove it from DB (__core_config_data__ table, remove entry where __path__ = __crontab/jobs/aurmil_session_clean/schedule/cron_expr__) or disable it in admin if you have [AOE Scheduler module](https://github.com/AOEpeople/Aoe_Scheduler).

## License

The MIT License (MIT). Please see [License File](https://github.com/aurmil/magento-cleanup-session/blob/master/LICENSE.md) for more information.
