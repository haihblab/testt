<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Department;
use App\Models\Request;
use App\Models\Comment;
use App\Models\Status;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
$factory->define(Department::class, function (Faker $faker) {
    return [
        'name' => 'HB'.rand(1,3)
    ];
});

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'content' => $faker->name,
        'user_id' => rand(1, 10),
        'request_id' => rand(1, 10)
    ];
});


$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'address' => $faker->address,
        'password' => Hash::make('12345678'),
        'date_of_birth' => $faker->dateTimeBetween('1990-01-01', '2012-12-31')->format('Y/m/d'),
        'role_id' => rand(1, 3),
        'department_id' => rand(1, 3),
        'status' => rand(0, 1),
        'staff_id' => rand(1, 100)
    ];
});

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'status' => rand(1, 3),
        'user_id' => rand(1, 11)
    ];
});

$factory->define(Request::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'category_id' => rand(1, 7),
        'due_date' => $faker->dateTimeBetween('2021-06-05', '2022-12-31')->format('Y/m/d'),
        'manager_id' => rand(1, 7),
        'user_id' => rand(1, 7),
        'status' => rand(1, 3),
        'priority' => rand(1, 9),
        'content' => $faker->name
    ];
});
