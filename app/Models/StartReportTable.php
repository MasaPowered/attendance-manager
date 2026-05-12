<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class StartReportTable extends Model
{
    use HasFactory;

    //テーブルを自動的に読み込まない場合これでテーブルを連携させる
    protected $table = 'start_report_table';

    //自動でcreate_atとupdate_atの項目を追加しようとするのを禁止する
    public $timestamps = false;

    protected $fillable = ['id', 'user_id', 'date', 'arrivalcheck', 'arrivaltime', 'latetime', 'report'];

    public $primaryKey = 'id';

    // プライマリキーが自動増分でない
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
