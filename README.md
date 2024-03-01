# Doctrine Support for `arokettu/date`

[![Packagist]][Packagist Link]
[![PHP]][Packagist Link]
[![License]][License Link]
[![Gitlab CI]][Gitlab CI Link]
[![Codecov]][Codecov Link]

[Packagist]: https://img.shields.io/packagist/v/arokettu/date-doctrine.svg?style=flat-square
[PHP]: https://img.shields.io/packagist/php-v/arokettu/date-doctrine.svg?style=flat-square
[License]: https://img.shields.io/packagist/l/arokettu/date-doctrine.svg?style=flat-square
[Gitlab CI]: https://img.shields.io/gitlab/pipeline/sandfox/date-doctrine/master.svg?style=flat-square
[Codecov]: https://img.shields.io/codecov/c/gl/sandfox/date-doctrine?style=flat-square

[Packagist Link]: https://packagist.org/packages/arokettu/date-doctrine
[License Link]: LICENSE.md
[Gitlab CI Link]: https://gitlab.com/sandfox/date-doctrine/-/pipelines
[Codecov Link]: https://codecov.io/gl/sandfox/date-doctrine/

[``arokettu/date``](https://sandfox.dev/php/date.html) row classes and ID generators for Doctrine.

## Usage

```php
<?php

use Arokettu\Date\Date;
use Doctrine\ORM\Mapping\{Column,CustomIdGenerator,Entity,GeneratedValue,Id,Table};

#[Entity, Table(name: 'date_object')]
class DateObject
{
    #[Column(type: DateType::NAME)]
    public Date $date;
}
```

## Installation

```bash
composer require arokettu/date-doctrine
```

* Version 1.x is for `doctrine/dbal` v3
* Version 2.x is for `doctrine/dbal` v4

The versions are fully interchangeable except for hard dependency on DBAL.

## Documentation

Read full documentation for the base library here: <https://sandfox.dev/php/date.html>

Also on Read the Docs: <https://php-date.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/php-date/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [MIT License][License Link].
