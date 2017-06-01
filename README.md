# Afip Public API Client

Small lib to query Afip (Argentina) public api for persons data.

## Quick Start

**Installation**
```
$ composer require msantang/afip-public-api
```

**Using**
```
<?php
use Msantang\AfipPublicApi\Exceptions\Exception;
use Msantang\AfipPublicApi\Exceptions\NotFoundException;
require 'vendor/autoload.php';

$httpClient = new \GuzzleHttp\Client();
$var = new Msantang\AfipPublicApi\Client($httpClient);
try {
    $r = $var->persona("20222222229");
    print_r($r);
} catch (NotFoundException $e) {
    echo 'Persona no encontrada: '.$e->getMessage();
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage();
}
```

## Features
* PSR-4 autoloading compliant structure
* Unit-Testing with PHPUnit

## TODO
* Finish documentation
* More testing
* Add more api methods
