<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Schema;

trait HasUuidColumn
{
    use HasUuids;

    protected static function bootHasUuidColumn()
    {
        static::creating(function ($model) {
            try {
                if (empty($model->id)) {
                    $type = Schema::getColumnType($model->getTable(), 'id');
                    if (in_array($type, ['string', 'text', 'varchar'])) {
                        $model->id = (string) \Illuminate\Support\Str::orderedUuid();
                    }
                }
                if (Schema::hasColumn($model->getTable(), 'uuid') && empty($model->uuid)) {
                    $model->uuid = (string) \Illuminate\Support\Str::orderedUuid();
                }
            } catch (\Throwable $e) {
                //
            }
        });
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds(): array
    {
        try {
            if (Schema::hasColumn($this->getTable(), 'uuid')) {
                return ['uuid'];
            }
        } catch (\Throwable $e) {
            //
        }

        return [];
    }

    public function getKeyType()
    {
        $keyName = $this->getKeyName();
        $id = $this->attributes[$keyName] ?? null;
        if ($id !== null) {
            return is_numeric($id) ? 'int' : 'string';
        }

        static $keyTypes = [];
        $table = $this->getTable();
        if (!isset($keyTypes[$table])) {
            try {
                $type = Schema::getColumnType($table, $keyName);
                $keyTypes[$table] = in_array($type, ['string', 'text', 'varchar']) ? 'string' : ($this->keyType ?? 'int');
            } catch (\Throwable $e) {
                $keyTypes[$table] = $this->keyType ?? 'int';
            }
        }

        return $keyTypes[$table];
    }

    public function getIncrementing()
    {
        return $this->getKeyType() === 'int';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $keyName = $field ?? $this->getRouteKeyName();

        $model = static::where($keyName, (string)$value)->first();
        if ($model) {
            return $model;
        }

        if (is_numeric($value) || (is_string($value) && strlen($value) <= 10)) {
            return static::where($keyName, 'like', "0{$value}%")
                ->orWhere($keyName, 'like', "{$value}%")
                ->orWhere($keyName, 'like', "%{$value}%")
                ->first();
        }

        return null;
    }
}
