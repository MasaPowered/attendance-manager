<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginTime extends Model
{
    use HasFactory;

    //テーブルを自動的に読み込まない場合これでテーブルを連携させる
    protected $table = 'login_time_table';

    //自動でcreate_atとupdate_atの項目を追加しようとするのを禁止する
    public $timestamps = false;

    protected $fillable = ['id', 'name', 'logintime_status', 'start_time', 'end_time'];

    public $primaryKey = 'id';

    // プライマリキーが自動増分でない
    public $incrementing = false;
}
