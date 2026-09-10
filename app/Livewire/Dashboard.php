<?php


namespace App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Monitor;

    #[Layout('layouts.app')]
    class Dashboard extends Component {
        #[Validate('required|string|max:255')]
        public $namaField;

        #[Validate('required|url|max:255')]
        public $urlField;

        #[Validate('required|integer|min:100|max:599')]
        public $statusField;

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
            return view('livewire.dashboard', compact('monitors'));
        }
    }
