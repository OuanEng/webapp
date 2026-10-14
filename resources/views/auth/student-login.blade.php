<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เข้าสู่ระบบ | KKU DORM</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student-login.css') }}">
</head>
<body class="login-page">
    <main class="login-card panel">
        <p class="brand">KKU DORM</p>
        <h1>เข้าสู่ระบบ</h1>
        <p class="hint">กรอกรหัสนักศึกษาและรหัสผ่านเพื่อเข้าใช้งาน</p>
        @if (session('success'))
            <p class="success" role="status">{{ session('success') }}</p>
        @endif
        @if ($errors->any())
            <p class="error" role="alert">{{ $errors->first() }}</p>
        @endif
        <form class="student-login-form" method="POST" action="{{ route('auth.student') }}">
            @csrf
            <div class="field"><label for="student_id">รหัสนักศึกษา</label>
            <input id="student_id" name="student_id" type="text" value="{{ old('student_id') }}" placeholder="เช่น 673380322-2" autocomplete="username" maxlength="255" required autofocus></div>
            <div class="field"><label for="password">รหัสผ่าน</label>
            <input id="password" name="password" type="password" autocomplete="current-password" maxlength="255" required></div>
            <button class="login-submit" type="submit">เข้าสู่ระบบ</button>
        </form>
        <p class="footer"><a href="{{ route('password.request') }}">ลืมรหัสผ่าน?</a></p>
        <p class="footer">ระบบจัดการหอพักนักศึกษา มหาวิทยาลัยขอนแก่น</p>
    </main>
</body>
</html>
