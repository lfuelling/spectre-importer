<?php
declare(strict_types=1);


namespace App\Services\Local;

use Log;
use Storage;

/**
 * Class VerifyKeyMaterial
 */
class VerifyKeyMaterial
{
    /**
     *
     */
    public static function verifyOrCreate(): void
    {
        Log::debug('Going to check if key material is present.');

        $publicKey  = 'spectre_public.key';
        $privateKey = 'spectre_private.key';
        $storage    = Storage::disk('keys');
        if ($storage->has($publicKey) && $storage->has($privateKey)) {
            Log::info('Firefly III Spectre importer has correct key material.');
        }

        Log::info('Firefly III Spectre importer could not find key material. Will create new key material.');
        Log::debug('Generate new Spectre key pair for user.');
        $keyConfig = [
            'digest_alg'       => 'sha512',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        // Create the private and public key
        $res = openssl_pkey_new($keyConfig);
        // Extract the private key from $res to $privKey
        $privKey = '';
        openssl_pkey_export($res, $privKey);

        $pubKey = openssl_pkey_get_details($res);

        // store:
        $storage->put($publicKey, $pubKey['key']);
        $storage->put($privateKey, $privKey);

        Log::debug('Created key pair');
    }
}
