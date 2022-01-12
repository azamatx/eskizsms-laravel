# Eskiz.uz SMS API package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/azamatx/eskizsms-laravel.svg?style=flat-square)](https://packagist.org/packages/azamatx/eskizsms-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/azamatx/eskizsms-laravel.svg?style=flat-square)](https://packagist.org/packages/azamatx/eskizsms-laravel)

Simple Eskiz SMS (https://eskiz.uz/sms) API client for Laravel. Used to manage SMS verification. Sends generated random pin code to given mobile phone number and verifies the pin code. You have to subscribe to Eskiz before using this package.

Eskiz SMS API developer documentation can be found here: https://documenter.getpostman.com/view/663428/RzfmES4z?version=latest

## Installation

You can install the package via composer:

```bash
composer require azamatx/eskizsms-laravel
```

## Usage

Publish configuration file:

```bash
php artisan vendor:publish
```

Register the provider in `config/app.php`:
```php
'providers' => [
	// ...
	Azamatx\EskizsmsLaravel\EskizsmsLaravel::class,
	// ...
]
```

Include the package class in your controller:

```php
use EskizsmsLaravel;

class MyController extends Controller
{
	// controller methods here...
}
```

Send pin code:

```php
// you may enter number with or without "+" (plus) sign
$status = EskizsmsLaravel::sendPin('998901234567');

if(true === $status) {
	// SMS has been sent!
}
```

Validate pin code from SMS:

```php
$pin_correct = EskizsmsLaravel::validatePin($user_provided_pin);

if(true === $pin_correct) {
	// user entered correct pin code!
}
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email info@azamat.uz instead of using the issue tracker.

## Credits

-   [Azamat Xodjakov](https://github.com/azamatx)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
