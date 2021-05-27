<?php


namespace Modules\Esia\Entities;


use App\User;
use Illuminate\Database\Eloquent\Model;

class EsiaAccount extends Model
{
    protected $table = 'esia_accounts';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
