<?php
/**
 * VerifyKeyMaterial.php
 * Copyright (c) 2020 james@firefly-iii.org
 *
 * This file is part of the Firefly III Spectre importer
 * (https://github.com/firefly-iii/spectre-importer).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);


namespace App\Services\Local;

use App\Exceptions\ImportException;
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
            return;
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

    /**
     * @throws ImportException
     * @return string
     */
    public static function getPublicKey(): string
    {
        $publicKeyName = 'spectre_public.key';
        $storage       = Storage::disk('keys');
        if ($storage->has($publicKeyName)) {
            Log::info('Firefly III Spectre importer has a public key.');

            return $storage->get($publicKeyName);
        }
        throw new ImportException('No public key found.');
    }

    /**
     * @throws ImportException
     * @return string
     */
    public static function getPrivateKey(): string
    {
        $privateKeyName = 'spectre_private.key';;
        $storage = Storage::disk('keys');
        if ($storage->has($privateKeyName)) {
            Log::info('Firefly III Spectre importer has a private key.');

            return $storage->get($privateKeyName);
        }
        throw new ImportException('No private key found.');
    }
}
