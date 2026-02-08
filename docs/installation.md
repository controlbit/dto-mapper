# Installation

- [Requirements](#requirements)
- [Using Composer](#using-composer)
- [For Symfony users](#for-symfony-users)

## Requirements
- **PHP**: >=8.2
- **doctrine/collections**: ^2.0|^3.0 (Optional)
- **doctrine/orm**: ^2.0|^3.0 (Optional)

## Using Composer 
```bash
composer require controlbit/dto-mapper
```

## For Symfony users
Add to your `bundles.php` line:

```php
ControlBit\Dto\Bridge\Symfony\DtoBundle::class                               => ['all' => true],
```
