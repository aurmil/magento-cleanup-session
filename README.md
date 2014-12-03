# Magento - Clean Up Session extension

## Overview
This extension allows you to schedule a cleanup of Magento session storage (only files and database are supported).

Convenient when you can not edit php.ini (to configure session garbage collector) or do not want to use a scheduled bash script.

## Compatibility
Tested on Magento CE 1.6 - 1.9

## Notes
* Free and open source
* Fully configurable
* Bundled with English and French translations

## Installation
Just download the "app" folder and paste it into the root directory of your Magento application. It will be merged with the existing "app" folder.

No Magento files will be modified, no extended class, no overridden method.

## Usage
In __System > Configuration > Advanced > System__, this extension adds a new group: __Session Cleaning__.

![](http://4.bp.blogspot.com/-O7uSYP1x43Q/VH811dxaMSI/AAAAAAAAR88/43metOSczQc/s1600/session-cleanup.png)

Default values enable session cleanup task, daily at midnight.

You are free to change these settings and set an email address to receive log email if an error occurs.

## Changelog
### 1.0
* Initial release
