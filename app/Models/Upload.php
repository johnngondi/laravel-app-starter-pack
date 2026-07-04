<?php

namespace App\Models;

use App\Concerns\BelongsToOrganisation;
use App\Contracts\Uploadable;
use Database\Factories\UploadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * @property int $id
 * @property string|null $uuid
 * @property int $organisation_id
 * @property string|null $owner_type
 * @property int|null $owner_id
 * @property string $title
 * @property string|null $description
 * @property string $source_url
 * @property string|null $thumbnail_url
 * @property string $file_name
 * @property string|null $type
 * @property string|null $extension
 * @property int $size
 * @property int|null $user_id
 */
class Upload extends Model
{
    /** @use HasFactory<UploadFactory> */
    use BelongsToOrganisation, HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'source_url',
        'thumbnail_url',
        'file_name',
        'type',
        'extension',
        'size',
        'user_id',
        'organisation_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'size' => 'integer',
        'user_id' => 'integer',
        'organisation_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Upload $upload): void {
            // Assigned directly (not via fill) since `uuid` is intentionally not
            // mass-assignable — otherwise it would be silently dropped.
            $upload->uuid ??= Uuid::uuid4()->toString();
        });

        // Remove the physical file when the record is (soft) deleted.
        static::deleting(function (Upload $upload): void {
            if ($upload->source_url) {
                Storage::disk($upload->disk())->delete($upload->source_url);
            }
        });
    }

    /**
     * Store an uploaded file and persist its metadata.
     *
     * When an $owner is given it must be {@see Uploadable}; the record is
     * created through `$owner->uploads()` so the morph relation is attached.
     * Without an owner the upload is created standalone. Any value in
     * $attributes (e.g. user-supplied `title`/`description`) overrides the data
     * inferred from the file.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function store(UploadedFile $file, ?Model $owner = null, array $attributes = []): self
    {
        if ($owner !== null && ! $owner instanceof Uploadable) {
            throw new InvalidArgumentException(sprintf(
                'The owner [%s] must implement %s to receive uploads.',
                $owner::class,
                Uploadable::class,
            ));
        }

        $organisation = static::resolveOrganisation($owner);

        $directory = trim($owner?->getUploadDirectory() ?? 'uploads', '/');

        if ($organisation?->slug) {
            $directory .= '/'.$organisation->slug;
        }

        $path = $file->store($directory, static::diskName());

        $payload = array_merge([
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'Untitled',
            'source_url' => $path,
            'file_name' => $file->getClientOriginalName(),
            'type' => $file->getMimeType(),
            'extension' => Str::lower($file->getClientOriginalExtension()) ?: null,
            'size' => $file->getSize(),
            'user_id' => Auth::id(),
            'organisation_id' => $organisation?->getKey(),
        ], array_filter($attributes, static fn ($value) => $value !== null));

        return $owner !== null
            ? $owner->uploads()->create($payload)
            : static::create($payload);
    }

    /**
     * Resolve the organisation an upload belongs to: the owner itself when it
     * is an organisation, otherwise the current organisation.
     */
    protected static function resolveOrganisation(?Model $owner): ?Organisation
    {
        if ($owner instanceof Organisation) {
            return $owner;
        }

        return Auth::user()?->currentOrganisation;
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The filesystem disk uploads are stored on.
     */
    public static function diskName(): string
    {
        return config('uploads.disk', 'local');
    }

    public function disk(): string
    {
        return static::diskName();
    }

    public function previewUrl(): string
    {
        return route('uploads.preview', $this->uuid);
    }

    public function downloadUrl(): string
    {
        return route('uploads.download', $this->uuid);
    }

    /**
     * Human-friendly file size, e.g. "100 KB", "10 MB", "2 GB".
     */
    public function humanSize(): string
    {
        return Number::fileSize($this->size ?? 0, maxPrecision: 1);
    }

    public function isImage(): bool
    {
        return Str::startsWith((string) $this->type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->type === 'application/pdf' || $this->extension === 'pdf';
    }

    public function isVideo(): bool
    {
        return Str::startsWith((string) $this->type, 'video/');
    }

    public function isAudio(): bool
    {
        return Str::startsWith((string) $this->type, 'audio/');
    }

    /**
     * A Flux/heroicon name representing the file type.
     */
    public function iconName(): string
    {
        return match (true) {
            $this->isImage() => 'photo',
            $this->isPdf() => 'document-text',
            $this->isVideo() => 'film',
            $this->isAudio() => 'musical-note',
            in_array($this->extension, ['xls', 'xlsx', 'csv'], true) => 'table-cells',
            in_array($this->extension, ['doc', 'docx', 'txt', 'rtf'], true) => 'document-text',
            in_array($this->extension, ['zip', 'rar', '7z', 'gz', 'tar'], true) => 'archive-box',
            default => 'document',
        };
    }
}
