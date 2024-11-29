<?php

namespace App\Models\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Trait HasUniqueUrl
 *
 * This trait is designed to automatically generate a unique URL identifier for models using either
 * a ULID (Universally Unique Lexicographically Sortable Identifier) or a custom random string with
 * a date prefix. It ensures the uniqueness of the identifier by checking against existing entries in the database.
 *
 * Constants:
 * - URL_COLUMN_NAME: specifies the database column to store the unique URL identifier.
 * - GENERATE_CODE_TYPE: defines the type of code to generate ('ulid' for ULID or 'random' for random string).
 */
trait HasUnique
{
    /**
     * Boot the trait and attach model event listeners.
     * Automatically generates a unique URL identifier when creating the model if it is not set.
     */
    protected static function bootHasUniqueUrl()
    {
        static::creating(function ($model) {
            DB::transaction(function () use ($model) {
                $columnName = $model->getUrlColumnName();
                $type = $model->getGenerateCodeType();
                if (empty($model->$columnName)) {
                    $model->$columnName = static::generateUniqueCode($model, $columnName, $type);
                }
            });
        });
    }

    protected static function generateUniqueCode($model, $columnName, $type)
    {

        $maxAttempts = 10;
        $attempt = 0;

        do {
            if ($type === 'ulid') {
                $code = (string) Str::ulid(); // Generate a ULID string
            } else {
                $prefix = now()->format('ymd'); // Date prefix excluding time
                $random = strtoupper(Str::random(4));
                $code = $prefix . $random;
            }

            $attempt++;
        } while (static::existsCode($model, $columnName, $code, $type) && $attempt < $maxAttempts);

        if ($attempt == $maxAttempts) {
            throw new Exception('Unable to generate unique URL code after ' . $maxAttempts . ' attempts.');

            return null;
        }

        return $code;
    }

    protected static function existsCode($model, $columnName, $code, $type)
    {
        return $model->where($columnName, $code)->exists();
    }

    /**
     * Default implementation to define the URL column name.
     * Override in model if a different column name is needed.
     */
    public function getUrlColumnName()
    {
        return 'url';
    }

    /**
     * Default implementation to define the code generation type.
     * Override in model if a different generation type is needed.
     */
    public function getGenerateCodeType()
    {
        return 'ulid';  // 'ulid' or 'random'
    }
}
