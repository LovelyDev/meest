<?php

namespace App\Http\Controllers;

use App\Alias;
use App\Http\Requests\AliasRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AliasController extends Controller
{
    public function findById($id)
    {
        return Alias::find($id);
    }

    public function find()
    {
        //
    }

    /**
     * Create new user alias.
     * @return mixed
     */
    public function create()
    {
        $params = request()->all();

        request()->validate([
            'alias' => 'bail|required|unique:aliases,alias|max:14|regex:/^[a-zA-Z0-9\s]+$/',
            'creator_alias_id' => 'required|numeric|min:1'
        ], [
            'alias.unique' => "The user name {$params['alias']} is already registered to another account.",
            'alias.required' => "Please provide a valid username.",
            'alias.regex' => "Invalid characters detected, please only enter letters and numbers."
        ]);

        $user = Auth::user();

        // We check if the alias creator has not created any alias.
        // We bail an alias child has been created by the alias.
        $alias = Alias::where('id', $params['creator_alias_id'])
            ->where('alias_child_id', '=', 0)->first();

        if (empty($alias)) {
            throw ValidationException::withMessages([
                'alias' => ["Cannot create user name {$params['alias']}. You have already created a user name"],
            ]);
        }

        // Create the new alias.
        $alias = Alias::create([
            'alias' => ucwords($params['alias']),
            'slug' => Str::slug($params['alias'], '_'),
            'role_id' => 8, // not sure about this.
            'user_id' => $user->id,
            'hours' => 0,
        ]);

        // Set the alias_child_id of the the creating alias.
        // An alias with more than 24 chat hours can ONLY create 1 alias.
        $creatorAlias = Alias::find($params['creator_alias_id']);
        $creatorAlias->alias_child_id = $alias->id;
        $creatorAlias->save();

        return $alias;
    }


    /**
     * Update the alias model.
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $params = request()->all();
        $alias = Alias::where('id', $id)->first();
        $alias->bodies = $params['bodies'] ?? '000';
        $alias->hair = $params['hair']  ?? '000';
        $alias->faces = $params['faces']  ?? '000';
        $alias->shirts = $params['shirts']  ?? '000';
        $alias->coats = $params['coats']  ?? '000';
        $alias->pants = $params['pants']  ?? '000';
        $alias->shoes = $params['shoes']  ?? '000';
        $alias->accessories = $params['accessories']  ?? '000';
        $alias->head_accessories = $params['head_accessories']  ?? '000';
        $alias->specials = $params['specials']  ?? '000';
        $alias->save();

        return $alias;
    }

    public function delete($id)
    {
        //
    }

    public function disable($id)
    {
        //
    }

}
