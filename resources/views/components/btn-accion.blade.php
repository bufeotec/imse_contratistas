<span {!! $attributes->merge(['class' => 'm-2 cursoPointer']) !!}
      @if (isset($tooltip) && trim($tooltip) !== '')
          data-bs-tooltip="tooltip"
          data-bs-offset="0,6"
          data-bs-placement="top"
          data-bs-html="true"
          data-bs-original-title="{{ $tooltip }}"
        @endif
>
    {{$message}}
</span>
