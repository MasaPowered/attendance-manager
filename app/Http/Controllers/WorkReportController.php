<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportView;
use App\Models\StartReportTable;
use App\Models\EndReportTable;

//ファイルダウンロード用 2024/09/07
use Illuminate\Support\Facades\Storage;

//2026.05.25 バリデーション追加
use App\Http\Requests\EditWorkReportRequest;
use App\Http\Requests\DetailSearchFormRequest;
use App\Http\Requests\DeleteReportsRequest;
use App\Http\Requests\SelectReportRequest;
use App\Http\Requests\DeleteCheckReportsRequest;
//2026.06.08 Log用に追加
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WorkReportController extends Controller
{
    /*public function list(Request $request)
    {
        $message_array = ReportView::simplePaginate(5);

        //-----------------------------------------------------------------------------------------------------------------
        //dd($message_array);
        //-----------------------------------------------------------------------------------------------------------------

        return view('admin.work_reports.report_list', ['message_array' => $message_array]);
    }*/

    public function list(DetailSearchFormRequest $request)
    {
        //初期化
        $message_array = array();
        $error_message = array();
        $query = null;
        $searchitem = null;

        if (!empty($request->reset)) {
            $request = null;
        }

        if (!empty($request->schsubmit)) {
            $query = ReportView::query();

            //---検索条件---
            //日
            if (!empty($request->schdate)) {
                $query = $query->where('date', $request->schdate);
            }
            //月
            if (!empty($request->schmonth)) {
                $query = $query->where('date', 'LIKE', $request->schmonth . "%");
            }
            //利用者ID
            if (!empty($request->schuser_id)) {
                $query = $query->where('user_id', $request->schuser_id);
            }
            //シフト
            if (!empty($request->month_shift)) {
                $query = $query->where('shift_status', $request->month_shift);
            }

            //出勤報告か退勤報告にチェックが入っていれば適応する。
            if (!empty($request->arriveradio) || !empty($request->leaveradio)) {
                //$sql .= ' AND (';
                $query = $query->where(function ($q) use ($request) {
                    //出勤報告
                    if ($request->arriveradio == "ari") {
                        $q = $q->whereNotNull('arrivalcheck');
                    } else {
                        //「nashi」かチェックが無ければ出勤報告無しにする。
                        $q = $q->whereNull('arrivalcheck');
                    }

                    //AND OR選択
                    if ($request->andorradio == "and") {
                        //退勤報告
                        if ($request->leaveradio == "ari") {
                            $q = $q->whereNotNull('leavecheck');
                        } else {
                            //「nashi」かチェックが無ければ退勤報告無しにする。
                            $q = $q->whereNull('leavecheck');
                        }
                    } else if ($request->andorradio == "or") {
                        //退勤報告
                        if ($request->leaveradio == "ari") {
                            $q = $q->orWhereNotNull('leavecheck');
                        } else {
                            //「nashi」かチェックが無ければ退勤報告無しにする。
                            $q = $q->orWhereNull('leavecheck');
                        }
                    } else {
                        //退勤報告 ANDORが入っていなかったら「OR」にする。
                        if ($request->leaveradio == "ari") {
                            $q = $q->orWhereNotNull('leavecheck');
                        } else {
                            $q = $q->orWhereNull('leavecheck');
                        }
                    }
                });
            }

            // 遅刻あり
            if (!empty($request->checkbox)) {
                $query = $query->whereNotNull('latetime');
            }

            $query = $query->orderBy('date', 'desc')->orderBy('user_id', 'desc');

            $message_array = $query->paginate(50);

            $searchitem = [
                'schsubmit' => $request->schsubmit,
                'schdate' => $request->schdate,
                'schmonth' => $request->schmonth,
                'schuser_id' => $request->schuser_id,
                'month_shift' => $request->month_shift,
                'arriveradio' => $request->arriveradio,
                'andorradio' => $request->andorradio,
                'leaveradio' => $request->leaveradio,
                'checkbox' => $request->checkbox,
            ];
        }

        return view('admin.work_reports.report_list', ['message_array' => $message_array, 'searchitem' => $searchitem]);
    }

    public function edit(SelectReportRequest $request)
    {

        //初期化
        $message_array = array();
        $error_message = array();

        if (empty($request->radio)) {
            $error_message[] = "ラジオボタンを選択してください。";
        } else {
            $date = substr($request->radio, 0, 10);
            $userid = substr($request->radio, 11);

            $message_array = ReportView::query()->where('date', $date)->where('user_id', $userid)->first();
            /*try {
                $sql = $dbh->prepare('SELECT * FROM report_view WHERE date = :date AND userid= :userid');
                $sql->bindParam(':date', $date, PDO::PARAM_STR);
                $sql->bindParam(':userid', $userid, PDO::PARAM_STR);

                //SQLクエリの実行
                $res = $sql->execute();
                $message_array = $sql->fetch(PDO::FETCH_ASSOC);

                //$message_array = $dbh->query($sql);
            } catch (Exception $e) {
                //echo substr($post['radio'], 0, 10) . '<br>';
                //echo substr($post['radio'], 11). '<br>';
            }*/
        }

        return view('admin.work_reports.report_edit', ['message_array' => $message_array, 'error_message' => $error_message]);
    }

    public function edit_done(EditWorkReportRequest $request)
    {
        //初期化
        $message_array = array();
        $success_message = null;
        $error_message = array();

        if (!empty($request->submitbtn)) {
            //出勤テーブル更新
            if (!empty($request->arriveid)) {

                $startReportTable = StartReportTable::Where('id', $request->arriveid)->first();
                if (!empty($request->date)) {
                    $startReportTable->date = $request->date;
                }

                if (!empty($request->arrivaltime)) {
                    $startReportTable->arrivaltime = $request->arrivaltime;
                }
                if (!empty($request->latetime)) {
                    $startReportTable->latetime = $request->latetime;
                }
                $startReportTable->report = $request->startreport;
                $res = $startReportTable->save();

                if ($res) {
                    $success_message = "修正しました。";
                    //echo 'test';
                    //2023/10/31 ログ
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    /*$info = new Logwrite();
                    $info->append('admin(' . $_SESSION['user_id'] . '): report_arrive_edit[' . $post["userid"] . ' ' . $post["date"] . ' ' . $post["startreport"] . ']')
                        ->newline()
                        ->commit(LogWrite::APPEND);*/
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                } else {
                    $error_message[] = "修正に失敗しました。";
                }
            }
            //退勤テーブル更新
            if (!empty($request->leaveid)) {

                $endReportTable = EndReportTable::Where('id', $request->leaveid)->first();
                if (!empty($request->date)) {
                    $endReportTable->date = $request->date;
                }

                if (!empty($request->leavetime)) {
                    $endReportTable->leavetime = $request->leavetime;
                }
                $endReportTable->report = $request->endreport;
                $res = $endReportTable->save();

                if ($res) {
                    $success_message = "修正しました。";
                    //2023/10/31 ログ
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////
                    /*$info = new LogWrite();
                    $info->append('admin(' . $_SESSION['user_id'] . '): report_leave_edit[' . $post["userid"] . ' ' . $post["date"] . ' ' . $post["endreport"] . ']')
                        ->newline()
                        ->commit(LogWrite::APPEND);*/
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////
                } else {
                    $error_message[] = "修正に失敗しました。";
                }
            }
        }

        return view('admin.work_reports.report_edit_done', ['message_array' => $message_array, 'error_message' => $error_message, 'success_message' => $success_message]);
    }

    public function delete(DeleteReportsRequest $request)
    {

        //初期化
        $error_message = array();
        $dbh = null;
        $res = null;
        $post = null;
        $sql = null;
        $message_array = null;
        $searchitem = null;

        if (!empty($request->reset)) {
            $request = null;
        }

        if (!empty($request->schsubmit)) {

            $query = ReportView::query();
            //日
            if (!empty($request->schdate)) {
                $query = $query->where('date', $request->schdate);
            }
            //月
            if (!empty($request->schmonth)) {
                $query = $query->where('date', 'LIKE', $request->schmonth . "%");
            }

            // 利用者ID
            if (!empty($request->schuser_id)) {
                $query = $query->where('user_id', $request->schuser_id);
            }
            // シフト
            if (!empty($request->month_shift)) {
                $query = $query->where('shift_status', $request->month_shift);
            }

            //遅刻あり
            if (!empty($request->checkbox)) {
                $query = $query->whereNotNull('latetime');
            }

            //出勤報告か退勤報告どちらかがある
            $query = $query->where(function ($q) use ($request) {
                $q = $q->where('arrivalcheck', 1);
                $q = $q->orWhere('leavecheck', 1);
            });
            $query = $query->orderBy('date', 'desc')->orderBy('user_id', 'desc');

            $message_array = $query->paginate(50);

            $searchitem = [
                'schsubmit' => $request->schsubmit,
                'schdate' => $request->schdate,
                'schmonth' => $request->schmonth,
                'schuser_id' => $request->schuser_id,
                'month_shift' => $request->month_shift,
                'checkbox' => $request->checkbox,
            ];
        }



        return view('admin.work_reports.report_delete', ['message_array' => $message_array, 'searchitem' => $searchitem]);
    }

    public function delete_check(DeleteReportsRequest $request)
    {
        //初期化
        $message_array = array();
        $error_message = array();
        $date = array();
        $userid = array();
        $res = null;

        if (empty($request->report_check)) {
            $error_message[] = "チェックボックスを選択してください。";
            //全体削除 2023/11/13
            /*if (empty($post['message_array'])) {
            $error_message[] = "削除する項目がありません。";
            
            } else {
                $message_array = $post['message_array']; 
                var_dump($message_array);
            }*/
        } else {
            // ラジオボタンからチェックボックスへ変更 2023/11/14
            //$date = substr($post['radio'], 0, 10);
            //$userid = substr($post['radio'], 11);
            foreach ($request->report_check as $value) {
                $date[] = substr($value, 0, 10);
                $userid[] = substr($value, 11);
            }

            //$sql = 'SELECT * FROM report_view WHERE (date = ' . "'" . $date[0] . "'" . ' AND userid = ' . "'" . $userid[0] . "'" . ')';
            $query = ReportView::query()->where(function ($q) use ($date, $userid) {
                $q = $q->where('date', $date[0])->where('user_id', $userid[0]);
            });


            for ($i = 1; $i < count($date); $i++) {
                $query = $query->orWhere(function ($q) use ($date, $userid, $i) {
                    $q = $q->where('date', $date[$i])->where('user_id', $userid[$i]);
                });
            }

            $message_array = $query->get();


            /*try {
                $sql = 'SELECT * FROM report_view WHERE (date = ' . "'" . $date[0] . "'" . ' AND userid = ' . "'" . $userid[0] . "'" . ')';
                //$sql .= " AND date IN('" . implode("','", (array) $date) . "') AND userid IN('" . implode("','", (array) $userid) . "')";
                for ($i = 1; $i < count($date); $i++) {
                    $sql .= ' OR (date = ' . "'" . $date[$i] . "'" . ' AND userid = ' . "'"  . $userid[$i] . "'" . ')';
                }

                $stmt = $dbh->query($sql);
                $message_array = $stmt->fetchAll(PDO::FETCH_BOTH);
            } catch (Exception $e) {
                $error_message[] = "データの取得に失敗しました。";
            }*/

            //echo substr($post['radio'], 0, 10) . '<br>';
            //echo substr($post['radio'], 11) '<br>';
            //var_dump($sql);
            //$dbh = null;
        }

        return view('admin.work_reports.report_delete_check', ['message_array' => $message_array, 'error_message' => $error_message]);
    }

    public function delete_done(DeleteCheckReportsRequest $request)
    {
        //初期化
        $success_message = array();
        $error_message = array();
        $res = null;

        //出勤テーブル更新
        if (!empty($request->arriveid)) {
            $res = StartReportTable::query()->whereIn('id', (array)$request->arriveid)->delete();

            if ($res) {
                $success_message[] = "業務開始報告の削除に成功しました。";
                //2023/10/31 ログ
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /*for ($i = 0; $i < count($post['arriveid']); $i++) {
                    if (!empty($post['arriveid'][$i])) {
                        $info = new LogWrite();
                        $info->append('admin(' . $_SESSION['user_id'] . '): report_arrive_delete[' . $post["userid"][$i] . ' ' . $post["date"][$i] . ' ' . $post["startreport"][$i] . ']')
                            ->newline()
                            ->commit(LogWrite::APPEND);
                    }
                }*/
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } else {

                $error_message[] = "業務開始報告の削除に失敗しました。";
            }
            $text = "test";
        }
        //退勤テーブル更新
        if (!empty($request->leaveid)) {
            $res = EndReportTable::query()->whereIn('id', (array)$request->leaveid)->delete();

            if ($res) {
                $success_message[] = "業務終了報告の削除に成功しました。";
                //2023/10/31 ログ
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /*for ($i = 0; $i < count($post['leaveid']); $i++) {
                    if (!empty($post['leaveid'][$i])) {
                        $info = new LogWrite();

                        $info->append('admin(' . $_SESSION['user_id'] . '): report_leave_delete[' . $post["userid"][$i] . ' ' . $post["date"][$i] . ' ' . $post["endreport"][$i] . ']')
                            ->newline()
                            ->commit(LogWrite::APPEND);
                    }
                }*/
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } else {
                $error_message[] = "業務終了報告の削除に失敗しました。";
            }
        }

        return view('admin.work_reports.report_delete_done', ['success_message' => $success_message, 'error_message' => $error_message]);
    }

    public function download(DetailSearchFormRequest $request)
    {
        //初期化
        $message_array = array();
        $query = null;
        $searchitem = null;
        $month = date('Y-m');

        if (!empty($request->reset)) {
            $request = null;
        }

        if (!empty($request->schsubmit)) {

            $query = ReportView::query();

            //2026.05.28 日付検索が選択されてなかったら最新月を取得
            if (empty($request->schdate) && empty($request->schmonth)) {
                $query = $query->where('date', 'LIKE', $month . "%");
            }

            //---検索条件---
            //日
            if (!empty($request->schdate)) {
                $query = $query->where('date', $request->schdate);
            }
            //月
            if (!empty($request->schmonth)) {
                $query = $query->where('date', 'LIKE', $request->schmonth . "%");
            }
            // 利用者ID
            if (!empty($request->schuser_id)) {
                $query = $query->where('user_id', $request->schuser_id);
            }
            //シフト
            if (!empty($request->month_shift)) {
                $query = $query->where('shift_status', $request->month_shift);
            }

            //出勤報告か退勤報告にチェックが入っていれば適応する。
            if (!empty($request->arriveradio) || !empty($request->leaveradio)) {
                $query = $query->where(function ($q) use ($request) {
                    //出勤報告
                    if ($request->arriveradio == "ari") {
                        $q = $q->whereNotNull('arrivalcheck');
                    } else {
                        //「nashi」かチェックが無ければ出勤報告無しにする。
                        $q = $q->whereNull('arrivalcheck');
                    }

                    //AND OR選択
                    if ($request->andorradio == "and") {
                        //退勤報告
                        if ($request->leaveradio == "ari") {
                            $q = $q->whereNotNull('leavecheck');
                        } else {
                            //「nashi」かチェックが無ければ退勤報告無しにする。
                            $q = $q->whereNull('leavecheck');
                        }
                    } else if ($request->andorradio == "or") {
                        //退勤報告
                        if ($request->leaveradio == "ari") {
                            $q = $q->orWhereNotNull('leavecheck');
                        } else {
                            //「nashi」かチェックが無ければ退勤報告無しにする。
                            $q = $q->orWhereNull('leavecheck');
                        }
                    } else {
                        //退勤報告 ANDORが入っていなかったら「OR」にする。
                        if ($request->leaveradio == "ari") {
                            $q = $q->orWhereNotNull('leavecheck');
                        } else {
                            $q = $q->orWhereNull('leavecheck');
                        }
                    }
                });
            }

            // 遅刻あり
            if (!empty($request->checkbox)) {
                //$sql .= ' AND latetime IS NOT NULL';
                $query = $query->whereNotNull('latetime');
            }

            //$sql .= ' ORDER BY date DESC, userid';
            $query = $query->orderBy('date', 'desc')->orderBy('user_id', 'desc');


            $message_array = $query->paginate(50);

            $searchitem = [
                'schsubmit' => $request->schsubmit,
                'schdate' => $request->schdate,
                'schmonth' => $request->schmonth,
                'schuser_id' => $request->schuser_id,
                'month_shift' => $request->month_shift,
                'arriveradio' => $request->arriveradio,
                'andorradio' => $request->andorradio,
                'leaveradio' => $request->leaveradio,
                'checkbox' => $request->checkbox,
            ];
        }

        return view('admin.report_download.report_list_download', ['message_array' => $message_array, 'searchitem' => $searchitem]);
    }

    public function download_done(DetailSearchFormRequest $request)
    {
        //初期化
        $message_array = array();
        $query = null;
        $csv = '';
        $month = date('Y-m');

        //$sql = 'SELECT * FROM report_view WHERE 1';
        $query = ReportView::query();

        //2026.05.28 日付検索が選択されてなかったら最新月を取得
        if (empty($request->schdate) && empty($request->schmonth)) {
            $query = $query->where('date', 'LIKE', $month . "%");
        }

        //---検索要素がある場合SQL追加---
        //日
        if (!empty($request->schdate)) {
            //$sql .= ' AND date = ' . "'" . $post['schdate'] . "'";
            $query = $query->where('date', $request->schdate);
        }
        //月
        if (!empty($request->schmonth)) {
            //$sql .= ' AND date LIKE ' . "'" . $post['schmonth'] . "%'";
            $query = $query->where('date', 'LIKE', $request->schmonth . "%");
        }
        // 利用者ID
        if (!empty($request->schuser_id)) {
            //$sql .= ' AND userid = ' . "'" . $post['schuser_id'] . "'";
            $query = $query->where('user_id', $request->schuser_id);
        }
        //シフト
        if (!empty($request->month_shift)) {
            //$sql .= ' AND shift_status = ' . "'" . $request->month_shift . "'";
            $query = $query->where('shift_status', $request->month_shift);
        }

        //出勤報告か退勤報告にチェックが入っていれば適応する。
        if (!empty($request->arriveradio) || !empty($request->leaveradio)) {
            //$sql .= ' AND (';
            $query = $query->where(function ($q) use ($request) {
                //出勤報告
                if ($request->arriveradio == "ari") {
                    $q = $q->whereNotNull('arrivalcheck');
                } else {
                    //「nashi」かチェックが無ければ出勤報告無しにする。
                    $q = $q->whereNull('arrivalcheck');
                }

                //AND OR選択
                if ($request->andorradio == "and") {
                    //退勤報告
                    if ($request->leaveradio == "ari") {
                        $q = $q->whereNotNull('leavecheck');
                    } else {
                        //「nashi」かチェックが無ければ退勤報告無しにする。
                        $q = $q->whereNull('leavecheck');
                    }
                } else if ($request->andorradio == "or") {
                    //退勤報告
                    if ($request->leaveradio == "ari") {
                        $q = $q->orWhereNotNull('leavecheck');
                    } else {
                        //「nashi」かチェックが無ければ退勤報告無しにする。
                        $q = $q->orWhereNull('leavecheck');
                    }
                } else {
                    //退勤報告 ANDORが入っていなかったら「OR」にする。
                    if ($request->leaveradio == "ari") {
                        $q = $q->orWhereNotNull('leavecheck');
                    } else {
                        $q = $q->orWhereNull('leavecheck');
                    }
                }
            });
        }

        // 遅刻あり
        if (!empty($request->checkbox)) {
            //$sql .= ' AND latetime IS NOT NULL';
            $query = $query->whereNotNull('latetime');
        }

        //$sql .= ' ORDER BY date DESC, userid';
        $query = $query->orderBy('date', 'desc')->orderBy('user_id', 'desc');


        $message_array = $query->get();


        $csv = '日付,利用者ID,名前,シフト,出勤,遅刻,出勤時報告,退勤,退勤時報告';
        $csv .= "\n";
        if (!empty($message_array)) {
            foreach ($message_array as $value) {
                $csv .= $value['date'] . ',';
                $csv .= $value['user_id'] . ',';
                $csv .= $value['name'] . ',';
                $csv .= $value['shift_status'] . ',';
                $csv .= ($value['arrivalcheck'] ? 'OK' : '') . ',';
                $csv .= $value['latetime'] . ',';
                $csv .= str_replace("\r\n", " ", $value['startreport']) . ',';
                $csv .= ($value['leavecheck'] ? 'OK' : '') . ',';
                $csv .= str_replace("\r\n", " ", $value['endreport']) . ",\n";
            }
        }

        $filePath = 'files/work_report.csv';

        $csv = mb_convert_encoding($csv, 'SJIS', 'UTF-8');

        Storage::put($filePath, $csv);

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['error' => 'ファイルが存在しません'], 404);
        }

        Log::info('Work report CSV downloaded', [
            'operator_id'   => Auth::id(),
            'file_path'     => $filePath,
            'record_count'         => $message_array->count(),
            'details' => $request->except(['_token']),
        ]);

        return Storage::download($filePath);
    }
}
