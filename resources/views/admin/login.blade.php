<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>業務報告管理システム</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
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
</style>

<body>
    <header class="menu">
        <nav class="navbar navbar-expand-md">
            <div class="container">
                <a class="navbar-brand">勤怠報告管理システム</a>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">利用者画面へ</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                        <div class="card-header">管理者ログイン</div>

                        <div class="card-body">
                            @if (session('error_message'))
                                <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                                    ※{{ session('error_message') }}
                                </div>
                            @endif
                            <form method="POST" action="{{ url()->current() }}">
                                @csrf
                                <div class="row mb-3">
                                    <label for="email" class="col-md-4 col-form-label text-md-end">メールアドレス</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password" class="col-md-4 col-form-label text-md-end">パスワード</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" name="submitbtn" class="btn btn-primary">
                                            ログイン
                                        </button>

                                    </div>
                                </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>