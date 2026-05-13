<?php

namespace App\Http\Controllers;

use App\Models\LoginTime;
use App\Models\User;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB; //2024/05/15 sasaki
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    public function user_list(Request $request)
    {
        $message_array = User::all();

        return view('user.user_list', ['message_array' => $message_array, 'data' => $request->array]);
    }

    public function user_edit(Request $request)
    {
        //初期化
        $message_array = array();
        $error_message = array();

        if (empty($request->radio))
            $error_message[] = "ラジオボタンを選択してください。 ";

        if (empty($error_message)) {
            $message_array = User::Where('id', $request->radio)->get();
        }

        return view('user.user_edit', ['error_message' => $error_message, 'message_array' => $message_array]);
    }

    public function user_edit_done(Request $request)
    {
        //初期化
        $message_array = array();
        $success_message = null;
        $error_message = array();
        $res = null;

        //2026.04.29　後にuserid別に作ったら使う
       /* if (empty($request->id)) {
            $error_message[] = "利用者IDが入力されていません。";
        }*/

        if (empty($request->name)) {
            $error_message[] = "氏名が入力されていません。";
        }

        if (empty($request->pass)) {
            $error_message[] = "パスワードが入力されていません。";
        }

        if (strcmp($request->pass, $request->pass2) !== 0) {
            $error_message[] = "パスワードが一致しません。";
        }
        //暗号化
        //$pass = md5($request->pass);
        //2026/01/21
        //$pass = Hash::make($request->pass);

        if (!empty($request->submitbtn)) {

            $userTable = User::Where('id', $request->id)->first();
            $userTable->name = $request->name;
            $userTable->password = $request->pass;
            $res = $userTable->save();

            if ($res) {
                $success_message = "修正しました。";
                //2023/10/31 ログ
                ////////////////////////////////////////////////////////////////////////////////////////////////
                /*$info = new LogWrite();

                $info->append('admin(' . $_SESSION['user_id'] . '): user_edit[' . $post["user_id"] . '' . $post["name"] . ']')
                    ->newline()
                    ->commit(LogWrite::APPEND);*/
                ////////////////////////////////////////////////////////////////////////////////////////////////
            } else {
                $error_message[] = "修正に失敗しました。";
            }
        }

        return view('user.user_edit_done', ['id' => $request->id, 'name' => $request->name, 'success_message' => $success_message, 'error_message' => $error_message]);
    }

    public function add(Request $request)
    {
        return view('user.user_add');
    }

    public function add_check(Request $request)
    {
        //初期化
        $message_array = array();
        $error_message = array();
        $res = null;
        $data = null;

        if (!empty($request->submitbtn)) {
            //2026.04.29　後にuserid別に作ったら使う
            /*if (empty($request->id)) {
                $error_message[] = "利用者IDが入力されていません。";
            } else {
                if (!preg_match('/^[0-9]+$/', $request->id)) {
                    $error_message[] = "利用者IDは半角数字のみ入力してください。 ";
                } else {
                    //利用者登録チェック
                    $res = User::Where('id', $request->id)->get();

                    if ($res->count() > 0) {
                        $error_message[] = "このIDはすでに存在します。";
                    }
                    $rec = null;
                }
            }*/

            if (empty($request->name)) {
                $error_message[] = "氏名が入力されていません。";
            }
            if (empty($request->email)) {
                $error_message[] = "メールアドレスが入力されていません。";
            }
            if (empty($request->pass)) {
                $error_message[] = "パスワードが入力されていません。";
            }
            if (strcmp($request->pass, $request->pass2) !== 0) {
                $error_message[] = "パスワードが一致しません。";
            }
        }

        //暗号化
        $data = [
            //"id" => $request->id,
            "name" => $request->name,
            "email" => $request->email,
            //"pass" => md5($request->pass),
            //2026/01/21
            "pass" => Hash::make($request->pass),
        ];

        //dd($data);

        return view('user.user_add_check', ['data' => $data, 'error_message' => $error_message]);
    }

    public function create(Request $request)
    {
        //初期化
        $message_array = array();
        $error_message = array();
        $res = null;

        if (!empty($request->submitbtn)) {
            if (/*empty($request->id) || */empty($request->name) || empty($request->email)  || empty($request->pass)) {
                $error_message[] = "データ登録に失敗しました。 ";
            }

            if (empty($error_message)) {
                $res = User::create([
                    //2026.04.29 別の形でuserid追加する予定
                    //"id" => $request->id,
                    "name" => $request->name,
                    "email" => $request->email,
                    "password" => $request->pass,
                ]);

                if ($res) {
                    $success_message = "追加しました。";
                    //2023/10/31 ログ
                    /////////////////////////////////////
                    /*$info = new LogWrite();
                    $info->append('admin(' . $_SESSION['user_id'] . '): user_add[' . $post["user_id"] . ' ' . $post["name"] . ']')
                    ->newline()
                        ->commit(LogWrite::APPEND);*/
                    /////////////////////////////////////
                } else {
                    $error_message[] = "追加に失敗しました。";
                }
            }
        }

        return view('user.user_add_done', ['request' => $request, 'success_message' => $success_message, 'error_message' => $error_message]);
    }

    public function delete(Request $request)
    {
        $message_array = User::all();

        return view('user.user_delete', ['message_array' => $message_array]);
    }

    public function delete_check(Request $request)
    {
        //初期化
        $message_array = array();
        $error_message = array();

        if (empty($request->radio)) {
            $error_message[] = "ラジオボタンを選択してください。 ";
        } else {
            $message_array = User::where('id', $request->radio)->first();
        }

        return view('user.user_delete_check', ['message_array' => $message_array, 'error_message' => $error_message]);
    }

    public function delete_done(Request $request)
    {
        //初期化
        $success_array = null;
        $error_message = array();
        $res = null;

        if (!empty($request->id)) {
            $res = User::where('id', $request->id)->delete();

            if ($res) {
                $success_message = "削除しました。";
                //2023/10/31 ログ
                ///////////////////////////////////////////////////////////////////////////////////////////
                /*$info = new Logwrite();

                $info->append('admin (' . $_SESSION['user_id'] . '): user_delete[' . $post["user_id"] . ']')
                ->newline()
                    ->commit(LogWrite::APPEND);*/
            } else {
                $error_message[] = "削除に失敗しました。";
            }
        }

        return view('user.user_delete_done', ['success_message' => $success_message, 'error_message' => $error_message]);
    }

    public function logintime_set()
    {
        // ログイン時間取得
        $message_array = LoginTime::Where('id', 1)->first();

        return view('user.user_logintime_set', ['message_array' => $message_array]);
    }

    public function post_logintime_set(Request $request)
    {
        //-----------------------------------------------------------------------------------------------------------------
        //初期化
        $message_array = array();
        $success_message = null;
        $error_message = array();
        $res = null;


        if (!empty($request->submit)) {
            // ログイン時間更新
            // 値をセット
            if (!empty($request->logintime_status)) {
                $logintime_status = 1;
            } else {
                $logintime_status = 0;
            }

            $loginTime = LoginTime::Where('id', 1)->first();
            $loginTime->logintime_status = $logintime_status;
            $loginTime->start_time = $request->start_time;
            $loginTime->end_time = $request->end_time;
            $res = $loginTime->save();

            if ($res) {
                $success_message = "保存しました。 ";
                //2023/10/31 ログ
                //////////////////////////////////////////////////////////////////////////////////////////////////
                /*$info = new Logwrite();

                if ($logintime_status == 1) {
                    $info->append('admin(' . $_SESSION['user_id'] . '): user_logintime_set[ON START TIME:' . $post["start_time"] . ' END TIME:' . $post["end_time"] . ']')
                        ->newline()
                        ->commit(LogWrite::APPEND);
                } else {
                    $info->append('admin(' . $_SESSION['user_id'] . '): user_logintime_set[OFF]')
                    ->newline()
                        ->commit(LogWrite::APPEND);
                }*/
                //////////////////////////////////////////////////////////////////////////////////////////////////
            } else {
                $error_message[] = "保存に失敗しました。";
            }
            $res = null;
        }

        // ログイン時間取得
        $message_array = LoginTime::Where('id', 1)->first();
        //-----------------------------------------------------------------------------------------------------------------

        return view('user.user_logintime_set', ['message_array' => $message_array, 'success_message' => $success_message, 'error_message' => $error_message]);
    }
}
