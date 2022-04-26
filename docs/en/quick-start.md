# Quick Start

1. Create "phpstan.neon" in project directory. For more configuration options, see [Advanced Usage](/docs/en/advanced-usage.md).
```
includes:
    - vendor/syntro/silverstripe-phpstan/phpstan.neon
```

2. Execute from project dir:
```
vendor/bin/phpstan analyse app/src -c phpstan.neon -a vendor/symbiote/silverstripe-phpstan/bootstrap.php --level 4
```

3. Visit the [PHPStan Github](https://github.com/phpstan/phpstan) for additional information. Try adjusting the `--level`, lower = less strict, higher = more strict.
