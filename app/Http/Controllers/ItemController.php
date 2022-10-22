<?php

/*
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\AppModel;
use App\Models\ItemModel;
use App\Models\UniqueViewModel;
use App\Models\GithubModel;
use App\Models\CaptchaModel;
use App\Models\ReviewModel;
use App\Models\ReportModel;
use App\Models\PushModel;
use App\Models\User;

/**
 * Class ItemController
 * 
 * Item specific route handling
 */
class ItemController extends Controller
{
    /**
     * Query item list
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function query()
    {
        try {
            $type = request('type', '_all_');
            $paginate = request('paginate', null);
            $text_search = request('text_search', null);
            $tag = request('tag', null);

            $data = ItemModel::queryItems($type, $paginate, $text_search, $tag);
            foreach ($data as &$item) {
                $user = User::where('id', '=', $item->userId)->first();
                $item->userData = new \stdClass();
                $item->userData->id = $user->id;
                $item->userData->username = $user->username;

                $item->views = AppModel::countAsString(UniqueViewModel::viewForItem($item->id));
                $item->avg_stars = ReviewModel::getAverageStars($item->id);
                $item->review_count = ReviewModel::getReviewCount($item->id);
                $item->tags = explode(' ', $item->tags);
            }

            return response()->json(array('code' => 200, 'data' => $data->toArray()));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Query item list of a specific user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryUser()
    {
        try {
            $userId = request('user');
            $paginate = request('paginate', null);

            $user = User::where('id', '=', $userId)->where('locked', '=', false)->first();
            if (!$user) {
                throw new \Exception('User not found: ' . $user);
            }

            $data = ItemModel::queryUserItems($user->id, $paginate);
            foreach ($data as &$item) {
                $user = User::where('id', '=', $item->userId)->first();
                $item->userData = new \stdClass();
                $item->userData->id = $user->id;
                $item->userData->username = $user->username;

                $item->views = AppModel::countAsString(UniqueViewModel::viewForItem($item->id));
                $item->avg_stars = ReviewModel::getAverageStars($item->id);
                $item->review_count = ReviewModel::getReviewCount($item->id);
                $item->tags = explode(' ', $item->tags);
            }

            return response()->json(array('code' => 200, 'data' => $data->toArray()));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Query item reviews
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryReviews()
    {
        try {
            $itemId = request('itemId');
            $paginate = request('paginate', null);

            $data = ReviewModel::queryReviews($itemId, $paginate);
            foreach ($data as &$item) {
                $user = User::where('id', '=', $item->userId)->first();

                $item->userData = new \stdClass();
                $item->userData->id = $user->id;
                $item->userData->username = $user->username;
                $item->userData->avatar = $user->avatar;

                $item->item = ItemModel::where('id', '=', $itemId)->first();
            }

            if (!\Auth::guest()) {
                $user_review = ReviewModel::where('itemId', '=', $itemId)->where('userId', '=', auth()->id())->first();
                
                if ($user_review !== null) {
                    $authUser = User::getByAuthId();

                    $user_review->userData = new \stdClass();
                    $user_review->userData->id = $authUser->id;
                    $user_review->userData->username = $authUser->username;
                    $user_review->userData->avatar = $authUser->avatar;

                    $user_review = $user_review->toArray();
                }
            } else {
                $user_review = null;
            }

            $review_count = ReviewModel::getReviewCount($itemId);

            return response()->json(array('code' => 200, 'data' => $data->toArray(), 'user_review' => $user_review, 'count' => $review_count));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * View specific item
     * 
     * @param $item
     * @return mixed
     */
    public function view($item)
    {
        try {
            $check_flags = true;
            $viewer = User::getByAuthId();
            if (($viewer) && ($viewer->admin)) {
                $check_flags = false;
            }

            $item = ItemModel::getBySlug($item, $check_flags);
            if (!$item) {
                $item = ItemModel::where('id', '=', $item);
                if ($check_flags) {
                    $item->where('locked', '=', false)->where('approved', '=', true);
                }
                $item = $item->first();
                if (!$item) {
                    throw new \Exception(__('app.item_not_found'));
                }
            }

            $user = User::where('id', '=', $item->userId)->first();
            $item->userData = new \stdClass();
            $item->userData->id = $user->id;
            $item->userData->username = $user->username;

            $item->views = AppModel::countAsString(UniqueViewModel::viewForItem($item->id));
            $item->tags = explode(' ', $item->tags);
            $item->avg_stars = ReviewModel::getAverageStars($item->id);
            $item->review_count = ReviewModel::getReviewCount($item->id);
            $item->user_review = ReviewModel::where('itemId', '=', $item->id)->where('userId', '=', auth()->id())->first();

            $old_github = $item->github;

            try {
                $item->github = GithubModel::queryRepoInfo($item->github);
                $item->github->last_commit_diff = Carbon::parse($item->github->pushed_at)->diffForHumans();
                $item->github->commit_day_count = Carbon::parse($item->github->pushed_at)->diff(Carbon::now())->days;
                $item->github->stargazers_count = AppModel::countAsString($item->github->stargazers_count);
                $item->github->forks_count = AppModel::countAsString($item->github->forks_count);
            } catch (\Exception $e) {
                $item->github = $old_github;
            }
            
            $others = ItemModel::queryRandom($item->id, $item->langId, env('APP_QUERYRANDOMCOUNT'));
            foreach ($others as &$other) {
                $user = User::where('id', '=', $other->userId)->first();
                $other->userData = new \stdClass();
                $other->userData->id = $user->id;
                $other->userData->username = $user->username;

                $other->views = AppModel::countAsString(UniqueViewModel::viewForItem($other->id));
                $other->avg_stars = ReviewModel::getAverageStars($item->id);
                $other->review_count = ReviewModel::getReviewCount($item->id);
                $other->tags = explode(' ', $other->tags);
            }

            return view('entities.item', [
                'captcha' => CaptchaModel::createSum(session()->getId()),
                'user' => User::getByAuthId(),
                'item' => $item,
                'others' => $others
            ]);
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * View submit form
     * 
     * @return mixed
     */
    public function viewSubmit()
    {
        try {
            parent::validateLogin();

            $user = User::getByAuthId();

            return view('home.submit', [
                'captcha' => CaptchaModel::createSum(session()->getId()),
                'user' => $user,
                'metro' => true
            ]);
        } catch (\Exception $e) {
            return redirect('/')->with('error', $e->getMessage());
        }
    }

    /**
     * Submit item
     * 
     * @return mixed
     */
    public function submit()
    {
        try {
            parent::validateLogin();

            $valtable = [
                'name' => 'required',
                'summary' => 'required|max:120',
                'type' => 'required|numeric',
                'description' => 'required',
                'creator' => 'required',
                'tags' => 'nullable',
                'github' => 'nullable',
                'twitter' => 'nullable',
                'website' => 'nullable'
            ];

            if (!env('APP_ALLOW_DL_HOSTING')) {
                $valtable['download'] = 'required';
            }

            $attr = request()->validate($valtable);

            ItemModel::addItem($attr);

            return redirect('/')->with('success', __('app.item_submitted_successfully'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * View edit form
     * 
     * @param $id
     * @return mixed
     */
    public function viewEdit($id)
    {
        try {
            parent::validateLogin();

            $user = User::getByAuthId();
            
            $query = ItemModel::where('id', '=', $id);

            if (!$user->admin) {
                $query->where('userId', '=', $user->id)->where('locked', '=', false);
            }

            $item = $query->first();

            if (!$item) {
                throw new \Exception('Invalid item or insufficient permissions: ' . $id);
            }
            
            return view('home.edit', [
                'captcha' => CaptchaModel::createSum(session()->getId()),
                'user' => $user,
                'metro' => true,
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update item with edited data
     * 
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        try {
            parent::validateLogin();

            $valtable = [
                'summary' => 'required|max:120',
                'type' => 'required|numeric',
                'description' => 'required',
                'creator' => 'required',
                'tags' => 'nullable',
                'github' => 'nullable',
                'twitter' => 'nullable',
                'website' => 'nullable'
            ];

            if (!env('APP_ALLOW_DL_HOSTING')) {
                $valtable['download'] = 'required';
            }

            $attr = request()->validate($valtable);

            $user = User::getByAuthId();

            $query = ItemModel::where('id', '=', $id);

            if (!$user->admin) {
                $query->where('userId', '=', $user->id)->where('locked', '=', false);
            }

            $item = $query->first();

            if (!$item) {
                throw new \Exception('Invalid item or insufficient permissions: ' . $id);
            }

            $attr['name'] = $item->name;

            ItemModel::editItem($id, $attr);

            return redirect('/view/' . $item->slug)->with('success', __('app.item_saved_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create a review for an item
     * 
     * @param $id
     * @return mixed
     */
    public function createReview($id)
    {
        try {
            parent::validateLogin();

            $attr = request()->validate([
                'content' => 'required',
                'rating' => 'required|numeric|min:1|max:5'
            ]);

            $item = ItemModel::where('id', '=', $id)->first();
            $reviewer = User::where('id', '=', auth()->id())->first();

            $already = ReviewModel::where('itemId', '=', $id)->where('userId', '=', auth()->id())->count();
            if ($already > 0) {
                throw new \Exception('You have already reviewed this product. Please delete your old review before reviewing again.');
            }

            ReviewModel::addReview(auth()->id(), $id, $attr['content'], $attr['rating']);

            PushModel::addNotification(__('app.review_added_short'), __('app.review_added_long', ['reviewer' => $reviewer->username, 'item_name' => $item->name, 'url' => url('/view/' . $item->slug)]), 'PUSH_REVIEWED', $item->userId);

            return back()->with('flash.success', __('app.review_stored'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Report an item
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportItem($id)
    {
        try {
            parent::validateLogin();

            ReportModel::addReport(auth()->id(), $id, 'ENT_ITEM');

            return response()->json(array('code' => 200, 'msg' => __('app.report_successful')));
        } catch(\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Report a review
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportReview($id)
    {
        try {
            parent::validateLogin();

            ReportModel::addReport(auth()->id(), $id, 'ENT_REVIEW');

            return response()->json(array('code' => 200, 'msg' => __('app.report_successful')));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Delete a review
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteReview($id)
    {
        try {
            parent::validateLogin();

            $user = User::getByAuthId();

            $item = ReviewModel::where('id', '=', $id)->first();
            if ($item->locked) {
                throw new \Exception('Item is locked');
            }

            if ((!$item->userId !== $user->id) && (!$user->admin)) {
                throw new \Exception('Insufficient permissions');
            }

            $item->delete();

            return response()->json(array('code' => 200, 'msg' => __('app.removal_successful')));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Delete an item
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteItem($id)
    {
        try {
            parent::validateLogin();

            $user = User::getByAuthId();

            $item = ItemModel::where('id', '=', $id)->first();
            if ($item->locked) {
                throw new \Exception('Item is locked');
            }

            if ((!$item->userId !== $user->id) && (!$user->admin)) {
                throw new \Exception('Insufficient permissions');
            }

            AppModel::deleteEntity($item->id, 'ENT_ITEM');

            return response()->json(array('code' => 200, 'msg' => __('app.removal_successful')));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }
}
