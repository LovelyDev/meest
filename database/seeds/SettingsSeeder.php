<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Setting::create([
      'site_name' => 'Chat Horizon',
      'site_slogan' => 'Connecting the world',
    ]);

  }
}
