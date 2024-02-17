<?php

use Illuminate\Support\Facades\Validator;

it('finds missing debug statements', function () {
    expect(['dd', 'dump', 'vardump'])
        ->not->toBeUsed();
});

it('does not use the validator facade', function () {
    expect(Validator::class)
        ->not->toBeUsed()->ignoring('App\Actions\Fortify');
});
