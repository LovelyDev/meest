<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call([
      UserSeeder::class,
      RoomTypeSeeder::class,
      RoomAccessSeeder::class,
      RoomYoutubeAccessSeeder::class,
      RoomSeeder::class,
      RoleSeeder::class,
      AliasSeeder::class,
      SettingsSeeder::class,
      UserRoleSeeder::class,
      AvatarDefaultSeeder::class,
    ]);

  }
}
