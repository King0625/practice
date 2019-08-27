<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->paragraph(1),
        'price' => $faker->randomFloat(2, 1, 100),
        'quantity' => $faker->numberBetween(1, 15),
        'available' => $faker->randomElement([Product::AVAILABLE, Product::UNAVAILABLE])
    ];
});
