{{--
    Primary submit button for native (non-Livewire) POST forms — e.g. the
    Fortify auth forms (login, register, password reset). Shows a spinner and
    disables itself while the form is submitting, giving feedback and blocking
    double-submits. The full-page POST reloads the page, which resets the state.

    For Livewire actions (wire:submit / wire:click) DON'T use this — add WireUI's
    `spinner="methodName"` to <x-button> instead, which rides wire:loading.
--}}
<x-button
    {{ $attributes->merge(['type' => 'submit']) }}
    primary
    x-data="{ submitting: false }"
    x-init="$el.closest('form')?.addEventListener('submit', () => { submitting = true })"
    x-bind:disabled="submitting"
>
    <svg
        x-show="submitting"
        x-cloak
        class="me-2 size-4 animate-spin"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        aria-hidden="true"
    >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    {{ $slot }}
</x-button>
