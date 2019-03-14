<?php

use Faker\Generator as Faker;

$factory->define(App\Book::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'title' => $faker->word(),
        'image_url' => $faker->url,
        'author' => $faker->name,
        'published' => "2019-01-02"
    ];
});
