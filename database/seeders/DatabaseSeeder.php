<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $user = User::factory()->create([
      'name' => 'Marco',
      'email' => 'marcoangelo.quanico@gmail.com',
      'password' => Hash::make('1234'),
    ]);
    $role = \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
    $user->assignRole($role);
  }
}
