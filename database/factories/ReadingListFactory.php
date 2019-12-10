<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ReadingList;
use Faker\Generator as Faker;

$factory->define(ReadingList::class, function (Faker $faker) {
    return [
        'name' => ucfirst('Link Name'),
        'position' => 1,
        'user_id' => 1,
    ];
});
