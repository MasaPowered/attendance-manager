<?php

/*function sanitize($befor)
{
    $after = null;
    foreach ($befor as $key => $value) {
        if (is_array($value)) {
            $after[$key] = $value;
        } else {
            $after[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    return $after;
}


function pulldown_month()
{
    echo '<select name=month>';
    echo '<option value="01">01</option>';
    echo '<option value="02">02</option>';
    echo '<option value="03">03</option>';
    echo '<option value="04">04</option>';
    echo '<option value="05">05</option>';
    echo '<option value="06">06</option>';
    echo '<option value="07">07</option>';
    echo '<option value="08">08</option>';
    echo '<option value="09">09</option>';
    echo '<option value="10">10</option>';
    echo '<option value="11">11</option>';
    echo '<option value="12">12</option>';
    echo '</select>';
}

function pulldown_shift($day, $shift = null)
{
    return '<select name="shift' . $day . '">
    <option value=""></option>
    <option value="出勤" ' . ((!strcmp($shift, "出勤")) ? 'selected' : '') . '>出勤</option>
    <option value="休" ' . ((!strcmp($shift, "休")) ? 'selected' : '') . '>休</option>
    <option value="確休" ' . ((!strcmp($shift, "確休")) ? 'selected' : '') . '>確休</option>
    <option value="在宅" ' . ((!strcmp($shift, "在宅")) ? 'selected' : '') . '>在宅</option>
    </select>';
}

function pulldown_monthshift($shift = null)
{
    echo '<select name="month_shift">';
    echo '<option value=""></option>';
    echo '<option value="出勤" ' . ((!strcmp($shift, "出勤")) ? 'selected' : '') . '>出勤</option>';
    echo '<option value="休" ' . ((!strcmp($shift, "休")) ? 'selected' : '') . '>休</option>';
    echo '<option value="確休" ' . ((!strcmp($shift, "確休")) ? 'selected' : '') . '>確休</option>';
    echo '<option value="在宅" ' . ((!strcmp($shift, "在宅")) ? 'selected' : '') . ' >在宅</option>';
    echo '</select>';
}

function session_check_admin()
{
    $login_message = null;
    session_start();
    session_regenerate_id(true);

    if (isset($_SESSION['adomin_login']) == false) {
        //$login_message = 'ログインされていません。';
        //$login_message .= '<a href="../admin/admin_login.php">ログイン画面へ</a>';
        //exit();
        header('Location:../admin/admin_login.php');
    } else {
        $login_message = 'ログイン中:';
        $login_message .= $_SESSION['admin_user_name'];
        $login_message .= '<br />';
    }
    return $login_message;
}

function session_check_user()
{
    $login_message = null;
    session_start();
    session_regenerate_id(true);
    if (isset($_SESSION['login']) == false) {
        //$login_message='nth. ';
        //$login_message.= '<a href="../user_work_report/user_login.php">^</a>';
        //exit();
        header('Location:../user_work_report/user_login.php');
    } else {
        $login_message = 'ログイン中: ';
        $login_message .= $_SESSION['user_name'];
        $login_message .= '<br />';
    }
    return $login_message;
}

function download_file($path_file)
{
    //ファイルの存在確認
    if (!file_exists($path_file)) {
        die("Error: File(" . $path_file . ") does not exist");
    }

    //オープンできるか確認
    if (!($fp = fopen($path_file, "r"))) {
        die("Error: Cannot open the file(" . $path_file . ")");
    }
    fclose($fp);

    if (($content_length = filesize($path_file)) == 0) {
        die("Error: File size is 0.(" . $path_file . ")");
    }
    //ダウンロード用のHTTPヘッダー送信
    header("Cache-Control: private");
    header("Pragma: private");
    header('Content-Description: File Transfer');
    header("Content-Disposition: inline; filename=\"" . basename($path_file) . "\"");
    header("Content-Length: " . $content_length);
    header("Content-Type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');

    // ファイルを読んで出力
    if (!readfile($path_file)) {
        die("Cannot read the file(" . $path_file . ")");
    }
}

// logクラス作成 2023/10/30
class FileWrite
{
    //プロパティ
    private $filename = '../log/info.log';
    private $content = '';
    public const APPEND = FILE_APPEND;

    // コンストラクタ関数
    function __construct($filename = null)
    {
        if ($filename != null) {
            $this->filename = $filename;
        }
    }

    // 内容を追加するメソッド
    function append($content)
    {
        // 渡される contentをこのクラスのcontent に格納する
        // 渡ってきた内容を結合していくので.=
        // $this format() を content に加える
        $this->content .= $this->format($content);

        // チェーンメソッドが活用できるようにreturn で$thisを返す
        return $this;
    }

    // format() では継承したグラスで拡張するので、単に内容を返すだけにする
    // 外部から呼ばれないようにするためい自クラスか継承したクラスのみで呼び出せる protectedをつける
    protected function format($content)
    {
        return $content;
    }
    // 改行するメソッド
    function newline()
    {
        $this->content .= PHP_EOL;
        return $this;
        // return $this->append (PHP_EOL);
    }
    // ファイルを書き込む
    //第三引数を渡す場合とそうでない場合があるのでデフォルト値をnull
    function commit($flag = null)
    {
        // ファイルを書き込む
        file_put_contents($this->filename, $this->content, $flag);
        // 内容を空にする
        $this->content = '';
        return $this;
    }
}

class Logwrite extends Filewrite
{

    protected function format($content)
    {
        $time_str = date('Y/m/d H:i:s');
        // 日付と時間を先頭につけ、 return する
        return sprintf('%s %s', $time_str, $content);
    }
}*/
