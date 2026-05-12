<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\StartReportTable;

class UserTable extends Model
{
    use HasFactory;

    //テーブルを自動的に読み込まない場合これでテーブルを連携させる
    protected $table = 'user_table';

    //自動でcreate_atとupdate_atの項目を追加しようとするのを禁止する
    public $timestamps = false;

    protected $fillable = ['id', 'name', 'pass', 'memo'];

    public $primaryKey = 'id';

    // もしプライマリキーが自動増分でない場合
    public $incrementing = false;
}
