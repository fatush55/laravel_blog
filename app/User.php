<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    const IS_ADMIN = 1;
    const IS_NORMAL = 0;
    const IS_BAN = 1;
    const IS_UNDER_BAN = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function add($fields)
    {
        $user = new static;
        $user->fill($fields);
        $user->password = bcrypt($fields['password']);
        $user->save();

        return $user;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->password = bcrypt($fields['password']);
        $this->save();
    }

    public function remove()
    {
        Storage::delete('upload/user/' . $this->image);
        $this->delete();
    }

    public function uploadAvatar($image)
    {
        if ($image == null) return;

        Storage::delete('upload/user/' . $this->image);
        $filename = str_random(10) . '.' . $image->extension();
        $image->saveAs('upload/user', $filename);
        $this->image = $filename;
        $this->save();
    }

    public function getAvatar()
    {
        if ($this->image == null) {
            return '/image/user/no-avatar.png';
        }
        return '/upload/user/' . $this->image;
    }

    public function makeAdmin()
    {
        $this->is_admin = User::IS_ADMIN;
        $this->save();
    }

    public function makeUnBan()
    {
        $this->is_admin = User::IS_NORMAL;
        $this->save();
    }

    public function toggleAdmin($value)
    {
        if ($value == null) {
            return $this->makeUnBan();
        }

        return $this->makeAdmin();
    }

    public function makeBan()
    {
        $this->status = User::IS_BAN;
        $this->save();
    }

    public function makeUnderBan()
    {
        $this->status = User::IS_UNDER_BAN;
        $this->save();
    }

    public function toggleBan($value)
    {
        if ($value == null){
             return $this->makeUnBan();
        }

        return $this->makeBan();
    }


}
