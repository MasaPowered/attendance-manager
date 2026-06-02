<?php

namespace App\Http\Controllers;

use App\Models\LoginTime;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\StartReportTable;
use App\Models\EndReportTable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
//2026.06.01 バリデーション追加
use App\Http\Requests\ReportRequest;


class UserReportController extends Controller
{
    /*public function login(Request $request)
    {
        return view('user_work_report.user_login');
    }

    public function post_login(Request $request)
    {
        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
            //return app()->call('App\Http\Controllers\UserReportController@report_start');
            //2026.04.29 redirectじゃないといけないって見た気がするけどリンクの書き方がわからない。
            return redirect()->route('user_report_start');
        } else {
            $msg = 'ログインに失敗しました。';
            return view('user_work_report.user_login', ['error_message' => $msg]);
        }
    }*/

    //public function logout(Request $request)
    //{
        //Auth::guard('web')->logout();
        //2023/10/30 ログ
        /////////////////////////////////////////////////////////////////////////////
        /*$info = new LogWrite();
        $info->append('admin(' . $_SESSION['user_id'] . '): admin_logout')
        ->newline()
        ->commit(LogWrite::APPEND);*/
        /////////////////////////////////////////////////////////////////////////////

        //return view('user_work_report.user_login');
    //}

    public function report_start()
    {
        /*if (auth()->guest()) {
            dd('あなたはゲスト（未ログイン）です。なぜここに入れたのか不思議ですね。');
        }

        if (auth()->check()) {
            dd('あなたはログイン中です。ユーザーIDは: ' . auth()->id());
        }*/

        //------------------------------------------------------------------------------
        //初期化
        $message_array = array();
        $success_message = null;
        $error_message = array();
        $dbo = null;
        $sql = null;
        $res = null;
        $rec = null;
        $arrivaltime = date('H:i:s');
        $login_lock_flg = false;

        $post = null;

        //時間とテーブル参照してログイン時間外ならフォーム非表示にする 2023/10/19
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // ログイン時間取得
        $message_array = LoginTime::Where('id', 1)->first();
        if ($message_array->logintime_status == true) {
            $datetime1 = new DateTime($message_array->start_time);
            $datetime2 = new DateTime($message_array->end_time);
            $datetime3 = new DateTime($arrivaltime);
            if ($datetime3 < $datetime1 || $datetime2 < $datetime3) {
                // ログイン時間外
                $login_lock_flg = true;
            }
        }

        $message_array = array();
        //////////////////////////////////////////////////////////////////////////////////////////////////////

        //ログイン制限フラグ追加 2023/10/19
        if ($login_lock_flg == false) {
            //現在の日付を入力
            $date = date("Y-m-d");
            $userid = User::Where('id', '1')->first();
            //後でログイン入れたらログインユーザで検索するようにする。

            //業務報告メッセージ取得
            $message_array = DB::table('start_report_table')
                ->join('users', 'start_report_table.user_id', '=', 'users.id')
                ->select('start_report_table.*', 'users.name as name')
                ->get();
        }



        $dbh = null;
        //------------------------------------------------------------------------------

        return view('user.report_start_add', ['login_lock_flg' => $login_lock_flg, 'message_array' => $message_array, 'error_message' => $error_message]);
    }

    public function post_report_start(ReportRequest $request)
    {

        //------------------------------------------------------------------------------
        //初期化
        $message_array = array();
        $error_message = array();
        $dbo = null;
        $sql = null;
        $res = null;
        $rec = null;
        $arrivaltime = date('H:i:s');
        $login_lock_flg = false;

        //時間とテーブル参照してログイン時間外ならフォーム非表示にする 2023/10/19
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // ログイン時間取得
        $message_array = LoginTime::Where('id', 1)->first();
        if ($message_array->logintime_status == true) {
            $datetime1 = new DateTime($message_array->start_time);
            $datetime2 = new DateTime($message_array->end_time);
            $datetime3 = new DateTime($arrivaltime);
            if ($datetime3 < $datetime1 || $datetime2 < $datetime3) {
                // ログイン時間外
                $login_lock_flg = true;
            }
        }

        $message_array = array();
        //////////////////////////////////////////////////////////////////////////////////////////////////////

        //ログイン制限フラグ追加 2023/10/19
        if (!empty($request->submit) && $login_lock_flg == false) {
            //現在の日付を入力
            $date = date("Y-m-d");
            //$user = User::Where('id', '1')->first();
            $user = Auth::user();
            //後でログイン入れたらログインユーザで検索するようにする。
            //2026.05.03　ログインユーザを取得するようにした。
            $rec = StartReportTable::Where('user_id', $user->id)->Where('date', $date)->first();
            /*try {
                //SQL作成
                $sql = $dbh->prepare("SELECT userid FROM start_report_table WHERE date=:date AND userid=:user_id");
                $sql->bindParam(':date', $date, PDO::PARAM_STR);
                $sql->bindParam(':user_id', $userid, PDO::PARAM_STR);
                //SQLクエリの実行
                $res = $sql->execute();
                $rec = $sql->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                //$error_message[] = $e->getMessage();
            }*/

            if (!empty($rec)) {
                $error_message[] = "本日はすでに報告されています。メッセージ内容を修正したい場合スタッフにお声がけください。";
            }

            $res = null;
            $rec = null;

            // 入力チェック
            if (empty($request->report)) {
                $error_message[] = "テキストが入力されていません。";
            }

            if (empty($error_message)) {
                if ($request->submit) {
                    $latetime = null;
                    //シフトテーブルの時間を参照する
                    $rec = Shift::Where('user_id', $user->id)->Where('date', $date)->first();
                    /*try {
                        $statment = $dbh->prepare("SELECT * FROM shift_table WHERE user_id=:user_id AND date=:date");

                        $statment->bindParam(':user_id', $userid, PDO::PARAM_STR);
                        $statment->bindParam(':date', $date, PDO::PARAM_STR);
                        $res = $statment->execute();
                        $rec = $statment->fetch(PDO::FETCH_ASSOC);
                    } catch (Exception $e) {
                        $error_message[] = $e->getMessage();
                    }*/

                    if ($rec != false) {
                        $datetime1 = new DateTime($rec['arrivaltime']);
                        $datetime2 = new DateTime($arrivaltime);
                        if ($datetime1 <= $datetime2) {
                            //遅刻
                            $latetime = $arrivaltime;
                        }
                    }
                    //echo $rec['arrivaltime'];
                    $rec = null;
                    $res = null;
                    $statment = null;

                    //業務報告メッセージ登録
                    $res = StartReportTable::create([
                        "user_id" => $user->id,
                        "date" => $date,
                        "arrivalcheck" => true,
                        "arrivaltime" => $arrivaltime,
                        "latetime" => $latetime,
                        "report" => $request->report,
                    ]);
                    //トランザクション開始
                    /*$dbh->beginTransaction();
                    try {
                        //SQL作成
                        $statment = $dbh->prepare("INSERT INTO start_report_table(userid, date, arrivalcheck, arrivaltime, latetime, report)
                        VALUES(:user_id, :date, true, :arrivaltime, :latetime,:wrs)");

                        $statment->bindParam(':user_id', $userid, PDO::PARAM_STR);
                        $statment->bindParam(':date', $date, PDO::PARAM_STR);
                        $statment->bindParam(':arrivaltime', $arrivaltime, PDO::PARAM_STR);
                        $statment->bindParam(':latetime', $latetime, PDO::PARAM_STR);
                        $statment->bindParam(':wrs', $post["report"], PDO::PARAM_STR);

                        //SQL実行
                        $res = $statment->execute();
                        // コミット
                        $res = $dbh->commit();
                    } catch (Exception $e) {
                        //ロールバック
                        //$error_message[] = $dbh->rollBack();
                    }*/


                    if ($res) {
                        $success_message = "追加しました。";
                        //2023/10/31 ログ
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        /*$info = new LogWrite();

                        $info->append('user(' . $_SESSION['user_id'] . '): user_report_arrive_add[' . $userid . ' ' . $date . ' ' . $arrivaltime . ' ' . $post["report"] . ']')
                            ->newline()
                            ->commit(LogWrite::APPEND);*/
                        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    } else {
                        $error_message[] = "追加に失敗しました。";
                    }

                    $res = null;
                    $statment = null;
                }
            }
            //業務報告メッセージ取得
            $message_array = DB::table('start_report_table')
                ->join('users', 'start_report_table.user_id', '=', 'users.id')
                ->select('start_report_table.*', 'users.name as name')
                ->get();

            /*$sql = 'SELECT * FROM start_report_table AS A LEFT OUTER JOIN user_table AS B ON A.userid= B.user_id ORDER BY date DESC';
        try {
            $message_array = $dbh->query($sql);
        } catch (Exception $e) {
        }*/
        }



        //------------------------------------------------------------------------------

        return view('user.report_start_add', ['login_lock_flg' => $login_lock_flg, 'message_array' => $message_array, 'success_message' => $success_message, 'error_message' => $error_message]);
    }

    public function report_end()
    {

        //------------------------------------------------------------------------------
        //初期化
        $message_array = array();
        $error_message = array();
        $dbo = null;
        $sql = null;
        $res = null;
        $rec = null;
        $leavetime = date('H:i:s');
        $login_lock_flg = false;




        //時間とテーブル参照してログイン時間外ならフォーム非表示にする 2023/10/24
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //ログイン時間取得
        $message_array = LoginTime::Where('id', 1)->first();
        if ($message_array->logintime_status == true) {
            $datetime1 = new DateTime($message_array->start_time);
            $datetime2 = new DateTime($message_array->end_time);
            $datetime3 = new DateTime($leavetime);
            if ($datetime3 < $datetime1 || $datetime2 < $datetime3) {
                // ログイン時間外
                $login_lock_flg = true;
            }
        }
        $message_array = array();
        ///////////////////////////////////////////////////////////////////////////////////////////////////

        //ログイン制限フラグ追加 2023/10/19
        if ($login_lock_flg == false) {
            //現在の日付を入力
            $date = date("Y-m-d");
            $userid = User::Where('id', '1')->first();
            //後でログイン入れたらログインユーザで検索するようにする。

            //業務報告メッセージ取得
            //$message_array = User::with('startReportTables')->get();

            $message_array = DB::table('end_report_table')
                ->join('users', 'end_report_table.user_id', '=', 'users.id')
                ->select('end_report_table.*', 'users.name as name')
                ->get();
        }
        //------------------------------------------------------------------------------

        return view('user.report_end_add', ['login_lock_flg' => $login_lock_flg, 'message_array' => $message_array, 'error_message' => $error_message]);
    }

    public function post_report_end(ReportRequest $request)
    {

        //------------------------------------------------------------------------------
        //初期化
        $message_array = array();
        $success_message = null;
        $error_message = array();
        $dbo = null;
        $sql = null;
        $res = null;
        $rec = null;
        $leavetime = date('H:i:s');
        $login_lock_flg = false;

        //時間とテーブル参照してログイン時間外ならフォーム非表示にする 2023/10/24
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //ログイン時間取得
        $message_array = LoginTime::Where('id', 1)->first();
        if ($message_array->logintime_status == true) {
            $datetime1 = new DateTime($message_array->start_time);
            $datetime2 = new DateTime($message_array->end_time);
            $datetime3 = new DateTime($leavetime);
            if ($datetime3 < $datetime1 || $datetime2 < $datetime3) {
                // ログイン時間外
                $login_lock_flg = true;
            }
        }
        $message_array = array();
        ///////////////////////////////////////////////////////////////////////////////////////////////////

        // ログイン制限フラグ追加 2023/10/24
        if (!empty($request->submit) && $login_lock_flg == false) {
            //現在の日付を入力
            $date = date("Y-m-d");
            //$user = User::Where('id', '1')->first();
            $user = Auth::user();
            //後でログイン入れたらログインユーザで検索するようにする。
            //2026.05.03　ログインユーザを取得するようにした。
            $rec = EndReportTable::Where('user_id', $user->id)->Where('date', $date)->first();
            /*try {
                //SQL作成
                $sql = $dbh->prepare("SELECT userid FROM end_report_table WHERE date=:date AND userid=:user_id");
                $sql->bindParam(':date', $date, PDO::PARAM_STR);
                $sql->bindParam(':user_id', $userid, PDO::PARAM_STR);

                //SQLクエリの実行
                $res = $sql->execute();
                $rec = $sql->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                //$error_message[] = $e->getMessage();
            }*/

            if (!empty($rec)) {
                $error_message[] = "本日はすでに報告されています。メッセージ内容を修正したい場合スタッフにお声がけください。";
            }
            $res = null;
            $rec = null;

            // 入力チェック
            if (empty($request->report)) {
                $error_message[] = "テキストが入力されていません。";
            }

            if (empty($error_message)) {
                if ($request->submit) {
                    // 業務報告メッセージ登録
                    $res = EndReportTable::create([
                        "user_id" => $user->id,
                        "date" => $date,
                        "leavecheck" => true,
                        "leavetime" => $leavetime,
                        "report" => $request->report,
                    ]);
                    // トランザクション開始
                    /*$dbh->beginTransaction();
                    try {
                        //SQL作成
                        $statment = $dbh->prepare("INSERT INTO end_report_table(userid, date, leavecheck, leavetime, report) VALUES (:user_id, :date, true,:leavetime, :wrs)");
                        $statment->bindParam(':user_id', $userid, PDO::PARAM_STR);
                        $statment->bindParam(':date', $date, PDO::PARAM_STR);
                        $statment->bindParam(':leavetime', $leavetime, PDO::PARAM_STR);
                        $statment->bindParam(':wrs', $post["report"], PDO::PARAM_STR);

                        //SQL実行
                        $res = $statment->execute();
                        // コミット
                        $res = $dbh->commit();
                    } catch (Exception $e) {
                        //ロールバック
                        $dbh->rollBack();
                    }*/

                    if ($res) {
                        $success_message = "追加しました。";
                        //2023/10/31 ログ
                        /////////////////////////////////////////////////////////////////////////////////////////
                        /*$info = new LogWrite();
                        $info->append('user(' . $_SESSION['user_id'] . '): user_report_leave_add[' . $userid . ' ' . $date . ' ' . $leavetime . ' ' . $post["report"] . '"]')
                            ->newline()
                            ->commit(LogWrite::APPEND);*/
                        /////////////////////////////////////////////////////////////////////////////////////////
                    } else {
                        $error_message[] = "追加に失敗しました。";
                    }
                    $res = null;
                    $statment = null;
                }
            }
            //業務報告メッセージ取得
            $message_array = DB::table('end_report_table')
                ->join(
                    'users',
                    'end_report_table.user_id',
                    '=',
                    'users.id'
                )
                ->select('end_report_table.*', 'users.name as name')
                ->get();

            /*$sql = 'SELECT * FROM end_report_table AS A LEFT OUTER JOIN user_table AS B ON A.userid = B.user_id ORDER BY date DESC';
        try {
            $message_array = $dbh->query($sql);
        } catch (Exception $e) {
        }*/
        }


        //------------------------------------------------------------------------------

        return view('user.report_end_add', ['login_lock_flg' => $login_lock_flg, 'message_array' => $message_array, 'success_message' => $success_message, 'error_message' => $error_message]);
    }
}
