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
use App\Models\User;

/**
 * Class ReviewModel
 * 
 * Review manager
 */
class ReviewModel extends Model
{
    use HasFactory;

    /**
     * Add review
     * 
     * @param $userId
     * @param $itemId
     * @param $text
     * @param $stars
     * @return void
     * @throws \Exception
     */
    public static function addReview($userId, $itemId, $text, $stars)
    {
        try {
            $user = User::where('id', '=', $userId)->where('locked', '=', false)->first();
            if (!$user) {
                throw new \Exception('User not valid');
            }

            $exists = static::where('userId', '=', $userId)->where('itemId', '=', $itemId)->count();
            if ($exists > 0) {
                throw new \Exception('There is already a review for this product by the user');
            }

            if (($stars < 1) || ($stars > 5)) {
                throw new \Exception('Stars must be a value from 1 to 5');
            }

            $item = new self();
            $item->userId = $userId;
            $item->itemId = $itemId;
            $item->content = $text;
            $item->stars = $stars;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Query reviews of an item
     * 
     * @param $itemId
     * @param $paginate
     * @return mixed
     * @throws \Exception
     */
    public static function queryReviews($itemId, $paginate = null)
    {
        try {
            $query = static::where('itemId', '=', $itemId)->where('locked', '=', false);

            if ($paginate !== null) {
                $query->where('id', '<', $paginate);
            }

            return $query->orderBy('id', 'desc')->limit(env('APP_MAXQUERYCOUNT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Query reviews of a specific user
     * 
     * @param $userId
     * @param $paginate
     * @return mixed
     * @throws \Exception
     */
    public static function queryUserReviews($userId, $paginate = null)
    {
        try {
            $query = static::where('userId', '=', $userId)->where('locked', '=', false);

            if ($paginate !== null) {
                $query->where('id', '<', $paginate);
            }

            return $query->orderBy('id', 'desc')->limit(env('APP_MAXQUERYCOUNT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a review
     * 
     * @param $userId
     * @param $reviewId
     * @return void
     * @throws \Exception
     */
    public static function deleteReview($userId, $reviewId)
    {
        try {
            $user = User::where('id', '=', $userId)->where('locked', '=', false)->first();
            if (!$user) {
                throw new \Exception('Insufficient permissions');
            }

            $item = static::where('id', '=', $reviewId)->first();
            if (!$item) {
                throw new \Exception('Review not found');
            }

            if (($user->id != $item->userId) || (!$user->admin)) {
                throw new \Exception('Insufficient permissions');
            }

            $item->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get average stars value of an item
     * 
     * @param $itemId
     * @return int
     * @throws \Exception;
     */
    public static function getAverageStars($itemId)
    {
        try {
            $count = static::where('itemId', '=', $itemId)->count();
            $stars = static::where('itemId', '=', $itemId)->sum('stars');

            return ($count > 0) ? $stars / $count : 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get review count of an item
     * 
     * @param $itemId
     * @return int
     * @throws \Exception
     */
    public static function getReviewCount($itemId)
    {
        try {
            return static::where('itemId', '=', $itemId)->count();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
