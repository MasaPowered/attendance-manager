<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserTableController;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function login(Request $request)
    {
        return view('admin.login');
    }

    public function post_login(Request $request)
    {
        
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('admin.report_list');
        } else {
            $msg = 'ログインに失敗しました。';
            return view('admin.login', ['error_message' => $msg]);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        //2023/10/30 ログ
        /////////////////////////////////////////////////////////////////////////////
        /*$info = new LogWrite();
        $info->append('admin(' . $_SESSION['user_id'] . '): admin_logout')
        ->newline()
        ->commit(LogWrite::APPEND);*/
        /////////////////////////////////////////////////////////////////////////////

        return redirect()->route('admin.login');
    }

    public function list(Request $request)
    {
        $message_array = Admin::all();

        return view('admin.admins.admin_list', ['message_array' => $message_array]);
    }

    public function edit(Request $request)
    {
        //初期化
        $message_array = array();
        $error_message = array();

        if (empty($request->radio))
            $error_message[] = "ラジオボタンを選択してください。 ";

        if (empty($error_message)) {
            $message_array = Admin::Where('id', $request->radio)->first();
        }

        return view('admin.admins.admin_edit', ['message_array' => $message_array, 'error_message' => $error_message]);
    }

    public function edit_done(Request $request)
    {
        //初期化
        $message_array = array();
        $success_message = null;
        $error_message = array();
        $res = null;

        if (empty($request->id)) {
            $error_message[] = "管理者IDが入力されていません。";
        }

        if (empty($request->name)) {
            $error_message[] = "氏名が入力されていません。";
        }

        if (empty($request->pass)) {
            $error_message[] = "パスワードが入力されていません。";
        }

        if (!empty($request->pass) && !empty($request->pass2)) {
            if (strcmp($request->pass, $request->pass2) !== 0) {
                $error_message[] = "パスワードが一致しません。";
            }
        }

        //暗号化
        //$pass = md5($request->pass);
        //2026/01/21
        $pass = Hash::make($request->pass);
        
        if (empty($error_message)) {
            $admin = Admin::Where('id', $request->id)->first();
            $admin->name = $request->name;
            $admin->password = $pass;
            $res = $admin->save();

            if ($res) {
                $success_message = "修正しました。 ";
                //2023/10/30 ログ
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /*$info = new Logwrite();
                $info->append('admin(' . $_SESSION['user_id'] . '): admin_edit[' . $post["user_id"] . ' ' . $post["name"] . ']')
                ->newline()
                ->commit(LogWrite::APPEND);*/
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } else {
                $error_message[] = "修正に失敗しました。";
            }

            $user = Auth::user();

            //ログインアカウントが編集された場合
            if (!empty($success_message) && $request->id == $user->id) {
                /*try {
                    $sql = $dbh->prepare("SELECT user_id, name FROM admin_table WHERE user_id=?");
                    $data[] = $post['user_id'];
                    //SQLクエリの実行
                    $res = $sql->execute($data);
                    $rec = $sql->fetch(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                }
                if ($rec == false) {
                    $error_message[] = "読み込みに失敗しました。";
                } else {
                    $_SESSION['user_id'] = $rec['user_id'];
                    $_SESSION['admin_user_name'] = $rec['name'];
                }
                $rec = null;*/
            }
        }

        return view('admin.admins.admin_edit_done', ['id' => $request->id, 'name' => $request->name, 'success_message' => $success_message, 'error_message' => $error_message]);
    }

    public function add(Request $request)
    {
        return view('admin.admins.admin_add');
    }

    public function add_check(Request $request)
    {
        //初期化
        $error_message = array();
        $res = null;

        if (!empty($request->submitbtn)) {

            if (empty($request->name)) {
                $error_message[] = "氏名が入力されていません。";
            }

            if (empty($request->email)) {
                $error_message[] = "メールアドレスが入力されていません。";
            }

            if (empty($request->pass)) {
                $error_message[] = "パスワードが入力されていません。";
            }

            if (!empty($request->pass) && !empty($request->pass2)) {
                if (strcmp($request->pass, $request->pass2) !== 0) {
                    $error_message[] = "パスワードが一致しません。";
                }
            }
        }

        //暗号化
        $data = [
            "name" => $request->name,
            "email" => $request->email,
            //"pass" => md5($request->pass),
            //2026/01/21
            "pass" => Hash::make($request->pass),
        ];

        return view('admin.admins.admin_add_check', ['data' => $data, 'error_message' => $error_message]);
    }

    public function create(Request $request)
    {
        //初期化
        $success_message = null;
        $error_message = array();
        $res = null;

        if (!empty($request->submitbtn)) {
            if (empty($request->name) || empty($request->email) || empty($request->pass)) {
                $error_message[] = "データ登録に失敗しました。";
            }
            if (empty($error_message)) {
                $res = Admin::create([
                    "name" => $request->name,
                    "email" => $request->email,
                    //2026.04.29 pass→password
                    "password" => $request->pass,
                ]);

                if ($res == true) {
                    $success_message = "追加しました。";
                    //2023/10/30 ログ
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////
                    /*$info = new LogWrite();
                    $info->append('admin(', $_SESSION['user_id'] . '): admin_add[' . $post["user_id"] . ' ' . $post["name"] . ']')
                    ->newline()
                        ->commit(LogWrite::APPEND);*/
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////
                } else {
                    $error_message[] = "追加に失敗しました。";
                }
            }
        }

        $data = [
            "name" => $request->name,
            "email" => $request->email,
        ];

        return view('admin.admins.admin_add_done', ['data' => $data, 'request' => $request, 'success_message' => $success_message, 'error_message' => $error_message]);
    }

    public function delete(Request $request)
    {
        $message_array = Admin::all();

        return view('admin.admins.admin_delete', ['message_array' => $message_array]);
    }

    public function delete_check(Request $request)
    {
        //初期化
        $message_array = array();
        $error_message = array();

        if (empty($request->radio)) {
            $error_message[] = "ラジオボタンを選択してください。";
        } else {
            $message_array = Admin::where('id', $request->radio)->first();
        }

        return view('admin.admins.admin_delete_check', ['message_array' => $message_array, 'error_message' => $error_message]);
    }

    public function delete_done(Request $request)
    {
        //初期化
        $success_array = null;
        $error_message = array();
        $res = null;

        if (!empty($request->id)) {
            $res = admin::where('id', $request->id)->delete();

            if ($res) {
                $success_message = "削除しました。";
                //2023/10/30 ログ
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /*$info = new Logwrite();

                $info->append('admin(' . $_SESSION['user_id'] . '): admin_delete[' . $post["user_id"] . ' ' . $post["name"] . ']')
                    ->newline()
                    ->commit(LogWrite::APPEND);*/
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } else {
                $error_message[] = "削除に失敗しました。";
            }
        }

        return view('admin.admins.admin_delete_done', ['success_message' => $success_message, 'error_message' => $error_message]);
    }
}
