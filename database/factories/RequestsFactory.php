<?php

use Faker\Generator as Faker;

$factory->define(App\BRequest::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'book_id' => function () {
            return factory(App\Book::class)->create()->id;
        },
        'accepted' => random_int(0,1)
    ];
});
