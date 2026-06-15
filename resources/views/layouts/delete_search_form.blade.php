<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠システム</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <style>
        body {
            font-size: 15px;
            line-height: 1.5;
            color: #333;
        }

        .menu {
            position: relative;
            width: 100%;
            background-color: #ddd;
        }

        .menu ul>li {
            display: inline-block;
            text-align: center;
            width: 170px;
        }

        .menu ul>li:hover {
            background-color: #9a9a9a;
            color: #ddd;
        }

        .menu ul li ul {
            position: absolute;
            list-style: none;
            margin: 0px;
            padding: 0px;
            width: 170px;
            display: none;
        }

        .menu ul li ul li {
            text-align: left;
        }

        .menu ul li ul li a {
            text-decoration: none;
            color: #ddd;
        }

        .menu ul li ul li:hover {
            background-color: #ddd;
        }

        .menu ul li:hover ul {
            display: block;
            background-color: #9a9a9a;
        }

        .forget {
            font-size: 10px;
        }

        .reportsearch {
            display: flex;
            flex-wrap: wrap;
        }

        .searchitemblock {
            background-color: #ddd;
        }

        .error_message{
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
        }
    </style>
    @yield('css')
</head>

<body>
    <header class="menu">
        <ul>
            <li><a>業務報告</a>
                <ul>
                    <li><a href="{{ route('admin.work_reports.list') }}">業務報告一覧</a></li>
                    <li><a href="{{ route('admin.work_reports.delete') }}">業務報告削除</a></li>
                    <li><a href="{{ route('admin.work_reports.download') }}">業務報告書ダウンロード</a></li>
                </ul>
            </li>

            <li><a>利用者</a>
                <ul>
                    <li><a href="{{ route('admin.users.list') }}">利用者一覧</a></li>
                    <li><a href="{{ route('admin.users.add') }}">利用者追加</a></li>
                    <li><a href="{{ route('admin.users.delete') }}">利用者削除</a></li>
                    <li><a href="{{ route('admin.users.logintime_set') }}">利用者ログイン時間設定</a></li>
                </ul>
            </li>
            <li><a>シフト</a>
                <ul>
                    <li><a href="{{ route('admin.shifts.edit') }}">シフト編集</a></li>
                    <li><a href="{{ route('admin.shifts.delete') }}">シフト一括削除</a></li>
                    <li><a href="{{ route('admin.shifts.import') }}">シフトインポート</a></li>
                </ul>
            </li>
            <li><a>管理者</a>
                <ul>
                    <li><a href="{{ route('admin.admins.list') }}">管理者一覧</a></li>
                    <li><a href="{{ route('admin.admins.add') }}">管理者追加</a></li>
                    <li><a href="{{ route('admin.admins.delete') }}">管理者削除</a></li>
                </ul>
            </li>
            <li><a>...</a>
                <ul>
                    <li><a href="{{ route('admin.password.edit') }}">パスワード変更</a></li>
                    <li><a href="{{ route('admin.logout') }}">ログアウト</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <h1 class="title">@yield('title')</h1>
    <hr>
    <p>USER:{{$login_admin->name}}</p>
    <div class="container">
        <form method="GET">
            @csrf
            【日付】
            <input id="schdate" type="date" name="schdate" maxlength="10" value="<?php if (!empty($searchitem['schdate'])) echo $searchitem['schdate']; ?>">
            @error('schdate')
                <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                    {{ $message }}
                </div>
            @enderror
            【月】
            <input id="schmonth" type="month" name="schmonth" maxlength="10" value="<?php if (!empty($searchitem['schmonth'])) echo $searchitem['schmonth']; ?>">
            @error('schmonth')
                <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                    {{ $message }}
                </div>
            @enderror
            【利用者ID】
            <input type="text" name="schuser_id" maxlength="10" value="<?php if (!empty($searchitem['schuser_id'])) echo $searchitem['schuser_id']; ?>">
            @error('schuser_id')
                <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                    {{ $message }}
                </div>
            @enderror
            【シフト】
            {!! pulldown_monthshift($searchitem['month_shift'] ?? null) !!}
            @error('month_shift')
                <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                    {{ $message }}
                </div>
            @enderror
            【遅刻あり】
            <input type="checkbox" name="checkbox" <?php if (!empty($searchitem['checkbox'])) echo $searchitem['checkbox'] ? 'checked' : ''; ?>><br>
            @error('checkbox')
                <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                    {{ $message }}
                </div>
            @enderror
            <input type="submit" name="schsubmit" value="検索">
            <input type="submit" name="reset" value="リセット">
        </form>
        @yield('content')
    </div>
</body>

</html>