<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tweet extends Model
{

    use SoftDeletes;

    protected $fillable = ['id', 'user_id', 'body', 'feel'];

    protected $dates = ['deleted_at'];

    public function getCreatedAtAttribute($date)
    {
        //return "2017-09-04T22:08:41.038Z";
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d\TH:i:s.u\Z');
    }

    public function getUpdatedAtAttribute($date)
    {
        //return "2017-09-04T22:08:41.038Z";
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d\TH:i:s.u\Z');
    }


    /**
     * Tweets belongs to one User.
     *
     * @return BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}