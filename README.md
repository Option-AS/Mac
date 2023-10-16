# Value Object to represent a MAC address

Read more about MAC adresses: https://en.wikipedia.org/wiki/MAC_address

Supports a good number of input and output formats; hex strings, integer, bytes

Extracts various information about the mac adress:
* isUnicast()
* isMulticast()
* isUniversal()
* isLocal()

## Requirements

- PHP >= 8.1

## Installation

Via Composer

```bash
$ composer require option/mac
```

## Useage

```php
use Option\Mac\Mac;

// Any format will do; only hex digits is considered
$mac = Mac::factory("1234.5678.90AB");

// Output in various formats:
echo $mac;             // 01:23:45:67:89:AB
echo $mac->asColon();  // 01:23:45:67:89:AB
echo $mac->asDot();    // 0123.4567.89AB
echo $mac->asIEE802(); // 01-23-45-67-89-AB
echo $mac->asDash();   // 01-23-45-67-89-AB

// Keep the OUI but zero out the NIC.
echo $mac->vendor();   // 01-23-45-00-00-00
```

## License

The GNU Lesser General Public License (LGPL-3.0-or-later). Please see [License File](COPYING) for more information.