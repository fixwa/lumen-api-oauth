<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Illuminate\Support\Facades\Hash;

class UserAccessToken extends Model
{

    protected $table = 'oauth_access_tokens';

    /**
     * Sessions belongs to a single UserSession.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|UserSession
     */
    public function session()
    {
        return $this->belongsTo(UserSession::class, 'session_id', 'id');
    }
}
