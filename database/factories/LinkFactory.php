<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Link;
use Faker\Generator as Faker;

$factory->define(Link::class, function (Faker $faker) {
    return [
        'reading_list_id' => 1,
        'position'        => 1,
        'url'             => $faker->url,
        'image'           => $faker->imageUrl(400, 240),
        'description'     => 'Blah blah blah blah blah',
        'title'           => ucfirst($faker->word),
    ];
});
