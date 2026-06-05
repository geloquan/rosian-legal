<div class="space-y-4">
  @if ($fileName)
    <p class="text-sm text-gray-600 dark:text-gray-300">
      <span class="font-medium">File:</span> {{ $fileName }}
    </p>
  @endif

  @if ($errorMessage)
    <x-filament::section compact>
      <p class="text-sm text-danger-600 dark:text-danger-400">{{ $errorMessage }}</p>
    </x-filament::section>
  @endif

  <x-filament::section heading="Detected Variables" compact>
    @if (count($variables))
      <div class="flex flex-wrap gap-2">
        @foreach ($variables as $variable)
          <x-filament::badge color="gray">{{ $variable }}</x-filament::badge>
        @endforeach
      </div>
    @else
      <p class="text-sm text-gray-600 dark:text-gray-300">No variables were detected in this template.</p>
    @endif
  </x-filament::section>

  <x-filament::section heading="Document Preview">
    @if ($previewHtml)
      <div class="max-h-[32rem] overflow-auto rounded-lg border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
        {!! $previewHtml !!}
      </div>
    @else
      <p class="text-sm text-gray-600 dark:text-gray-300">Preview is unavailable for this file.</p>
    @endif
  </x-filament::section>
</div>
