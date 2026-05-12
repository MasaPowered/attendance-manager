<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportView extends Model
{
    use HasFactory;

    //テーブルを自動的に読み込まない場合これでテーブルを連携させる
    protected $table = 'report_view';

    //自動でcreate_atとupdate_atの項目を追加しようとするのを禁止する
    public $timestamps = false;

    protected $fillable = ['arriveid', 'leaveid', 'user_id', 'date', 'name', 'shift_status', 'arrivalcheck', 'leavecheck', 'arrivaltime', 'leavetime', 'latetime', 'startreport', 'endreport'];

    public $primaryKey = ['user_id', 'date'];

    // プライマリキーが自動増分でない
    public $incrementing = false;
}
