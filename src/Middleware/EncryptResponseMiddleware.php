<?php

namespace Akk7300\E2eeEncryption\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Spatie\Crypto\Rsa\PublicKey;

class EncryptResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check()) {
            try {
                $publicKeyString = auth()->user()->getAttribute(config('e2ee_encryption.public_key_column'));
                $publicKey = PublicKey::fromString($publicKeyString);

                $content = $response->getContent();
                $encryptedContent = base64_encode($publicKey->encrypt($content));

                $response->setContent($encryptedContent);
            } catch (Exception $e) {
                throw new Exception('Encryption failed: '.$e->getMessage());
            }
        }

        return $response;
    }
}
