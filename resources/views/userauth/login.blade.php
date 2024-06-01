<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
</head>
<body>

<h2>Đăng nhập</h2>

<form method="POST" action="{{ route('userlogin') }}">
    @csrf

    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
    </div>

    <div>
        <label for="password">Mật khẩu</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div>
        <button type="submit">Đăng nhập</button>
    </div>
</form>

@if ($errors->any())
    <div>
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

</body>
</html>
