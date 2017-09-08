<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * User: pablo <fixwah@gmail.com>
 * Date: 07/09/2017
 * Time: 06:23 PM
 */
class CheckExpiredTokens extends Command
{

    protected $signature = 'tokens:expire';
    protected $description = 'Verifies what tokens needs to expire.';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$allTokens = UserAccessToken::all();
        $allTokens = DB::table('oauth_access_tokens')->get();

        foreach ($allTokens as $token) {
            $expireDate = Carbon::createFromTimestamp($token->expire_time);
            if ($expireDate->isPast()) {
                DB::table('oauth_access_tokens')->where('id', '=', $token->id)->delete();
                $this->info('x');
            } else {
                $this->info('.');
            }
        }

        $this->info('Done.');
    }
}