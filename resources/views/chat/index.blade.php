@extends('layouts.html')

@php
    $avatarParts = [];
    $targetFolders = [
      'bodies' => ['weight' => 1, 'label' => 'Body'],
      'hair' => ['weight' => 2, 'label' => 'Hair'],
      'pants' => ['weight' => 3, 'label' => 'Pants'],
      'shirts' => ['weight' => 4, 'label' => 'Shirts'],
      'coats' => ['weight' => 5, 'label' => 'Coats'],
      'shoes' => ['weight' => 6, 'label' => 'Shoes'],
      'head_accessories' => ['weight' => 7, 'label' => 'Head accessories'],
      'accessories' => ['weight' => 8, 'label' => 'Accessories'],
    ];

    collect($targetFolders)->each(function($item, $folder) use (&$avatarParts){
      $avatarParts[$folder]['key'] = $folder;
      $avatarParts[$folder]['weight'] = $item['weight'];
      $avatarParts[$folder]['label'] = $item['label'];

      collect(['man', 'woman'])->each(function($gender) use (&$avatarParts, $folder) {
        $genders = Illuminate\Support\Facades\Storage::disk('public')->files("avatars/components/{$folder}/{$gender}");
          collect($genders)->each(function($item) use (&$avatarParts, $folder, $gender) {
            $id = basename($item, ".png");
            $avatarParts[$folder][$gender][$id] = '/storage/' . $item;
          });
      });
    });

    $avatarParts['built'] = ['key' => 'built', 'weight' => 9, 'label' => 'Pre-made avatars'];

@endphp

@section('content')
    <div class="container-main">
        <el-row>
            <el-col :span="24">
                <div class="grid-content bg-purple-dark">
                    <layout :user="{{ auth()->user() }}"
                            :avatars="{{ json_encode($avatarParts) }}">
                    </layout>
                </div>
            </el-col>
        </el-row>
    </div>
@endsection
