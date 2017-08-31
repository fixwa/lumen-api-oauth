<?php

use App\Comment;
use App\Post;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checking because truncate() will fail
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Post::truncate();
        Comment::truncate();

        factory(User::class, 10)->create();
        factory(Post::class, 50)->create();
        factory(Comment::class, 100)->create();

        User::create([
            'name' => 'Pablo',
            'email' => 'fixwah@gmail.com',
            'password' => Hash::make('12345678'),
            'imageUrl' => url('/default_user.png'),
        ]);

        $this->call(OAuthClientSeeder::class);
        $this->call(MarkersSeeder::class);

        // Enable it back
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
