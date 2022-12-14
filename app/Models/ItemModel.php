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
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ImageModel;

/**
 * Class ItemModel
 * 
 * Interface to item management
 */
class ItemModel extends Model
{
    use HasFactory;

    /**
     * Store item
     * 
     * @param $attr
     * @param $item
     * @param $userId
     * @param $isEdited
     * @return int
     * @throws \Exception
     */
    private static function storeItem($attr, $item, $userId = null, $isEdited = false)
    {
        try {
            if ((isset($attr['github'])) && (strlen($attr['github'] > 0)) && (strpos($attr['github'], 'https://github.com') !== 0)) {
                throw new \Exception(__('app.invalid_github_link'));
            }

            if ($userId !== null) {
                $item->userId = auth()->id();
            }

            if (!$isEdited) {
                $item->approved = false;
            }

            if ((!isset($attr['tags'])) || ($attr['tags'] === null)) {
                $attr['tags'] = '';
            }

            $item->slug = Str::slug($attr['name']);
            $item->name = $attr['name'];
            $item->typeId = $attr['type'];
            $item->creator = $attr['creator'];
            $item->summary = $attr['summary'];
            $item->description = $attr['description'];
            $item->tags = $attr['tags'] . ' ';
            $item->download = (!env('APP_ALLOW_DL_HOSTING')) ? $attr['download'] : '';
            $item->github = str_replace('https://github.com/', '', $attr['github']);
            $item->website = $attr['website'];
            $item->twitter = $attr['twitter'];

            $item->twitter = str_replace('https://twitter.com/', '', $item->twitter);
            $item->twitter = str_replace('@', '', $item->twitter);

            $image = request()->file('logo');
            if ($image !== null) {
                if ($image->getSize() > env('APP_MAXUPLOADSIZE')) {
                    throw new \Exception(__('app.upload_size_exceeded'));
                }

                $fname = uniqid('', true) . md5(random_bytes(55));
                $fext = $image->getClientOriginalExtension();

                $image->move(public_path() . '/gfx/logos/', $fname . '.' . $fext);

                $baseFile = public_path() . '/gfx/logos/' . $fname;
                $fullFile = $baseFile . '.' . $fext;

                if (!ImageModel::isValidImage(public_path() . '/gfx/logos/' . $fname . '.' . $fext)) {
                    throw new \Exception('Invalid image uploaded');
                }

                if (!ImageModel::createThumbFile($fullFile, ImageModel::getImageType($fext, $baseFile), $baseFile, $fext)) {
                    throw new \Exception('createThumbFile failed', 500);
                }

                unlink(public_path() . '/gfx/logos/' . $fname . '.' . $fext);

                $item->logo = $fname . '_thumb.' . $fext;
            }

            if (env('APP_ALLOW_DL_HOSTING')) {
                $download = request()->file('download');
                if ($download !== null) {
                    if ($download->getSize() > env('APP_MAXUPLOADSIZE')) {
                        throw new \Exception(__('app.upload_size_exceeded'));
                    }

                    $fname = Str::slug($attr['name']) . '_' . uniqid('', true);
                    $fext = $download->getClientOriginalExtension();

                    $download->move(public_path() . '/downloads/', $fname . '.' . $fext);

                    if (!AppModel::isValidArchive(public_path() . '/downloads/' . $fname . '.' . $fext)) {
                        unlink(public_path() . '/downloads/' . $fname . '.' . $fext);
                        throw new \Exception(__('app.invalid_archive_file'));
                    }

                    $item->download = asset('downloads/' . $fname . '.' . $fext);
                }
            }

            $item->save();

            $item->slug = Str::slug(strval($item->id) . ' ' . $attr['name']);
            $item->save();

            return $item->id;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add item
     * 
     * @param $attr
     * @return int
     * @throws \Exception
     */
    public static function addItem($attr)
    {
        try {
            $item = new self();
            return static::storeItem($attr, $item, auth()->id());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit item
     * 
     * @param $id
     * @param $attr
     * @return int
     * @throws \Exception
     */
    public static function editItem($id, $attr)
    {
        try {
            $item = static::where('id', '=', $id)->first();
            if (($item->userId !== auth()->id()) || (!User::isAdmin(auth()->id()))) {
                throw new \Exception('Insufficient permissions');
            }

            return static::storeItem($attr, $item, null, true);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Query a list of items
     * 
     * @param $type
     * @param $paginate
     * @param $text_search
     * @param $tag
     * @return mixed
     * @throws \Exception
     */
    public static function queryItems($type = '_all_', $paginate = null, $text_search = null, $tag = null)
    {
        try {
            if ($type !== '_all_') {
                $query = static::where(function($query) use($type) {
                    $query->where('typeId', function($query) use($type) {
                        $query->select('id')
                            ->from('type_models')
                            ->where('slug', '=', $type);
                    });
                });
            } else {
                $query = static::where('typeId', '>', 0);
            }

            if ($paginate !== null) {
                $query->where('id', '<', $paginate);
            }

            if ($text_search !== null) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . trim(strtolower($text_search)) . '%'])
                    ->orWhereRaw('LOWER(summary) LIKE ?', ['%' . trim(strtolower($text_search)) . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . trim(strtolower($text_search)) . '%']);
            }

            if ($tag !== null) {
                $query->whereRaw('LOWER(tags) LIKE ?', ['%' . $tag . ' ' . '%']);
            }

            $query->where('approved', '=', true)->where('locked', '=', false);

            return $query->orderBy('id', 'desc')->limit(env('APP_MAXQUERYCOUNT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Query items by a specific user
     * 
     * @param $userId
     * @param $paginate
     * @return mixed
     * @throws \Exception
     */
    public static function queryUserItems($userId, $paginate = null)
    {
        try {
            $query = static::where('approved', '=', true)->where('locked', '=', false)->where('userId', '=', $userId);

            if ($paginate !== null) {
                $query->where('id', '<', $paginate);
            }

            return $query->orderBy('id', 'desc')->limit(env('APP_MAXQUERYCOUNT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Query random items
     * 
     * @param $exclude
     * @param $type
     * @param $limit
     * @return mixed
     * @throws \Exception
     */
    public static function queryRandom($exclude, $type, $limit)
    {
        try {
            if ($type !== '_all_') {
                $query = static::where('typeId', '=', $type);
            } else {
                $query = static::where('typeId', '>', 0);
            }

            return $query->where('id', '<>', $exclude)->where('approved', '=', true)->where('locked', '=', false)->limit($limit)->inRandomOrder()->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get item by slug
     * 
     * @param $slug
     * @param $check_flags
     * @return mixed
     * @throws \Exception
     */
    public static function getBySlug($slug, $check_flags = true)
    {
        try {
            $query = static::where('slug', '=', $slug);

            if ($check_flags) {
                $query->where('locked', '=', false)->where('approved', '=', true);
            }

            return $query->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
