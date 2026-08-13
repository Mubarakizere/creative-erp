<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

trait HasUuidColumn
{
    use HasUuids;

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}
