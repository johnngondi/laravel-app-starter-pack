<?php

namespace App\Livewire\Organisations;

use App\Models\Organisation;
use App\Models\Upload;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Organisation · Uploads')]
class UploadSettings extends Component
{
    public Organisation $organisation;

    /**
     * Bind the organisation resolved from the route slug.
     */
    public function mount(Organisation $organisation): void
    {
        $this->authorize('viewAny', Upload::class);

        $this->organisation = $organisation;
    }

    /**
     * Keep the permission team scope aligned with this organisation.
     */
    public function boot(): void
    {
        setPermissionsTeamId($this->organisation->getKey());
    }

    /**
     * Delete one of the organisation's uploads. A Livewire action (rather than a
     * native form) so the confirm button's WireUI spinner rides wire:loading.
     */
    public function deleteUpload(string $uuid): void
    {
        $upload = $this->organisation->uploads()->where('uuid', $uuid)->firstOrFail();

        $this->authorize('delete', $upload);

        $upload->delete();

        Flux::modal("upload-delete-{$uuid}")->close();

        Flux::toast(variant: 'success', text: __('File deleted.'));
    }

    public function render(): View
    {
        return view('livewire.organisations.upload-settings');
    }
}
