<?php
/**
 * Configuration.php
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
/**
 * Configuration.php
 * Copyright (c) 2020 james@firefly-iii.org
 *
 * This file is part of the Firefly III CSV importer
 * (https://github.com/firefly-iii/csv-importer).
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

namespace App\Services\Configuration;

use Log;

/**
 * Class Configuration
 */
class Configuration
{
    /** @var int */
    public const VERSION = 1;
    /** @var bool */
    private $addImportTag;
    /** @var array */
    private $doMapping;
    /** @var bool When set to true, the importer will ignore existing duplicate transactions found in Firefly III. */
    private $ignoreDuplicateTransactions;
    /** @var array */
    private $mapping;
    /** @var bool */
    private $rules;
    /** @var bool */
    private $skipForm;
    /** @var bool */
    private $skipKey;
    /** @var int */
    private $version;

    /**
     * Configuration constructor.
     */
    private function __construct()
    {
        $this->ignoreDuplicateTransactions = true;
        $this->rules                       = true;
        $this->skipForm                    = false;
        $this->skipKey                     = false;
        $this->addImportTag                = true;
        $this->mapping                     = [];
        $this->doMapping                   = [];
        $this->version                     = self::VERSION;
    }

    /**
     * @param array $array
     *
     * @return static
     */
    public static function fromArray(array $array): self
    {
        $version                             = $array['version'] ?? 1;
        $object                              = new self;
        $object->ignoreDuplicateTransactions = $array['ignore_duplicate_transactions'] ?? false;
        $object->rules                       = $array['rules'] ?? true;
        $object->skipForm                    = $array['skip_form'] ?? false;
        $object->skipKey                     = $array['skip_key'] ?? false;
        $object->addImportTag                = $array['add_import_tag'] ?? true;
        $object->mapping                     = $array['mapping'] ?? [];
        $object->doMapping                   = $array['do_mapping'] ?? [];
        $object->version                     = $version;

        return $object;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public static function fromFile(array $data): self
    {
        Log::debug('Now in Configuration::fromFile', $data);
        $version = $data['version'] ?? 1;

        return self::fromArray($data);
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    public static function fromRequest(array $array): self
    {
        $object                              = new self;
        $object->version                     = self::VERSION;
        $object->ignoreDuplicateTransactions = $array['ignore_duplicate_transactions'];
        $object->rules                       = $array['rules'];
        $object->skipForm                    = $array['skip_form'];
        $object->skipKey                     = $array['skip_key'];
        $object->addImportTag                = $array['add_import_tag'] ?? true;
        $object->mapping                     = $array['mapping'];
        $object->doMapping                   = $array['do_mapping'];

        return $object;
    }

    /**
     * @return array
     */
    public function getDoMapping(): array
    {
        return $this->doMapping ?? [];
    }

    /**
     * @param array $doMapping
     */
    public function setDoMapping(array $doMapping): void
    {
        $this->doMapping = $doMapping;
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping ?? [];
    }

    /**
     * @param array $mapping
     */
    public function setMapping(array $mapping): void
    {
        $this->mapping = $mapping;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles ?? [];
    }

    /**
     * @return bool
     */
    public function isAddImportTag(): bool
    {
        return $this->addImportTag;
    }


    /**
     * @return bool
     */
    public function isIgnoreDuplicateTransactions(): bool
    {
        return $this->ignoreDuplicateTransactions;
    }

    /**
     * @return bool
     */
    public function isRules(): bool
    {
        return $this->rules;
    }

    /**
     * @return bool
     */
    public function isSkipForm(): bool
    {
        return $this->skipForm;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param bool $skipKey
     */
    public function setSkipKey(bool $skipKey): void
    {
        $this->skipKey = $skipKey;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'ignore_duplicate_transactions' => $this->ignoreDuplicateTransactions,
            'rules'                         => $this->rules,
            'skip_form'                     => $this->skipForm,
            'skip_key'                      => $this->skipKey,
            'add_import_tag'                => $this->addImportTag,
            'do_mapping'                    => $this->doMapping,
            'mapping'                       => $this->mapping,
            'version'                       => $this->version,
        ];
    }


}
