<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use App\Models\User;

//2026.05.28 バリデーション追加
use App\Http\Requests\EditShiftRequest;
use App\Http\Requests\DeleteShiftRequest;
use App\Http\Requests\ImportShiftRequest;

class ShiftController extends Controller
{

    public function edit(Request $request)
    {
        //初期化
        $schmonth = date('Y-m');
        $timestamp = strtotime($schmonth . '-01');
        $message_array = array();
        $res = null;
        $rec = array();
        $schuser_id = '';
        $user_name = null;

        $today = date('Y-m-j');
        $weeks = [];
        $week = '';
        $year = date("Y");
        $holidays = array();

        $html_title = date('Y年n月', $timestamp);

        $day_count = date('t', $timestamp);

        //1日の曜日
        $youbi = date('w', $timestamp);

        //第1週目: 空のセルを追加
        $week .= str_repeat('<td></td>', $youbi);
        $row = 0;
        for ($day = 1; $day <= $day_count; $day++, $youbi++) {
            $date = $schmonth . (($day < 10) ? '-0' : '-') . $day;

            if ($today == $date) {
                $week .= '<td class="today"><div>' . $day . '</div>';
                //$week .= '</br><input type="text" name="shift' . $day. '" size="10" >';
                $week .= '<div>' . pulldown_shift($day) . '</div>';
                $week .= '<div><input type="time" name="arrivaltime[' . $day . ']" size="10" ></div>';
                $week .= '<div><input type="time" name="leavetime[' . $day . ']" size="10" ></div>';
            } else {
                $week .= '<td><div>' . $day . '</div>';
                //$week .= '</br><input type="text" name="shift' . $day . '" size="10" >';
                $week .= '<div>' . pulldown_shift($day) . '</div>';
                $week .= '<div><input type="time" name="arrivaltime[' . $day . ']" size="10" ></div>';
                $week .= '<div><input type="time" name="leavetime[' . $day . ']" size="10" ></div>';
            }
            $week .= '</td>';

            if ($youbi % 7 == 6 || $day == $day_count) {
                if ($day == $day_count) {
                    $week .= str_repeat('<td></td>', 6 - ($youbi % 7));
                }
                $weeks[] = '<tr>' . $week . '</tr>';
                $week = '';
            }
        }

        $searchitem = [
            'schmonth' => $schmonth,
            'schuser_id' => $schuser_id,
            'html_title' => $html_title,
            'user_name' => $user_name,
        ];

        return view('admin.shifts.shift_edit', ['searchitem' => $searchitem, 'weeks' => $weeks]);
    }

    public function post_edit(EditShiftRequest $request)
    {
        //初期化
        $schmonth = date('Y-m');
        $timestamp = strtotime($schmonth . '-01');
        $message_array = array();
        $posts = array();
        $res = null;
        $rec = array();
        //$schuser_id = '';
        $success_message = null;
        $error_message = array();
        $user_name = null;

        $today = date('Y-m-j');
        $weeks = [];
        $week = '';
        $year = date("Y");
        $holidays = array();

        if (!empty($request->schmonth)) {
            $schmonth = $request->schmonth;
            $timestamp = strtotime($schmonth . '-01');
        }

        //2026.05.20 これ無くていいかも$request->schuser_idだけで成り立つ。
        /*if (!empty($request->schuser_id)) {
            $schuser_id = $request->schuser_id;
        }*/

        $html_title = date('Y年n月', $timestamp);

        $day_count = date('t', $timestamp);

        //1日の曜日
        $youbi = date('w', $timestamp);

        // シフト編集
        if (!empty($request->editsubmit)) {
            //2026.05.28 データの消失を防ぐため、その月の既存データをあらかじめ一括取得しておく
            $query = Shift::where('date', 'LIKE', $schmonth . '%');
            if (!empty($request->schuser_id)) {
                $query->where('user_id', $request->schuser_id);
            }
            $existingShifts = $query->get()->mapWithKeys(function ($item) {
                return [$item->user_id . '_' . $item->date => $item];
            });

            //ユーザー指定がある場合
            if (!empty($request->schuser_id) && !empty($schmonth)) {
                //$row = 0;
                //echo $request->month_arrivaltime;
                //echo $request->month_leavetime;
                //$sql = 'INSERT INTO shift_table(user_id, date, shift_status, arrivaltime, leavetime) VALUES';


                for ($day = 1; $day <= $day_count; $day++) {
                    $shift = $request->input("shift.$day");
                    $arrivaltime = $request->input("arrivaltime.$day");
                    $leavetime = $request->input("leavetime.$day");
                    
                    //一括入力があれば優先する
                    if (!empty($request->month_arrivaltime)) {
                        $arrivaltime = $request->month_arrivaltime;
                    }

                    if (!empty($request->month_leavetime)) {
                        $leavetime = $request->month_leavetime;
                    }

                    if (!empty($request->month_shift)) {
                        $shift = $request->month_shift;
                    }

                    //休みの場合時間をnullにする。
                    if($shift === '休'){
                        $arrivaltime = null;
                        $leavetime = null;
                    }

                    $date = $schmonth . (($day < 10) ? '-0' : '-') . $day;

                    //2026.05.28 この日のデータがすでにDBに存在するかどうかを判定
                    $key = $request->schuser_id . '_' . $date;
                    $hasExisting = isset($existingShifts[$key]);

                    if (!empty($shift) || !empty($arrivaltime) || !empty($leavetime)|| $hasExisting) {

                        $posts[] = [
                            'user_id' => $request->schuser_id,
                            'date' => $date,
                            'shift_status' => $shift,
                            'arrivaltime' => $arrivaltime,
                            'leavetime' => $leavetime,
                        ];
                    }
                }

                $res = Shift::upsert(
                        $posts,                                         // 更新・挿入するデータの配列
                        ['user_id', 'date'],                            // ユニーク判定に使用するカラム
                        ['shift_status', 'arrivaltime', 'leavetime']    // 更新対象のカラム
                    );

                if ($res) {
                    $success_message = "更新しました。";
                    //2023/10/30 ログ
                    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    /*$info = new LogWrite();
                    $info->append('admin(' . $_SESSION['user_id'] . '): shift_edit[' . $post["schuser_id"] . ' ' . $schmonth . ']')
                        ->newline()
                        ->commit(LogWrite::APPEND);*/
                    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                } else {
                    $error_message[] = "更新に失敗しました。";
                }
            } else if (empty($request->schuser_id) && !empty($schmonth)) {
                //IDが選択されていない場合全員のシフトをセット
                //$row = 0;
                /*try {
                    //ユーザーを取得
                    $sql = 'SELECT user_id FROM user_table';
                    $rec = $dbh->query($sql);
                } catch (Exception $e) {
                    //$error_message[] = $e->getMessage();
                }*/
                $rec = User::all();

                //2026.05.19 user無しで登録しようとするからエラー判定追加した
                if (empty($rec)) {
                    $error_message[] = "利用者が登録されていません。";
                }

                //シフトを登録
                //$sql = 'INSERT INTO shift_table(user_id, date, shift_status, arrivaltime, leavetime) VALUES';

                foreach ($rec as $value) {
                    for ($day = 1; $day <= $day_count; $day++) {
                        $shift = $request->input("shift.$day");
                        $arrivaltime = $request->input("arrivaltime.$day");
                        $leavetime = $request->input("leavetime.$day");
                        
                        //一括入力があれば優先する
                        if (!empty($request->month_arrivaltime)) {
                            $arrivaltime = $request->month_arrivaltime;
                        }
                        if (!empty($request->month_leavetime)) {
                            $leavetime = $request->month_leavetime;
                        }
                        if (!empty($request->month_shift)) {
                            $shift = $request->month_shift;
                        }

                        $date = $schmonth . (($day < 10) ? '-0' : '-') . $day;
                        
                        //2026.05.28【追加】画面からの入力が空なら、各ユーザーの元のDBの値を維持する
                        $key = $value->id . '_' . $date;
                        if (isset($existingShifts[$key])) {
                            if ($shift === null || $shift === '') {
                                $shift = $existingShifts[$key]->shift_status;
                            }
                            if ($arrivaltime === null || $arrivaltime === '') {
                                $arrivaltime = $existingShifts[$key]->arrivaltime;
                            }
                            if ($leavetime === null || $leavetime === '') {
                                $leavetime = $existingShifts[$key]->leavetime;
                            }
                        }

                        //休みの場合時間をnullにする。
                        if($shift === '休'){
                            $arrivaltime = null;
                            $leavetime = null;
                        }

                        if (!empty($shift) || !empty($arrivaltime) || !empty($leavetime)) {

                            $posts[] = [
                                'user_id' => $value->id,
                                'date' => $date,
                                'shift_status' => $shift,
                                'arrivaltime' => $arrivaltime,
                                'leavetime' => $leavetime,
                            ];
                        }
                    }
                }

                /*$sql .= ' ON DUPLICATE KEY UPDATE shift_status=VALUES(shift_status), arrivaltime=VALUES(arrivaltime), leavetime=VALUES(leavetime)';

                $dbh->beginTransaction();
                try {
                    $res = $dbh->query($sql);

                    //$res = $statment->execute();
                    $res = $dbh->commit();
                } catch (Exception $e) {
                    //ロールバック
                    $dbh->rollBack();
                    //$error_message[] = $e->getMessage();
                }*/

                //2026.05.19 user無しで登録しようとするからエラー判定追加した
                if (empty($error_message)) {
                    $res = Shift::upsert(
                        $posts,                                         // 更新・挿入するデータの配列
                        ['user_id', 'date'],                            // ユニーク判定に使用するカラム
                        ['shift_status', 'arrivaltime', 'leavetime']    // 更新対象のカラム
                    );

                    if ($res) {

                        $success_message = "更新しました。";
                        //2023/10/30 ログ
                        ////////////////////////////////////////////////////////////////////////////////////////////////
                        /*$info = new LogWrite();
                        $info->append('admin(' . $_SESSION['user_id'] . '): shift_edit[ALL USER  ' . $schmonth . ']')
                            ->newline()
                            ->commit(LogWrite::APPEND);*/
                        ////////////////////////////////////////////////////////////////////////////////////////////////     
                    } else {
                        //2026.05.19 多分ミス。
                        //$error_message[] = "更新しました。";
                        $error_message[] = "更新に失敗しました。";
                    }
                }

                //$statment = null;
                //echo $sql;
            }
        }
        //検索
        if (!empty($request->schsubmit) || !empty($request->editsubmit)) {
            if (empty($request->schuser_id)) {
                $error_message[] = "利用者IDが入力されていません。";
            } else {
                // 利用者登録チェック
                /*try {
                    //SQL作成
                    $sql = $dbh->prepare("SELECT user_id, name FROM user_table WHERE user_id=?");
                    $data[] = $request->schuser_id;
                    //SQLクエリの実行
                    $res = $sql->execute($data);
                    $rec = $sql->fetch(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                }*/

                $rec = User::query()->where('id', $request->schuser_id)->first();

                if (empty($rec)) {
                    $error_message[] = "このユーザーは登録されていません。";
                }
                //$res = null;
                $user_name = $rec->name;
            }
            if (empty($error_message)) {
                /*$sql = 'SELECT * FROM shift_table WHERE 1';
                $sql .= ' AND date LIKE ' . "'" . $schmonth . "%'";
                $sql .= ' AND user_id = ' . "'" . $request->schuser_id . "'";
                $sql .= ' ORDER BY date, user_id';
                try {
                    $stmt = $dbh->query($sql);
                    $message_array = $stmt->fetchAll(PDO::FETCH_BOTH);
                } catch (Exception $e) {
                }*/

                $query = Shift::query();
                $query = $query->where('date', 'LIKE', $schmonth . "%");
                $query = $query->where('user_id', $request->schuser_id);
                $query = $query->orderBy('date', 'asc')->orderBy('user_id', 'asc');
                $message_array = $query->get();
            }
        }

        //第1週目: 空のセルを追加
        $week .= str_repeat('<td></td>', $youbi);
        $row = 0;
        for ($day = 1; $day <= $day_count; $day++, $youbi++) {
            //$day = 1;
            //foreach ($message_array as $value) {
            $date = $schmonth . (($day < 10) ? '-0' : '-') . $day;

            if ($today == $date) {
                $week .= '<td class="today"><div>' . $day . '</div>';
                if (!empty($message_array[$row]) && $message_array[$row]->date == $date) {
                    //$week.= '</br><input type="text" name="shift' . $day. '" value="' . $message_array[$row]['shift_status'] . size="10">'; 
                    //$week .= '</br>' . $message_array[$day - 1]['shift_status'];
                    $week .= '<div>' . pulldown_shift($day, $message_array[$row]->shift_status) . '</div>';
                    //2026.05.28 ブラウザで勝手にフォーマット変わらないように強制的に00:00にするコード追加
                    $arrival_val = $message_array[$row]->arrivaltime ? date('H:i', strtotime($message_array[$row]->arrivaltime)) : null;
                    $week .= '<div><input type="time" name="arrivaltime[' . $day . ']" value="' . $arrival_val . '" size="10"></div>';
                    //2026.05.28 ブラウザで勝手にフォーマット変わらないように強制的に00:00にするコード追加
                    $leave_val = $message_array[$row]->leavetime ? date('H:i', strtotime($message_array[$row]->leavetime)) : null;
                    $week .= '<div><input type="time" name="leavetime[' . $day . ']" value="' . $leave_val . '" size="10"></div>';
                    $row++;
                } else {
                    //$week .= '</br><input type="text" name="shift' . $day. '" size="10" >';
                    $week .= '<div>' . pulldown_shift($day) . '</div>';
                    $week .= '<div><input type="time" name="arrivaltime[' . $day . ']" size="10" ></div>';
                    $week .= '<div><input type="time" name="leavetime[' . $day . ']" size="10" ></div>';
                }
            } else {
                $week .= '<td><div>' . $day . '</div>';
                if (!empty($message_array[$row]) && $message_array[$row]->date == $date) {
                    //$week.= '</br><input type="text" name="shift' . $day. '" value="' . $message_array[$row]['shift_status'] . '" size="10" >';
                    //$week .= '</br>' . $message_array[$day - 1]['shift_status'];
                    $week .= '<div>' . pulldown_shift($day, $message_array[$row]->shift_status) . '</div>';
                    //2026.05.28 ブラウザで勝手にフォーマット変わらないように強制的に00:00にするコード追加
                    $arrival_val = $message_array[$row]->arrivaltime ? date('H:i', strtotime($message_array[$row]->arrivaltime)) : null;
                    $week .= '<div><input type="time" name="arrivaltime[' . $day . ']" value="' . $arrival_val . '" size="10"></div>';
                    //2026.05.28 ブラウザで勝手にフォーマット変わらないように強制的に00:00にするコード追加
                    $leave_val = $message_array[$row]->leavetime ? date('H:i', strtotime($message_array[$row]->leavetime)) : null;
                    $week .= '<div><input type="time" name="leavetime[' . $day . ']" value="' . $leave_val . '" size="10" ></div>';
                    $row++;
                } else {
                    //$week .= '</br><input type="text" name="shift' . $day . '" size="10" >';
                    $week .= '<div>' . pulldown_shift($day) . '</div>';
                    $week .= '<div><input type="time" name="arrivaltime[' . $day . ']" size="10" ></div>';
                    $week .= '<div><input type="time" name="leavetime[' . $day . ']" size="10" ></div>';
                }
            }
            $week .= '</td>';

            if ($youbi % 7 == 6 || $day == $day_count) {
                if ($day == $day_count) {
                    $week .= str_repeat('<td></td>', 6 - ($youbi % 7));
                }
                $weeks[] = '<tr>' . $week . '</tr>';
                $week = '';
            }
            //$day++;
            //$youbi++;
        }

        $searchitem = [
            'schmonth' => $schmonth,
            'schuser_id' => $request->schuser_id,
            'html_title' => $html_title,
            'user_name' => $user_name,
        ];

        return view('admin.shifts.shift_edit', ['success_message' => $success_message, 'error_message' => $error_message, 'searchitem' => $searchitem, 'weeks' => $weeks]);
    }

    public function delete()
    {
        $schmonth = date('Y-m');
        $timestamp = strtotime($schmonth . '-01');
        $schuser_id = '';

        $html_title = date('Y年n月', $timestamp);
        $day_count = date('t', $timestamp);

        $searchitem = [
            'schmonth' => $schmonth,
            'schuser_id' => $schuser_id,
            'html_title' => $html_title,
            'day_count' => $day_count,
        ];

        return view('admin.shifts.shift_month_delete', ['searchitem' => $searchitem]);
    }

    public function post_delete(DeleteShiftRequest $request)
    {
        $ym = date('Y-m');
        $timestamp = strtotime($ym . '-01');
        $post = null;
        $message_array = array();
        $res = null;
        $rec = array();
        $schmonth = date('Y-m');
        $schuser_id = '';

        $today = date('Y-m-j');
        $weeks = [];
        $week = '';
        $year = date("Y");
        $holidays = array();
        $prev_user_id = '';
        $endflg = false;
        $row = 0;

        if (!empty($request->schmonth)) {
            $schmonth = $request->schmonth;
            $timestamp = strtotime($schmonth . '-01');
        }
        $html_title = date('Y年n月', $timestamp);
        $day_count = date('t', $timestamp);
        // 1日の曜日
        $youbi = date('w', $timestamp);


        //検索
        if (!empty($request->schsubmit)) {
            if (empty($error_message)) {
                /*$sql = 'SELECT A.user_id, A.date, B.name, A.shift_status FROM shift_table AS A LEFT OUTER JOIN user_table AS B ON A.user_id = B.user_id WHERE 1';

                //---検索要素がある場合 SQL追加---
                //月
                $sql .= ' AND A.date LIKE ' . "'" . $schmonth . "%'";
                //利用者ID
                //if (!empty($post['schuser_id'])) {
                //$sql .= ' AND user_id = ' . "'" . $post['schuser_id'] . "'";
                //}

                $sql .= ' ORDER BY A.user_id, A.date';
                try {
                    $stmt = $dbh->query($sql);
                    $message_array = $stmt->fetchAll(PDO::FETCH_BOTH);
                } catch (Exception $e) {
                    $error_message[] = $e->getMessage();
                }*/

                $message_array = DB::table('shift_table')
                    ->join('users', 'shift_table.user_id', '=', 'users.id')
                    ->select('shift_table.*', 'users.name as name')
                    ->where('date', 'LIKE', $schmonth . '%')
                    ->orderBy('user_id')->orderBy('date')
                    ->get();
            }
            //var_dump($message_array);
            //echo $sql;
        }
        //シフト削除
        /*if (!empty($post['deletesubmit'])) {
            $dbh->beginTransaction();
            try {
            //if (!empty($post['schmonth'])) {
            //$sql .= ' AND date LIKE ' . "'" . $post['schmonth'] . "%'";

            //SQL作成
            $statment = $dbh->prepare("DELETE FROM shift_table WHERE date LIKE :data");

            // 値をセット
            $statment->bindParam(': data', "'". $schmonth."%'", PDO::PARAM_STR);

            //SQL実行
            $res = $statment->execute();

            // コミット
            $res = $dbh->commit();
            } catch (Exception $e) {
            //ロールバック
            $dbh->rollBack();
            }

            if ($res) {
                $success_message = "削除しました。 ";
            } else {
                $error_message[] = "削除に失敗しました。";
            }

            $statment = null;
        }*/

        $searchitem = [
            'schmonth' => $schmonth,
            'schuser_id' => $schuser_id,
            'html_title' => $html_title,
            'day_count' => $day_count,
            'schsubmit' => $request->schsubmit,
        ];

        return view('admin.shifts.shift_month_delete', ['message_array' => $message_array, 'searchitem' => $searchitem]);
    }

    public function delete_check(DeleteShiftRequest $request)
    {
        $searchitem = [
            'schmonth' => $request->schmonth,
        ];

        return view('admin.shifts.shift_month_delete_check', ['searchitem' => $searchitem]);
    }

    public function delete_done(DeleteShiftRequest $request)
    {
        //初期化
        $success_message = null;
        $error_message = array();
        $res = null;

        //シフト削除
        if (!empty($request->schmonth)) {

            /*try {
                //SQL作成
                $statment = $dbh->prepare('DELETE FROM shift_table WHERE DATE_FORMAT(date, "%Y-%m") = :data');
                $data = $post['schmonth'];
                //値をセット
                $statment->bindParam(':data', $data, PDO::PARAM_STR);
                //SQL実行
                $res = $statment->execute();
                // コミット
                $res = $dbh->commit();
            } catch (Exception $e) {
                $error_message[] = $e->getMessage();
                //ロールバック
                $dbh->rollBack();
            }*/

            //2026.05.19 Postgreに対応させるため変更
            //$res = Shift::query()->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$request->schmonth])->delete();
            $yearMonth = $request->schmonth; 

            // その月の「初日」と「末日」を計算
            $startDate = $yearMonth . '-01';                  // '2026-05-01'
            $endDate = date('Y-m-t', strtotime($startDate));  // '2026-05-31'

            // Eloquent（エロクアント）でスマートに削除
            $res = Shift::whereBetween('date', [$startDate, $endDate])->delete();

            

            if ($res) {
                $success_message = "削除しました。";
                //2023/10/31 ログ
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /*$info = new Logwrite();

                $info->append('admin(' . $_SESSION['user_id'] . '): shift_month_delete[' . $data . ']')
                    ->newline()
                    ->commit(LogWrite::APPEND);*/
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } else {
                $error_message[] = "削除に失敗しました。";
            }
        }

        return view('admin.shifts.shift_month_delete_done', ['success_message' => $success_message, 'error_message' => $error_message]);
    }

    public function import()
    {

        return view('admin.shifts.shift_import');
    }

    public function import_done(ImportShiftRequest $request)
    {
        //初期化
        $message_array = array();
        $success_message = null;
        $error_message = array();
        $res = null;
        $date_array = array();
        $shift_array_array = array();
        $shift_array = array();
        $user_id = null;
        $user_id_array = array();
        $posts = array();
        $timestamp = null;
        $day_count = null;
        $schmonth = null;
        $rowData = [];
        $csvErrors = [];                // エラーメッセージをためる配列


        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // ファイルを読み込み
        $fp = fopen($filePath, 'r');

        $row = 0;
        $column = 0;
        while ($line = fgetcsv($fp)) {
            //var_dump($line);
            if ($row == 0) {
                //日付取得
                foreach ($line as $value) {
                    if ($column == 0) {
                        /*if (!preg_match('/^([1-9][0-9]{3})\/([1-9]{1}|1[0-2]{1})\([1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1})$/', $value)) {
                        $error_message [] = "正しい書式ではありません。";
                        echo $value;
                        break;
                        */
                    } else {
                        //$value = str_replace('/', '-', $value);
                        $date_array[] = $value;
                    }
                    $column++;
                }
            } else {
                $shift_array = [];
                // シフト取得
                foreach ($line as $value) {
                    if ($column >= 1) {
                        $shift_array[] = $value;
                    } else {
                        $user_id = $value;
                        $user_id_array[] = $user_id;
                    }
                    $column++;
                }
                $shift_array_array[$user_id] = $shift_array;
            }
            $column = 0;
            $row++;
        }
        fclose($fp);

        if (empty($error_message)) {
            if (empty($user_id_array)) {
                $error_message[] = "利用者IDが見つかりません。";
            }

            if (empty($shift_array_array)) {
                $error_message[] = "シフトデータが見つかりません。";
            }
            if (empty($error_message)) {
                //---SQL作成---
                /*$sql = 'INSERT INTO shift_table(user_id, date, shift_status) VALUES';
                $row = 0;*/
                foreach ($user_id_array as $user_value) {
                    //2026.05.15 ユーザーが存在するか確認
                    if (!User::where('id', $user_value)->exists()) {
                        // 存在しない場合はスキップ
                        continue; 
                    }
                    for ($i = 0, $max = count($date_array); $i < $max; $i++) {

                        $rowData = [
                            'user_id' => $user_value,
                            'date' => $date_array[$i],
                            'shift_status' => $shift_array_array[$user_value][$i],
                        ];

                        //2026.05.29 バリデーション追加
                        $validator = Validator::make($rowData, [
                            'user_id'           => 'required|integer|max:9999999999',
                            'date'              => 'required|date_format:Y/m/d',
                            'shift_status'      => 'nullable|in:出勤,休,確休,在宅',
                        ], [
                            'user_id.integer'   => '利用者IDは半角数字で入力してください。',
                            'user_id.max'       => '利用者IDが長すぎます。',
                            'date.date_format'  => '日付の形式が正しくありません。Y/m/d形式に変更してください',
                            'shift_status.in'   => 'シフトの選択肢が正しくありません。',
                        ]);
                        if ($validator->fails()) {
                            foreach ($validator->errors()->all() as $error) {
                                $csvErrors[] = "{$date_array[$i]}: {$error}";
                            }
                        }

                        $posts[] = $rowData;
                    }
                }

                //2026/05.29 csvがバリデーションで引っかかったらインポートを止める。
                if (!empty($csvErrors)) {
                    return redirect()->back()
                        ->withErrors(['csv_errors' => $csvErrors])
                        ->withInput();
                }

                $res = Shift::upsert($posts, ['user_id', 'date'], ['shift_status']);

                if ($res) {
                    $timestamp = strtotime($date_array[0]);
                    $day_count = date('t', $timestamp);
                    $schmonth = date('Y-m', $timestamp);

                    $message_array = DB::table('shift_table')
                        ->join('users', 'shift_table.user_id', '=', 'users.id')
                        ->select('shift_table.*', 'users.name as name')
                        ->where('date', 'LIKE', $schmonth . '%')
                        ->orderBy('user_id')->orderBy('date')
                        ->get();

                    $success_message = "インポートしました。 ";
                    //2023/10/30 ログ
                    ////////////////////////////////////////////////////////////////////////////////////////////////
                    /*$info = new LogWrite();
                    $info->append('admin (' . $_SESSION['user_id'] . '): shift_import[' . $schmonth . ']')
                        ->newline()
                        ->commit(LogWrite::APPEND);*/
                    ////////////////////////////////////////////////////////////////////////////////////////////////
                } else {
                    $error_message[] = "インポートに失敗しました。";
                }
            }
        }

        $searchitem = [
            'timestamp' => $timestamp,
            'day_count' => $day_count,
        ];

        return view('admin.shifts.shift_import_done', ['message_array' => $message_array, 'success_message' => $success_message, 'error_message' => $error_message, 'searchitem' => $searchitem]);
    }
}
