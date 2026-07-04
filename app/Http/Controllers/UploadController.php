<?php

namespace App\Http\Controllers;

use App\Contracts\Uploadable;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadController extends Controller
{
    /**
     * Store an uploaded file. When `owner_type`/`owner_id` are supplied they are
     * resolved to a model (which must use the HasUploads trait) and the upload
     * is attached to it; otherwise the upload is created standalone.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Upload::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:'.config('uploads.max_size', 25600)],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'owner_type' => ['nullable', 'string', 'required_with:owner_id'],
            'owner_id' => ['nullable', 'required_with:owner_type'],
        ]);

        $owner = $this->resolveOwner(
            $validated['owner_type'] ?? null,
            $validated['owner_id'] ?? null,
        );

        Upload::store($request->file('file'), $owner, [
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('status', __('File uploaded.'));
    }

    /**
     * Stream the file inline for in-browser previewing. Public: the uuid in the
     * URL is the access token.
     */
    public function preview(Upload $upload): StreamedResponse
    {
        return Storage::disk($upload->disk())->response(
            $upload->source_url,
            $upload->file_name,
            $upload->type ? ['Content-Type' => $upload->type] : [],
        );
    }

    /**
     * Stream the file as an attachment. Public, keyed by uuid.
     */
    public function download(Upload $upload): StreamedResponse
    {
        return Storage::disk($upload->disk())->download($upload->source_url, $upload->file_name);
    }

    /**
     * Delete an upload (and its underlying file via the model's deleting hook).
     */
    public function destroy(Upload $upload): RedirectResponse
    {
        $this->authorize('delete', $upload);

        $upload->delete();

        return back()->with('status', __('File deleted.'));
    }

    /**
     * Resolve the morph owner from its type/id, ensuring it can hold uploads.
     */
    protected function resolveOwner(?string $type, int|string|null $id): ?Model
    {
        if ($type === null || $id === null) {
            return null;
        }

        $class = Relation::getMorphedModel($type) ?? $type;

        abort_unless(
            is_a($class, Model::class, true) && is_a($class, Uploadable::class, true),
            422,
            'The given owner cannot receive uploads.',
        );

        return $class::query()->findOrFail($id);
    }
}
