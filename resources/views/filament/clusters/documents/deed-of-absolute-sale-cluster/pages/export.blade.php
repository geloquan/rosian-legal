<x-filament-panels::page>
    {{-- Page content --}}
  @if($this->showForm)

  @else
    {{ $this->table }}
  @endif
</x-filament-panels::page>
