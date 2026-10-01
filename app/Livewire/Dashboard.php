<?php


namespace App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Monitor;
use App\Models\Incident;

    #[Layout('layouts.app')]
    class Dashboard extends Component {
        #[Validate('required|string|max:255')]
        public String $namaField = '';

        #[Validate('required|url|max:255')]
        public String $urlField = '';

        #[Validate('required|integer|min:100|max:599')]
        public String $statusField = '200';

        public function store() {
            $this->validate();

            Monitor::create([
                'name' => $this->namaField,
                'url' => $this->urlField,
                'expected_status' => $this->statusField,
            ]);

            $this->reset(['namaField', 'urlField', 'statusField']);
        }

        public function render() {
            $monitors = Monitor::all();

            $incidents = Incident::with('monitor')
            ->latest('detected_at')
            ->limit(10)
            ->get();

            return view('livewire.dashboard', [
                'monitors' => $monitors,
                'incidents' => $incidents,
            ]);
        }
    }
