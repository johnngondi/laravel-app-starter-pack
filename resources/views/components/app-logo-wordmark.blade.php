{{--
    AlienSoft App horizontal wordmark for the auth + onboarding pages. These pages
    follow the user's theme (@fluxAppearance), so we swap between the navy
    variant (legible on light backgrounds) and the white variant (for dark)
    using Tailwind's `dark:` toggle. Callers supply the sizing (e.g. h-12 w-auto).
--}}
<img
    src="{{ asset('AlienSoftLogo/aliensoft-horizontal-light.svg') }}"
    alt="{{ config('app.name', 'AlienSoft App') }}"
    {{ $attributes->merge(['class' => 'block dark:hidden']) }}
/>
<img
    src="{{ asset('AlienSoftLogo/aliensoft-horizontal.svg') }}"
    alt="{{ config('app.name', 'AlienSoft App') }}"
    {{ $attributes->merge(['class' => 'hidden dark:block']) }}
/>
