<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="{{ asset('AlienSoftLogo/aliensoft-icon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('AlienSoftLogo/aliensoft-icon.svg') }}">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

{{-- Resolve the signed-in member's saved appearance, overriding any device
     default so the choice follows them across logins and devices. --}}
@auth
    @if (auth()->user()->theme)
        <script>
            window.Flux.applyAppearance(@js(auth()->user()->theme))
        </script>
    @endif
@endauth

@wireUiScripts
