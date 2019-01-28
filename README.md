# Rollbar for Craft CMS 3.x

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

    `cd /path/to/project`

2. Add the respository to your `composer.json` file:

```json
"repositories": [
    {
      "type": "path",
      "url": "plugins/rollbar"
    },
],
```

3. Then tell Composer to load the plugin:

    `composer require enovatedesign/craft-rollbar`

4. In the Control Panel, go to Settings -> Plugins and click the “Install”
button for "Integrity".

## Configuration

Most settings can be set in the control panel or a config file. Settings
in config files override any set in the control panel.

To configure the plugin in a config file, create a `config/rollbar.php` file, for
example:

```php
<?php

return [
    '*' => [
        'enabled' => true,
        'basePath' => '@webroot',
    ],
    'dev' => [
        'enabled' => false,
    ],
];
```