<?php

/*
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\PushModel;
use App\Models\MailerModel;
use App\Models\ItemModel;
use App\Models\ReviewModel;
use App\Models\ReportModel;

/**
 * Class User
 * 
 * Interface to user management
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get user by E-Mail
     * 
     * @param $email
     * @return mixed
     * @throws \Exception
     */
    public static function getByEmail($email)
    {
        try {
            return static::where('email', '=', $email)->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get user by username
     * 
     * @param $username
     * @return mixed
     * @throws \Exception
     */
    public static function getByUsername($username)
    {
        try {
            return static::where('username', '=', $username)->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get user by auth ID
     * 
     * @return mixed
     * @throws \Exception
     */
    public static function getByAuthId()
    {
        try {
            return static::where('id', '=', auth()->id())->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Indicate if a user is valid (not locked and e-mail confirmed)
     * 
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public static function isValidUser($id)
    {
        try {
            $count = static::where('id', '=', $id)->where('account_confirm', '=', '_confirmed')->where('locked', '=', false)->count();
            return $count > 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Indicate if user is admin
     * 
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public static function isAdmin($id)
    {
        try {
            return static::where('id', '=', $id)->first()->admin;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return if string is a valid identifier for user name
     * 
     * @param $ident
     * @return mixed
     */
    public static function isValidNameIdent($ident)
    {
        if (is_numeric($ident) || (strlen($ident) == 0)) {
            return false;
        }

        return !preg_match('/[^a-z_\-0-9]/i', $ident);
    }

    /**
     * Perform user login
     * 
     * @param $email
     * @param $password
     * @return void
     * @throws \Exception
     */
    public static function login($email, $password)
    {
        try {
            $user = static::getByEmail($email);
            if (!$user) {
                throw new \Exception(__('app.user_not_found'));
            }

            if (!static::isValidUser($user->id)) {
                throw new \Exception(__('app.user_not_valid'));
            }

            if (!\Auth::attempt([
                'email' => $email,
                'password' => $password
            ])) {
                throw new \Exception(__('app.invalid_credentials'));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Perform user registration
     * 
     * @param array $attr
     * @return int
     * @throws \Exception
     */
    public static function register($attr)
    {
        try {
            if (!\Auth::guest()) {
                throw new \Exception(__('app.register_already_signed_in'));
            }

            $attr['username'] = trim(strtolower($attr['username']));

            $sum = CaptchaModel::querySum(session()->getId());
            if ($attr['captcha'] !== $sum) {
                throw new \Exception(__('app.register_captcha_invalid'));
            }

            if (static::getByEmail($attr['email'])) {
                throw new \Exception(__('app.register_email_in_use'));
            }

            if (static::getByUsername($attr['username'])) {
                throw new \Exception(__('app.register_username_in_use'));
            }

            if (!static::isValidNameIdent($attr['username'])) {
                throw new \Exception(__('app.register_username_invalid_chars'));
            }

            $user = new User();
            $user->username = $attr['username'];
            $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
            $user->email = $attr['email'];
            $user->avatar = 'default.png';
            $user->account_confirm = md5($attr['email'] . $attr['username'] . random_bytes(55));
            $user->bio = '';
            $user->location = '';
            $user->twitter = '';
            $user->save();

            $html = view('mail.registered', ['username' => $user->name, 'hash' => $user->account_confirm])->render();
            MailerModel::sendMail($user->email, __('app.mail_subject_register'), $html);

            PushModel::addNotification(__('app.register_welcome_short'), __('app.register_welcome_long', ['url' => url('/profile')]), 'PUSH_WELCOME', $user->id);

            return $user->id;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Resend account confirmation link
     *
     * @param $id
     * @throws \Exception
     */
    public static function resend($id)
    {
        try {
            $user = static::where('id', '=', $id)->where('account_confirm', '<>', '_confirmed')->first();
            if (!$user) {
                throw new \Exception(__('app.user_id_not_found_or_already_confirmed', ['id' => $id]));
            }

            $html = view('mail.registered', ['username' => $user->username, 'hash' => $user->account_confirm])->render();
            MailerModel::sendMail($user->email, __('app.mail_subject_register'), $html);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Confirm account
     *
     * @param $hash
     * @throws \Exception
     */
    public static function confirm($hash)
    {
        try {
            $user = static::where('account_confirm', '=', $hash)->first();
            if ($user === null) {
                throw new \Exception(__('app.register_confirm_token_not_found'));
            }

            $user->account_confirm = '_confirmed';
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Initialize password recovery
     *
     * @param $email
     * @throws \Exception
     */
    public static function recover($email)
    {
        try {
            $user = static::getByEmail($email);
            if (!$user) {
                throw new \Exception(__('app.email_not_found'));
            }

            $user->password_reset = md5($user->email . date('c') . uniqid('', true));
            $user->save();

            $htmlCode = view('mail.pwreset', ['username' => $user->username, 'hash' => $user->password_reset])->render();
            MailerModel::sendMail($user->email, __('app.mail_password_reset_subject'), $htmlCode);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Perform password reset
     *
     * @param $password
     * @param $password_confirm
     * @param $hash
     * @throws \Exception
     */
    public static function reset($password, $password_confirm, $hash)
    {
        try {
            if ($password != $password_confirm) {
                throw new \Exception(__('app.password_mismatch'));
            }

            $user = static::where('password_reset', '=', $hash)->first();
            if (!$user) {
                throw new \Exception(__('app.hash_not_found'));
            }

            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->password_reset = '';
            $user->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save user profile settings and data
     * 
     * @param $id
     * @param $attr
     * @return void
     * @throws \Exception
     */
    public static function saveUserProfile($id, $attr)
    {
        try {
            $user = static::where('id', '=', $id)->first();
            if (($user) && (!$user->locked)) {
                if (isset($attr['location'])) {
                    $user->location = $attr['location'];
                }

                if (isset($attr['bio'])) {
                    $user->bio = $attr['bio'];
                }

                if (isset($attr['twitter'])) {
                    $user->twitter = $attr['twitter'];
                    $user->twitter = str_replace('https://twitter.com/', '', $user->twitter);
                    $user->twitter = str_replace('@', '', $user->twitter);
                }

                if ((isset($attr['password'])) && (isset($attr['password_confirmation']))) {
                    if ($attr['password'] !== $attr['password_confirmation']) {
                        throw new \Exception(__('app.password_mismatch'));
                    }

                    static::changePassword($id, $attr['password']);
                }

                if (isset($attr['email'])) {
                    static::changeEMail($id, $attr['email']);
                }

                if (isset($attr['newsletter'])) {
                    $user->newsletter = $attr['newsletter'];
                }

                $av = request()->file('avatar');
                if ($av != null) {
                    $tmpName = md5(random_bytes(55));

                    $av->move(public_path() . '/gfx/avatars/', $tmpName . '.' . $av->getClientOriginalExtension());

                    list($width, $height) = getimagesize(public_path() . '/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension());

                    $avimg = imagecreatetruecolor(128, 128);
                    if (!$avimg)
                        throw new \Exception('imagecreatetruecolor() failed');

                    $srcimage = null;
                    $newname =  $tmpName . '.' . $av->getClientOriginalExtension();
                    switch (ImageModel::getImageType($av->getClientOriginalExtension(), public_path() . '/gfx/avatars/' . $tmpName)) {
                        case IMAGETYPE_PNG:
                            $srcimage = imagecreatefrompng(public_path() . '/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension());
                            imagecopyresampled($avimg, $srcimage, 0, 0, 0, 0, 128, 128, $width, $height);
                            imagepng($avimg, public_path() . '/gfx/avatars/' . $newname);
                            break;
                        case IMAGETYPE_JPEG:
                            $srcimage = imagecreatefromjpeg(public_path() . '/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension());
                            imagecopyresampled($avimg, $srcimage, 0, 0, 0, 0, 128, 128, $width, $height);
                            imagejpeg($avimg, public_path() . '/gfx/avatars/' . $newname);
                            break;
                        default:
                            throw new \Exception('Invalid image file: ' . $av->getClientOriginalExtension());
                            break;
                    }

                    $user->avatar = $newname;
                }

                $user->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Change password
     *
     * @param $id
     * @param $password
     * @throws Exception|Throwable
     */
    public static function changePassword($id, $password)
    {
        try {
            $user = static::where('id', '=', $id)->first();
            if (($user) && (!$user->locked)) {
                $user->password = password_hash($password, PASSWORD_BCRYPT);
                $user->save();

                $html = view('mail.pw_changed', ['name' => $user->username])->render();
                MailerModel::sendMail($user->email, __('app.password_changed'), $html);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Change E-Mail
     *
     * @param $id
     * @param $email
     * @throws Exception|Throwable
     */
    public static function changeEMail($id, $email)
    {
        try {
            $user = static::where('id', '=', $id)->first();
            if (($user) && (!$user->locked) && ($user->email !== $email)) {
                $oldMail = $user->email;
                $user->email = $email;
                $user->save();

                $html = view('mail.email_changed', ['name' => $user->username, 'email' => $email])->render();
                MailerModel::sendMail($user->email, __('app.email_changed'), $html);
                MailerModel::sendMail($oldMail, __('app.email_changed'), $html);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete user account
     * 
     * @param $userId
     * @return void
     * @throws \Exception
     */
    public static function deleteAccount($userId)
    {
        try {
            $user = User::where('id', '=', $userId)->first();
            if (!$user) {
                throw new \Exception('User not found: ' . $userId);
            }

            $reviews = ReviewModel::where('userId', '=', $userId)->get();
            foreach ($reviews as $review) {
                $review->delete();
            }

            $items = ItemModel::where('userId', '=', $userId)->get();
            foreach ($items as $item) {
                $item->delete();
            }

            $reports = ReportModel::where('userId', '=', $userId)->get();
            foreach ($reports as $report) {
                $report->delete();
            }

            $reports = ReportModel::where('entityId', '=', $userId)->where('type', '=', 'ENT_USER')->get();
            foreach ($reports as $report) {
                $report->delete();
            }

            $user->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
