<?php

/*
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TypeModel
 * 
 * Interface to item types
 */
class TypeModel extends Model
{
    use HasFactory;

    /**
     * Get full list of types
     * 
     * @return mixed
     * @throws \Exception
     */
    public static function getTypes()
    {
        try {
            return static::orderBy('type', 'asc')->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add new type
     * 
     * @param $name
     * @return void
     * @throws \Exception
     */
    public static function addType($name)
    {
        try {
            $item = new self();
            $item->type = $name;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit existing type
     * 
     * @param $id
     * @param $new_name
     * @return void
     * @throws \Exception
     */
    public static function editType($id, $new_name)
    {
        try {
            $item = static::where('id', '=', $id)->first();
            if (!$item) {
                throw new \Exception('Type not found: ' . $id);
            }

            $item->type = $new_name;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete existing type
     * 
     * @param $id
     * @return void
     * @throws \Exception
     */
    public static function deleteType($id)
    {
        try {
            $item = static::where('id', '=', $id)->first();
            if (!$item) {
                throw new \Exception('Type not found: ' . $id);
            }

            $item->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
