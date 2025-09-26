<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model {
    protected $fillable = ['user_id','ip','user_agent','success','logged_in_at'];
}
