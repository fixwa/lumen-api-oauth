<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|UserAccessToken
     */
    public function accessToken()
    {
        return $this->hasOne(UserAccessToken::class, 'session_id', 'id');
    }
}
