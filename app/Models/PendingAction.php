<?php

namespace App\Models;

use App\Enums\Priority;
use Database\Factories\PendingActionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $actionable_type
 * @property int|null $actionable_id
 * @property list<int> $actors
 * @property string $notes
 * @property string $action_type
 * @property Carbon $due_at
 * @property Priority $priority
 * @property string $resource_url
 * @property string $action_button_title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class PendingAction extends Model
{
    /** @use HasFactory<PendingActionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'actors',
        'notes',
        'action_type',
        'due_at',
        'priority',
        'resource_url',
        'action_button_title',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'actors' => 'array',
            'due_at' => 'datetime',
            'priority' => Priority::class,
        ];
    }

    /**
     * The resource this task is about. Nullable: a task may stand alone (e.g.
     * "review your dashboard") without pointing at a specific model.
     *
     * @return MorphTo<Model, $this>
     */
    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }
}
