# DTO Mapper

A DTO Mapper library and Symfony bundle

- [Quick guide](#quick-guide)
    - [Requirements](#requirements)
    - [Using Composer](#using-composer)
- [For Symfony users](#for-symfony-users)
- [Documentation](#documentation)
- [TODO (Upcoming)](#todo-upcoming)

## Quick setup
```bash
composer require controlbit/dto-mapper
```

Add to your `bundles.php` line:

```php
ControlBit\Dto\Bridge\Symfony\DtoBundle::class                               => ['all' => true],
```

Inject `ControlBit\Dto\Contract\Mapper\MapperInterface` in your Service and Map to/from your DTOs.

Next, read [Usage](docs/usage.md) and you're good!

## Documentation

- [Installation](docs/installation.md)
- [Usage](docs/usage.md)
- [Symfony Configuration Reference](docs/configuration.md)

## TODO (Upcoming):
- Mapping UploadedFile into \SplFileInfo (could be that half of it is working already)
- Caching Mapping Metadata
