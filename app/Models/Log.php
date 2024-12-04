<?php

namespace App\Models;
use Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    public static function info($logg)
    {
        if (Auth::user()) {
            $logs = new Log;
            $logs->users_id = Auth::user()->id;
            $logs->log = Auth::user()->name . ": " . $logg;
            $logs->save();
        }
    }
}
