# GcodeEstimator

GcodeEstimator is a PHP library to estimate the length/weight/cost of filament
used for a 3D print through the corresponding gcode file.

Requires PHP >= 7.2.

## Installation

Use [Composer](http://getcomposer.org/) to install GcodeEstimator in your project:

```shell
composer require "pyrech/gcode-estimator"
```

## Usage

Basic usage to get the length of filament used:

```php
include __DIR__.'/vendor/autoload.php';

use Pyrech\GcodeEstimator\Estimator;

$estimator = new Estimator();
$estimate = $estimator->estimate($absolutePathToGcode);

$estimate->getLength(); // returns the length of filament used (in mm);
```

You can also estimate the weight and cost of your print by describing the
properties of your filament spool:

```php
include __DIR__.'/vendor/autoload.php';

use Pyrech\GcodeEstimator\Estimator;
use Pyrech\GcodeEstimator\Filament;

$filament = new Filament(
    1.75, // filament diameter in mm
    1.24, // filament density in g/cm³
    750,  // weight of the spool in g
    25.99 // price of the spool (whatever your currency)
);

$estimator = new Estimator();
$estimate = $estimator->estimate($absolutePathToGcode, $filament);

$estimate->getLength(); // returns the length of filament used (in mm);
$estimate->getWeight(); // returns the weight of filament used (in g);
$estimate->getLength(); // returns the cost of filament used (in whatever currency you specified);
```

## Further documentation

You can see the current and past versions using one of the following:

* the `git tag` command
* the [releases page on Github](https://github.com/pyrech/gcode-estimator/releases)
* the file listing the [changes between versions](CHANGELOG.md)

And finally some meta documentation:

* [versioning and branching models](VERSIONING.md)
* [contribution instructions](CONTRIBUTING.md)

## Credits

* [All contributors](https://github.com/pyrech/gcode-estimator/graphs/contributors)

## License

GcodeEstimator is licensed under the MIT License - see the [LICENSE](LICENSE)
file for details.
