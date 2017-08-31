<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Illuminate\Support\Facades\Hash;

class UserSession extends Model
{

    protected $table = 'oauth_sessions';

    /**
     * Sessions belongs to a single User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

//    public function accessToken()
//    {
//        return $this->belongsTo(UserAccessToken::class, '');
//    }
}
