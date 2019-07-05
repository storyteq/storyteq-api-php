# Storyteq API PHP Client
A (incomplete) client for the Storyteq API.
## Installation

```
composer require storyteq/storyteq-api-php
```

## Usage
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Storyteq\Client;

$storyteq = new Client('v4', 'your-api-token');

var_dump($storyteq->readFeedData(1));
?>
```