![FilamentFathomWidget.png](https://raw.githubusercontent.com/devlogx/filament-fathom-dashboard-widget/main/art/FilamentFathomWidget.png)
# Filament Fathom Dashboard Widget

[![Latest Version on Packagist](https://img.shields.io/packagist/v/devlogx/filament-fathom-dashboard-widget.svg?style=flat-square)](https://packagist.org/packages/devlogx/filament-fathom-dashboard-widget)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/devlogx/filament-fathom-dashboard-widget/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/devlogx/filament-fathom-dashboard-widget/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/devlogx/filament-fathom-dashboard-widget/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/devlogx/filament-fathom-dashboard-widget/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/devlogx/filament-fathom-dashboard-widget.svg?style=flat-square)](https://packagist.org/packages/devlogx/filament-fathom-dashboard-widget)

This package allows you to integrate a simple analytics dashboard widget for panel.

## Screenshots
![filament_fathom_light.jpg](https://raw.githubusercontent.com/devlogx/filament-fathom-dashboard-widget/main/art/filament_fathom_light.jpg)
![filament_fathom_dark.jpg](https://raw.githubusercontent.com/devlogx/filament-fathom-dashboard-widget/main/art/filament_fathom_dark.jpg)

## Installation

You can install the package via composer:

```bash
composer require devlogx/filament-fathom-dashboard-widget
```

Get the Fathom API-Token and add it your `env` file.
1. Visit the [Fathom "API" settings page](https://app.usefathom.com/api).
2. Login and click "Create New".
3. Give your token a name.
4. Select under Permissions "Site-specific key".
5. Select your Site and below "Read".
6. Add the copied API-Token to your `.env` file:

```bash
# ...
FATHOM_API_TOKEN="xxxxx|xxxxxxxxxxxxxxxxxxx"
FATHOM_SITE_ID="XXXXXXX"
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-fathom-dashboard-widget-config"
```

Optionally, you can publish the translations using

```bash
php artisan vendor:publish --tag="filament-fathom-dashboard-widget-translations"
```

This is the contents of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Fathom API-Token & Site id
    |--------------------------------------------------------------------------
    |
    | You can acquire your API-Token from the url below:
    | https://app.usefathom.com/api
    |
    */
    'api_token' => env('FATHOM_API_TOKEN'),
    'site_id' => env('FATHOM_SITE_ID'),

    /*
    |--------------------------------------------------------------------------
    | Fathom Domain
    |--------------------------------------------------------------------------
    |
    | If you're from the EU, I can recommend using the EU CDN:
    | cdn-eu.usefathom.com
    |
    */
    'domain' => env('FATHOM_DOMAIN', 'cdn.usefathom.com'),

    /*
    |--------------------------------------------------------------------------
    | Stats cache ttl
    |--------------------------------------------------------------------------
    |
    | This value is the ttl for the displayed dashboard
    | stats values. You can increase or decrease
    | this value.
    |
    */
    'cache_time' => 300,
];
```

## Usage

### Create own Dashboard file
Under `Filament/Pages/` create a new file called `Dashboard.php` with following contents:
```php
<?php

namespace App\Filament\Pages;

use Devlogx\FilamentFathom\Concerns\HasFilter;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFilter;
    
}
```

#### Remove the default Dashboard from your PanelProvider
```php
->pages([
    //Pages\Dashboard::class,
])
```
Alternatively if you already have a custom Dashboard, add the `HasFilter` trait to your Dashboard file.

### Add the Widget to your PanelProvider

```php
->widgets([
    Widgets\AccountWidget::class,
    Widgets\FilamentInfoWidget::class,
    \Devlogx\FilamentFathom\Widgets\FathomStatsWidget::class,// <-- add this widget
])
```

### Add the plugin to your PanelProvider

```php
->plugins([
    \Devlogx\FilamentFathom\FilamentFathomPlugin::make()
])
```

### Configure the plugin

```php
->plugins([
    \Devlogx\FilamentFathom\FilamentFathomPlugin::make()
        ->fathomLink(true) //Direct link to fathom analytics page
        ->pollingInterval("60s") //Auto polling interval
        ->filterSectionIcon("heroicon-s-adjustments-vertical")
        ->filterSectionIconColor("primary")
        ->liveVisitorIcon("heroicon-s-user") //First Block | Live Visitors
        ->liveVisitorColor("primary") //First Block | Live Visitors
        ->visitorsIcon("heroicon-s-user-group") //Second Block | All Visitors
        ->visitorsColor("primary") //Second Block | All Visitors
        ->viewsIcom("heroicon-s-eye") //Third Block | All Page Views
        ->visitorsColor("primary") //Third Block | All Page Views
        ->sessionTimeIcon("heroicon-s-clock") //Fourth Block | Avg. Session Time
        ->sessionTimeColor("primary") //Fourth Block | Avg. Session Time
])
```

## Using the raw Analytics functions
You can use the functions for your own widgets. There are plenty more available.

### Get Dashboard link
```php

use Devlogx\FilamentFathom\Facades\FilamentFathom;

$dashboardLink = FilamentFathom::getDashboardLink();
```

### Defining the Filter
```php
use Devlogx\FilamentFathom\Concerns\Filter;

$filter = (new Filter())
    ->setFrom(Carbon::now()->subDays(30))
    ->setTo(Carbon::now());
```

### Get different data
```php
use Devlogx\FilamentFathom\Facades\FilamentFathom;

//Get active visitors
$activeVisitors = FilamentFathom::activeVisitors($filter,false);

//Get avg session duration
$sessionDuration = FilamentFathom::sessionDuration($filter,false);

//Get visitors
$visitors = FilamentFathom::visitors($filter,false);

//Get page views
$views = FilamentFathom::views($filter,false);
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Develogix Agency](https://github.com/devlogx)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
