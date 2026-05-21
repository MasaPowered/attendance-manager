<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠システム</title>
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
                    <li><a href="admin_pass_edit">パスワード変更</a></li>
                    <li><a href="{{ route('admin.logout') }}">ログアウト</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <h1 class="title">@yield('title')</h1>
    <hr>
    <p>USER:{{$admin->name}}</p>

    <form method="POST">
        @csrf
        【日付】
        <input id="schdate" type="date" name="schdate" maxlength="10" value="<?php if (!empty($searchitem['schdate'])) echo $searchitem['schdate']; ?>">
        【月】
        <input id="schmonth" type="month" name="schmonth" maxlength="10" value="<?php if (!empty($searchitem['schmonth'])) echo $searchitem['schmonth']; ?>">
        【利用者ID】
        <input type="text" name="schuser_id" maxlength="10" value="<?php if (!empty($searchitem['schuser_id'])) echo $searchitem['schuser_id']; ?>">
        【シフト】
        <?php empty($searchitem['month_shift']) ? pulldown_monthshift() : pulldown_monthshift($searchitem['month_shift']); ?>
        【遅刻あり】
        <input type="checkbox" name="checkbox" <?php if (!empty($searchitem['checkbox'])) echo $searchitem['checkbox'] ? 'checked' : ''; ?>><br>
        <input type="submit" name="schsubmit" value="検索">
        <input type="submit" name="reset" value="リセット">
    </form>
    @yield('content')
</body>

</html>