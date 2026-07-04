<?php

namespace App\Concerns;

use App\Contracts\Uploadable;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Gives a model a polymorphic collection of uploads. Pair it with the
 * {@see Uploadable} interface so the model is accepted by
 * {@see Upload::store()}.
 */
trait HasUploads
{
    /**
     * @return MorphMany<Upload, $this>
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'owner', 'owner_type', 'owner_id');
    }

    /**
     * Get upload destination from model else use default
     */
    public function getUploadDirectory(): string
    {
        if (isset($this->upload_directory)) {
            return $this->upload_directory;
        }

        return 'uploads';
    }
}
