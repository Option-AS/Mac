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

Add to your composer.json

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Option-AS/Mac"
    }
]
```

You may need to add auth.json and edit to suit

```json
{
    "github-oauth": {
        "github.com": "ghp_ClassicTokenThatGivesReadAccess"
    }
}
```

Install it

```
composer require option/mac
```

Use it

```php
use Option\Mac\Mac;

// Any format will do; only hex digits is considered
$mac = Mac::factory("1234.5678.90AB");

echo $mac;             // 01:23:45:67:89:AB
echo $mac->asColon();  // 01:23:45:67:89:AB
echo $mac->asDot();    // 0123.4567.89AB
echo $mac->asIEE802(); // 01-23-45-67-89-AB
echo $mac->asDash();   // 01-23-45-67-89-AB
```
