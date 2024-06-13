<?php

namespace Akk7300\E2eeEncryption\Traits;

use Spatie\Crypto\Rsa\KeyPair;

trait GenerateEncryptionKeys
{
    protected static function bootGenerateEncryptionKeys()
    {
        static::creating(function ($model) {
            [$privateKey, $publicKey] = (new KeyPair())->generate();

            $publicKeyColumn = config('e2ee_encryption.public_key_storage');
            $privateKeyColumn = config('e2ee_encryption.private_key_storage');

            $model->$publicKeyColumn = $publicKey;
            $model->$privateKeyColumn = $privateKey;
        });
    }
}
