<?php

namespace App\Contracts;

use App\Concerns\HasUploads;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Marks a model that can own uploads. Satisfied by the {@see HasUploads}
 * trait — a model only needs to `use HasUploads` and `implements Uploadable`.
 *
 * @phpstan-require-extends Model
 */
interface Uploadable
{
    /**
     * @return MorphMany<Upload, covariant Model>
     */
    public function uploads(): MorphMany;

    /**
     * The directory (relative to the uploads disk) files are stored under.
     */
    public function getUploadDirectory(): string;
}
