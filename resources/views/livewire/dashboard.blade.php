     <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

<div>
        @foreach ($monitors as $monitor)
        <div>{{ $monitor->url }}</div>
        <div>{{ $monitor->name }}</div>
        <div>{{ $monitor->last_checked_at?->diffForHumans() ?? 'cek dulu lewat scheduler' }}</div>

        <div @class([
            'Monitor status',
            'bg-green-500' => $monitor->is_up,
            'bg-red-500' => ! $monitor->is_up,    
        ])>
        {{ $monitor-> is_up ? 'Up' : 'Down' }}
        </div>
        @endforeach
    
    <div x-data="{ tampilkan: false }">
        <button @click="tampilkan = !tampilkan">Tambah Monitor</button>
        <div x-show="tampilkan">
            <form wire:submit="store">
            <div>
                <label for="namaField">Nama</label>
                <input type="text" id="namaField" wire:model="namaField">
                @error('namaField') <span class="error">{{ $message }}</span> @enderror
            </div>
    
            <div>
                <label for="urlField">URL</label>
                <input type="url" id="urlField" wire:model="urlField">
                @error('urlField') <span class="error">{{ $message }}</span> @enderror
            </div>
    
            <div>
                <label for="statusField">Status</label>
                <input type="number" id="statusField" wire:model="statusField">
                @error('statusField') <span class="error">{{ $message }}</span> @enderror
            </div>
    
            <button type="submit">Submit</button>
        </form>
    </div>

    </div>
        
</div>


