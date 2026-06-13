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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DeleteCheckShiftRequest;
//2026.06.13 祝日ライブラリ追加
use \Yasumi\Yasumi;

class ShiftController extends Controller
{

    public function edit(EditShiftRequest $request)
    {
        //初期化
        //2026.06.10 PRGパターンに変更
        ////////////////////////////////////////////////////
        //scmonthが無かったら今月
        $schmonth = $request->input('schmonth') ?: date('Y-m');
        $schuser_id = $request->input('schuser_id');
        $message_array = collect();
        ////////////////////////////////////////////////////

        //$schmonth = date('Y-m');
        $timestamp = strtotime($schmonth . '-01');
        //$schuser_id = '';
        $user_name = null;

        $today = date('Y-m-j');
        $weeks = [];
        $week = '';

        $html_title = date('Y年n月', $timestamp);

        $day_count = date('t', $timestamp);

        //1日の曜日
        $youbi = date('w', $timestamp);

        //2026.06.10 PRGパターンに変更
        ////////////////////////////////////////////////////
        if (!empty($schuser_id)) {
            $user = User::find($schuser_id);
            $user_name = $user->name;
            $message_array = Shift::where('date', 'LIKE', $schmonth . "%")
                ->where('user_id', $schuser_id)
                ->orderBy('date', 'asc')
                ->orderBy('user_id', 'asc')
                ->get();
        }
        ////////////////////////////////////////////////////

        //2026.06.10 PRGパターンに変更
        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        //第1週目: 空のセルを追加
        $week .= str_repeat('<td></td>', $youbi);
        $row = 0;
        //2026.06.13 祝日追加
        $year = (int)date('Y', strtotime($schmonth . '-01'));
        $holidayProvider = Yasumi::create('Japan', $year, 'ja_JP');
        $holidays = array_values($holidayProvider->getHolidayDates());
        
        for ($day = 1; $day <= $day_count; $day++, $youbi++) {
            $date = $schmonth . (($day < 10) ? '-0' : '-') . $day;
        
            //2026.06.13 祝日追加
            $tdClasses = [];
            if ($today == $date)$tdClasses[] = 'today';
            if (in_array($date, $holidays))$tdClasses[] = 'holiday';
            $tdClassAttr = !empty($tdClasses) ? ' class="' . implode(' ', $tdClasses) . '"' : '';

            $week .= '<td' . $tdClassAttr . '><div>' . $day . '</div>';

            if (!empty($message_array[$row]) && $message_array[$row]->date == $date) {
                $week .= '<div>' . pulldown_shift($day, $message_array[$row]->shift_status) . '</div>';
                //2026.05.28 ブラウザで勝手にフォーマット変わらないように強制的に00:00にするコード追加
                $arrival_val = $message_array[$row]->arrivaltime ? date('H:i', strtotime($message_array[$row]->arrivaltime)) : null;
                $week .= '<div><input type="time" name="arrivaltime[' . $day . ']" value="' . $arrival_val . '" size="10"></div>';
                //2026.05.28 ブラウザで勝手にフォーマット変わらないように強制的に00:00にするコード追加
                $leave_val = $message_array[$row]->leavetime ? date('H:i', strtotime($message_array[$row]->leavetime)) : null;
                $week .= '<div><input type="time" name="leavetime[' . $day . ']" value="' . $leave_val . '" size="10"></div>';
                $row++;
            } else {;
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
        ////////////////////////////////////////////////////////////////////////////////////////////////////////

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
        //2026.06.10 PRGパターンに変更
        ////////////////////////////////////////////////////
        $schmonth = $request->input('schmonth', date('Y-m'));
        $schuser_id = $request->input('schuser_id');
        ////////////////////////////////////////////////////

        //$schmonth = date('Y-m');
        $timestamp = strtotime($schmonth . '-01');
        $posts = [];
        $res = null;
        $rec = [];
        $posts = [];
        $res = null;

        /*if (!empty($request->schmonth)) {
            $schmonth = $request->schmonth;
            $timestamp = strtotime($schmonth . '-01');
        }*/

        //2026.05.20 これ無くていいかも$request->schuser_idだけで成り立つ。
        /*if (!empty($request->schuser_id)) {
            $schuser_id = $request->schuser_id;
        }*/

        $html_title = date('Y年n月', $timestamp);

        $day_count = date('t', $timestamp);

        //1日の曜日
        $youbi = date('w', $timestamp);

        // シフト編集
        //2026.05.28 データの消失を防ぐため、その月の既存データをあらかじめ一括取得しておく
        $query = Shift::where('date', 'LIKE', $schmonth . '%');
        if (!empty($schuser_id)) {
            $query->where('user_id', $schuser_id);
        }

        $existingShifts = $query->get()->mapWithKeys(function ($item) {
            return [$item->user_id . '_' . $item->date => $item];
        });

        //ユーザー指定がある場合
        if (!empty($schuser_id)) {
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
                $key = $schuser_id . '_' . $date;
                $hasExisting = isset($existingShifts[$key]);

                if (!empty($shift) || !empty($arrivaltime) || !empty($leavetime)|| $hasExisting) {

                    $posts[] = [
                        'user_id' => $schuser_id,
                        'date' => $date,
                        'shift_status' => $shift,
                        'arrivaltime' => $arrivaltime,
                        'leavetime' => $leavetime,
                    ];
                }
            }

            $res = Shift::upsert(
                    $posts,
                    ['user_id', 'date'],
                    ['shift_status', 'arrivaltime', 'leavetime']
                );

            if ($res) {
                $success_message = "更新しました。";
                Log::info('User shift updated', [
                    'operator_id' => Auth::id(),
                    'target_id'   => $schuser_id,
                    'details'     => [
                        'month' => $schmonth,
                    ]
                ]);
                //2026.06.10 PRGパターンに変更
                return redirect()->route('admin.shifts.edit', [
                    'schmonth' => $schmonth,
                    'schuser_id' => $schuser_id
                ])->with('success_message', '更新しました。');
            }
        //IDが選択されていない場合全員のシフトをセット
        } else {
            $rec = User::all();

            //2026.05.19 user無しで登録しようとするからエラー判定追加した
            if (empty($rec)) {
                //2026.06.10 PRGパターンに変更
                return redirect()->back()->with(['error_message' => '更新に失敗しました。']);
            }

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

            //2026.05.19 user無しで登録しようとするからエラー判定追加した
            //if (empty($error_message)) {
                $res = Shift::upsert(
                    $posts,
                    ['user_id', 'date'],
                    ['shift_status', 'arrivaltime', 'leavetime']
                );

                if ($res) {

                    //$success_message = "更新しました。";
                    Log::info('All users shift updated', [
                        'operator_id' => Auth::id(),
                        'target_id'   => null, // 全員のため特定不可
                        'details'     => [
                            'month'      => $schmonth,
                            'user_count' => $rec->count(), // 更新対象の人数
                        ]
                    ]);

                    //2026.06.10 PRGパターンに変更
                    return redirect()->route('admin.shifts.edit', ['schmonth' => $schmonth])
                    ->with('success_message', '更新しました。');
                }
            //}
        }
        //2026.06.10 PRGパターンに変更
        return redirect()->back()->with('error_message', '更新に失敗しました。');
    }

    public function delete()
    {
        $schmonth = date('Y-m');

        $searchitem = [
            'schmonth' => $schmonth,
        ];

        return view('admin.shifts.shift_month_delete', ['searchitem' => $searchitem]);
    }

    public function post_delete(DeleteShiftRequest $request)
    {
        $ym = date('Y-m');
        $timestamp = strtotime($ym . '-01');
        $post = null;
        $message_array = [];
        $schmonth = date('Y-m');
        $schuser_id = '';

        if (!empty($request->schmonth)) {
            $schmonth = $request->schmonth;
            $timestamp = strtotime($schmonth . '-01');
        }
        $html_title = date('Y年n月', $timestamp);
        $day_count = date('t', $timestamp);

        //検索
        $message_array = DB::table('shift_table')
            ->join('users', 'shift_table.user_id', '=', 'users.id')
            ->select('shift_table.*', 'users.name as name')
            ->where('date', 'LIKE', $schmonth . '%')
            ->orderBy('user_id')->orderBy('date')
            ->get();

        $searchitem = [
            'schmonth' => $schmonth,
            'schuser_id' => $schuser_id,
            'html_title' => $html_title,
            'day_count' => $day_count,
            'schsubmit' => $request->schsubmit,
        ];

        //2026.06.13 配列をユーザーごとに置き換え
        $formattedShifts = [];

        foreach ($message_array as $shift) {
            $formattedShifts[$shift->user_id]['name'] = $shift->name;
            $dayNum = (int)date('j', strtotime($shift->date));
            $formattedShifts[$shift->user_id]['days'][$dayNum] = $shift->shift_status;
        }

        return view('admin.shifts.shift_month_delete', compact('formattedShifts', 'searchitem'));
    }

    public function delete_check(DeleteShiftRequest $request)
    {
        $searchitem = [
            'schmonth' => $request->schmonth,
        ];

        return view('admin.shifts.shift_month_delete_check', compact('searchitem'));
    }

    public function delete_done(DeleteCheckShiftRequest $request)
    {
        //シフト削除
        //2026.05.19 Postgreに対応させるため変更
        //$res = Shift::query()->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$request->schmonth])->delete();
        $schmonth = $request->schmonth; 

        // その月の「初日」と「末日」を計算
        $startDate = $schmonth . '-01';                   // '2026-05-01'
        $endDate = date('Y-m-t', strtotime($startDate));  // '2026-05-31'

        Shift::whereBetween('date', [$startDate, $endDate])->delete();

        Log::info('Shift deleted', [
            'operator_id' => Auth::id(),
            'details'     => [
                'month'      => $schmonth,
            ]
        ]);

        return redirect()
            ->route('admin.shifts.delete')
            ->with('success_message', "{$schmonth}のシフトを削除しました。");
    }

    public function import()
    {
        $schmonth = session('schmonth');
        $timestamp = strtotime($schmonth. '-01');
        $day_count = date('t', $timestamp);
        $message_array = null;
        if (!empty($schmonth)) {
            $message_array = DB::table('shift_table')
                    ->join('users', 'shift_table.user_id', '=', 'users.id')
                    ->select('shift_table.*', 'users.name as name')
                    ->where('date', 'LIKE', $schmonth . '%')
                    ->orderBy('user_id')->orderBy('date')
                    ->get();
        }

        $searchitem = [
            'timestamp' => $timestamp,
            'day_count' => $day_count,
        ];
        
        return view('admin.shifts.shift_import', ['message_array' => $message_array, 'searchitem' => $searchitem]);
    }

    public function import_done(ImportShiftRequest $request)
    {
        //初期化
        $message_array = [];
        $success_message = null;
        $error_message = [];
        $res = null;
        $date_array = [];
        $shift_array_array = [];
        $shift_array = [];
        $user_id = null;
        $user_id_array = [];
        $posts = [];
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
                    if ($column != 0) {
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

            Shift::upsert($posts, ['user_id', 'date'], ['shift_status']);

            $timestamp = strtotime($date_array[0]);
            $schmonth = date('Y-m', $timestamp);

            Log::info('Some users shift imported', [
                'operator_id' => Auth::id(),
                'target_id'   => null, // 全員のため特定不可
                'details'     => [
                    'month'      => $schmonth,
                ]
            ]);

            //2026.06.11 PRGパターンに変更
            return redirect()->route('admin.shifts.import')->with([
                'success_message' => 'インポートしました。',
                'schmonth' => $schmonth,
            ]);
        }

        //2026.06.10 PRGパターンに変更
        return redirect()->back()->with('error_message', 'インポートに失敗しました。');
    }
}
