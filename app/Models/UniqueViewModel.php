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
use Illuminate\Support\Facades\Cache;

/**
 * Class UniqueViewModel
 * 
 * Manager of unique item view counting
 */
class UniqueViewModel extends Model
{
    use HasFactory;

    /**
     * Add IP address as viewer for given resource item and return view count
     *
     * @param $id
     * @return int
     * @throws Exception
     */
    public static function viewForItem($id)
    {
        try {
            $count = 0;
            $ipAddress = md5(request()->ip());

            $item = static::where('itemId', '=', $id)->where('address', '=', $ipAddress)->first();
            if (!$item) {
                $item = new self();
                $item->itemId = $id;
                $item->address = $ipAddress;
                $item->save();
            }

            $count = Cache::remember('view_for_item_' . $id, 60 * 15, function() use ($id) {
                return static::where('itemId', '=', $id)->count();
            });

            return $count;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
