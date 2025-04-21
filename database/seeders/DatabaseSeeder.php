<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory()->create(
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'is_admin' => true,
            ]
        );
        $this->call(CountriesSeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(Cities::class);

    }
}
