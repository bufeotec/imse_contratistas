<button {!! $attributes->merge(['class' => 'btn create-new ms-3']) !!}
        @if (isset($tooltip) && trim($tooltip) !== '')
            data-bs-tooltip="tooltip"
            data-bs-offset="0,6"
            data-bs-placement="top"
            data-bs-html="true"
            data-bs-original-title="{{ $tooltip }}"
        @endif
>
    <span>
        <i class="{{ $icons }} me-sm-1"></i>
        <span class="d-none d-sm-inline-block">{{ $slot }}</span>
    </span>
</button>
