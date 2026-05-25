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

        header {
            position: relative;
            width: 100%;
            background-color: #ddd;
        }

        ul>li {
            display: inline-block;
        }

        ul>li>a {
            padding: 15px 30px;
            display: block;
            font-size: 0.8em;
        }

        ul>li:hover>a {
            background-color: #efefef;
            color: #444;
        }

        ul li ul {
            position: absolute;
        }

        ul li ul li {
            display: block;
        }

        ul li ul li a {
            background-color: #efefef;
            color: #444;
        }

        ul li ul li a:hover {
            background-color: #ddd;
        }

        .menu ul li ul li {
            height: 0;
            overflow: hidden;
        }

        .menu ul li:hover ul li {
            height: auto;
            overflow: visible;
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
            <li><a href="{{ route('report_start_add') }}">出勤時業務報告</a></li>
            <li><a href="{{ route('report_end_add') }}">退勤時業務報告</a></li>
            <li><a href="{{ route('logout') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    ログアウト
                </a>
            </li>
        </ul>
    </header>
    <h1 class="title">@yield('title')</h1>
    <hr>
    <p>USER:{{$user->name}}</p>
    @yield('content')

    <!-- 実際に送信される隠しフォーム -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>

</html>