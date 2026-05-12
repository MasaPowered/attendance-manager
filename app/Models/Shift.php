<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Shift extends Model
{
    use HasFactory;

    //テーブルを自動的に読み込まない場合これでテーブルを連携させる
    protected $table = 'shift_table';

    //自動でcreate_atとupdate_atの項目を追加しようとするのを禁止する
    public $timestamps = false;

    protected $fillable = ['user_id', 'date', 'shift_status', 'arrivaltime', 'leavetime'];

    public $primaryKey = ['user_id', 'date'];

    // もしプライマリキーが自動増分でない場合
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
