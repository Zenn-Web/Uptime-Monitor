     <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

<div wire:poll.30s>
    @foreach ($monitors as $monitor)
        <div class="rounded-xl shadow-md">
            {{ $monitor->url }}
            {{ $monitor->name }}
            {{ $monitor->last_checked_at?->diffForHumans() ?? 'Cek dulu lewat scheduler' }}
        </div>

        <div @class([
            'monitor-status',
            'bg-green-500' => $monitor->is_up,
            'bg-red-500' => ! $monitor->is_up,
        ])>
            {{ $monitor->is_up ? 'Up' : 'Down' }}
        </div>
    @endforeach

    <div x-data="{ tampilkan: false }">
        <button type="button" @click="tampilkan = !tampilkan">
            Tambah Monitor
        </button>

        <div x-show="tampilkan">
            <form wire:submit="store">
                <div>
                    <label for="namaField">Nama</label>
                    <input type="text" id="namaField" wire:model="namaField">
                    @error('namaField')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="urlField">URL</label>
                    <input type="url" id="urlField" wire:model="urlField">
                    @error('urlField')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="statusField">Status</label>
                    <input type="number" id="statusField" wire:model="statusField">
                    @error('statusField')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <h2 class="mt-8 text-xl font-semibold">
        Incident Terbaru
    </h2>

    @forelse ($incidents as $incident)
        <div class="mt-4 rounded-xl border p-4">
            <strong>{{ $incident->monitor->name }}</strong>

            @if ($incident->status === 'down')
                <p class="text-red-600">
                    Down sejak {{ $incident->detected_at->diffForHumans() }}
                </p>
            @else
                <p class="text-green-600">
                    Recovered {{ $incident->resolved_at?->diffForHumans() ?? 'Belum diketahui' }}
                </p>
            @endif

            @if ($incident->note)
                <p>{{ $incident->note }}</p>
            @endif
        </div>
    @empty
        <p class="mt-4">Belum ada incident.</p>
    @endforelse
</div>


