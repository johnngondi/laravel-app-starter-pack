{{-- A field label followed by a red asterisk that marks the field as required.
     Pass it to a WireUI field's `label` slot so the marker renders as HTML. --}}
<span>{{ $slot }}<span class="ms-0.5 text-red-600 dark:text-red-400" aria-hidden="true">*</span></span>
