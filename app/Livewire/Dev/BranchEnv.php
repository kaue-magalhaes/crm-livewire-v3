<?php

namespace App\Livewire\Dev;

use Illuminate\Support\Facades\Process;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * @property-read string $branch
 */
class BranchEnv extends Component
{
    public function render(): string
    {
        return <<<'blade'
        <div class="flex items-center space-x-2">
            <x-badge :value="$this->branch"/>
        </div>
        blade;
    }

    #[Computed]
    public function branch(): string
    {
        $process = Process::run('git branch --show-current');

        return trim($process->output());
    }
}
