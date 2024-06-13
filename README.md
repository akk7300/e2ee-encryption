# Laravel Response E2EE Encryption

This package provides middleware for encrypting API responses using a user's public key, and allows the frontend to decrypt the data using the user's private key.

## Installation

1. **Require the package via Composer:**
    ```bash
    composer require akk7300/e2ee-encryption
    ```

2. **Publish the configuration file:**
    ```bash
    php artisan vendor:publish --provider="Akk7300\E2eeEncryption\E2eeEncryptionServiceProvider" --tag="config"
    ```

## Configuration & Database Setup

The default configuration file config/e2ee_encryption.php looks like this:

```php
return [
    'table_name' => 'users',
    'public_key_column' => 'public_key',
    'private_key_column' => 'private_key',
];
```

your database schema should include the necessary table & columns to store user keys:

- **Table Name**: Create a table in your database with the name specified in `config/e2ee_encryption.php`.
- **Columns**:
  - `public_key`: Create a column in the table to store users' public keys.
  - `private_key`: Create a column in the table to store users' private keys.

You need to create these columns in your database schema manually.

## Usage

### Generating Key Pairs for Existing Data

To generate key pairs for existing data in your specified database table, use the following Artisan command:

```bash
php artisan generate:key-pairs
```
This command automatically creates public and private keys for existing data.

### Adding Middleware for Encrypting Response

Register the EncryptResponseMiddleware in your app/Http/Kernel.php:


```php
protected $routeMiddleware = [
    // ...
    'encrypt.response' => \Akk7300\E2eeEncryption\Middleware\EncryptResponseMiddleware::class,
];
```

Then, apply the middleware to your routes:

```php
Route::middleware(['auth', 'encrypt.response'])->group(function () {
    // ...
});
```

### Automatically Generating Key Pairs for New Data

To automatically generate key pairs for new rows, add the GenerateEncryptionKeys trait to your model. For example, in your User model:

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Akk7300\E2eeEncryption\Traits\GenerateEncryptionKeys;

class User extends Authenticatable
{
    use GenerateEncryptionKeys;

    //...
}
```

### Providing Private Key to Frontend
It's important to note that the process of providing the private key to the frontend application is not handled by this package. You, as the developer integrating this package, are responsible for securely managing and providing the private key to the frontend.

#### client side javascript example code for decryption
```php 
const crypto = require('crypto');

const privateKey = 'user private key';

const encrypted = 'encrypted data';

const buffer = Buffer.from(encrypted, 'base64');
const decrypted = crypto.privateDecrypt(
    {
        key: privateKey,
        passphrase: '',
    },
    buffer
);

const decryptedString = decrypted.toString('utf8');
const result = JSON.parse(decryptedString);

console.log(result);

```
## Packages Used

- [spatie/crypto](https://github.com/spatie/crypto) - for encryption functionality.


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Aung Khant](https://github.com/akk7300)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
