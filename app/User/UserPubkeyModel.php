<?php

namespace App\User;

use Illuminate\Database\Eloquent\Model;

class UserPubkeyModel extends Model
{
    //
    public $table='p_pubkeys';
    protected $primaryKey='uid';

}
