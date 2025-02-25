<?php

use App\Livewire\Dev\BranchEnv;
use Illuminate\Support\Facades\Process;
use Livewire\Livewire;

it('should show a current branch in the page', function () {
    Process::fake([
        'git branch --show-current' => Process::result(output: 'jeremias'),
    ]);

    Livewire::test(BranchEnv::class)
        ->assertSet('branch', 'jeremias')
        ->assertSee('jeremias');

    Process::assertRan('git branch --show-current');
});
