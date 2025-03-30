<?php

use App\Http\Requests\GetPositionsRequest;
use Illuminate\Support\Facades\Validator;

it('passes when date is valid', function () {
    $data = ['date' => '2024-12-25'];
    $request = new GetPositionsRequest();

    $validator = Validator::make(
        $data,
        $request->rules(),
        $request->messages()
    );    

    expect($validator->passes())->toBeTrue();
});

it('fails when date is missing', function () {
    $data = [];
    $request = new GetPositionsRequest();

    $validator = Validator::make(
        $data,
        $request->rules(),
        $request->messages()
    );    

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('date'))->toBe('The date parameter is required.');
});

it('fails when date format is invalid', function () {
    $data = ['date' => '25-12-2024'];
    $request = new GetPositionsRequest();

    $validator = Validator::make(
        $data,
        $request->rules(),
        $request->messages()
    );    

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('date'))->toBe('The date must be in the format YYYY-MM-DD.');
});
