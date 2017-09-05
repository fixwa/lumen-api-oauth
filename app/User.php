<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password', 'imageUrl'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'password', 'is_admin',
    ];

    /**
     * Verify user's credentials.
     *
     * @param  string $email
     * @param  string $password
     * @return int|boolean
     * @see    https://github.com/lucadegasperi/oauth2-server-laravel/blob/master/docs/authorization-server/password.md
     */
    public function verify($email, $password){

        $user = User::where('email', $email)->first();

        if($user && Hash::check($password, $user->password)){
            return $user->id;
        }

        return false;
    }

    /**
     * A User can have Many Sessions
     *
     * @return HasMany|UserSession[]
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'owner_id');
    }

    /**
     * User has many Tweets.
     *
     * @return HasMany|Tweet[]
     */
    public function tweets()
    {
        return $this->hasMany(Tweet::class, 'user_id', 'id');
    }
}
