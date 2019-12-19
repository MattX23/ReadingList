<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Link;
use Faker\Generator as Faker;

$factory->define(Link::class, function (Faker $faker) {
    return [
        'reading_list_id' => 1,
        'position'        => 1,
        'url'             => 'http://www.example.com/',
        'image'           => $faker->imageUrl(400, 240),
        'description'     => $faker->sentence(rand(6, 20)),
        'title'           => ucfirst($faker->word),
    ];
});
