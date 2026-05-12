<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠システム</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <header class="menu">
        <ul>
            <li><a>トップ</a></li>
        </ul>
    </header>
    <h1 class="title">利用者ログイン画面</h1>
    <hr>
    <!-- エラーメッセージ -->
    @if ($errors->any())
    <div class="login_error">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form method="POST" action={{ route('login') }}>
        @csrf
        Email:<br>
        <input id="email" type="text" name="email" maxlength="20"><br>
        パスワード: <br>
        <input id="password" type="password" name="password" maxlength="32"><br>
        <br>
        <input id="button" type="submit" name="submitbtn" value="ログイン">
    </form>
</body>

</html>