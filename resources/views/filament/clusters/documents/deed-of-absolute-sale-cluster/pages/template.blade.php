<x-filament-panels::page>
  @if($this->showForm)
    <x-filament::section>
      <form wire:submit="saveDocument">
        {{ $this->form }}

        <div class="flex gap-2 mt-6">
          <x-filament::button type="submit">
            Save
          </x-filament::button>

          <x-filament::button
            color="gray"
            wire:click="$set('showForm', false)"
          >
            Cancel
          </x-filament::button>
        </div>
      </form>
    </x-filament::section>
  @else
    {{ $this->table }}
  @endif
</x-filament-panels::page>
