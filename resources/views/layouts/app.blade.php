<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KKU DORM')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/navigation.js') }}" defer></script>
</head>
<body class="app-page">
    <div class="app-shell"><aside class="app-sidebar" aria-label="เมนูระบบ">
        <a class="brand" href="{{ route('dashboard') }}">KKU DORM</a><p class="sidebar-caption">ระบบจัดการหอพักนักศึกษา</p>
        <button id="menu-toggle" class="menu-toggle" type="button" aria-controls="main-navigation" aria-expanded="true" hidden>เมนูหลัก</button>
        <nav id="main-navigation" class="main-navigation" aria-label="เมนูหลัก">
            <p class="nav-group-label">การใช้งานของฉัน</p><a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>หน้าหลัก</a>
            <a href="{{ route('member.profile') }}" @if(request()->routeIs('member.profile')) aria-current="page" @endif>ข้อมูลของฉัน</a>
            <a href="{{ route('member.password.edit') }}" @if(request()->routeIs('member.password.*')) aria-current="page" @endif>เปลี่ยนรหัสผ่าน</a>
            <a href="{{ route('member.activities') }}" @if(request()->routeIs('member.activities')) aria-current="page" @endif>กิจกรรม</a>
            <a href="{{ route('member.qr') }}" @if(request()->routeIs('member.qr')) aria-current="page" @endif>QR ของฉัน</a>
            <a href="{{ route('member.scores') }}" @if(request()->routeIs('member.scores')) aria-current="page" @endif>คะแนนของฉัน</a>
            <a href="{{ route('member.complaints.index') }}" @if(request()->routeIs('member.complaints.*')) aria-current="page" @endif>เรื่องร้องเรียนของฉัน</a>
            <a href="{{ route('member.repairs.index') }}" @if(request()->routeIs('member.repairs.*')) aria-current="page" @endif>งานแจ้งซ่อมของฉัน</a>
            <a href="{{ route('member.finance') }}" @if(request()->routeIs('member.finance')) aria-current="page" @endif>การเงินที่เปิดเผย</a>
            @can('viewAny', App\Models\User::class)
<p class="nav-group-label">จัดการหอพัก</p>
                <a href="{{ route('admin.users.index') }}" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>สมาชิก</a>
                <a href="{{ route('admin.dorms.index') }}" @if(request()->routeIs('admin.dorms.*')) aria-current="page" @endif>หอพัก</a>
                <a href="{{ route('admin.floors.index') }}" @if(request()->routeIs('admin.floors.*')) aria-current="page" @endif>ชั้น</a>
                <a href="{{ route('admin.rooms.index') }}" @if(request()->routeIs('admin.rooms.*')) aria-current="page" @endif>ห้องพัก</a>
                <a href="{{ route('admin.activities.index') }}" @if(request()->routeIs('admin.activities.*')) aria-current="page" @endif>จัดการกิจกรรม</a>
                <a href="{{ route('admin.complaints.index') }}" @if(request()->routeIs('admin.complaints.*')) aria-current="page" @endif>จัดการเรื่องร้องเรียน</a>
                <a href="{{ route('admin.repairs.index') }}" @if(request()->routeIs('admin.repairs.*')) aria-current="page" @endif>จัดการงานซ่อม</a>
                <a href="{{ route('admin.finance.index') }}" @if(request()->routeIs('admin.finance.*')) aria-current="page" @endif>จัดการการเงิน</a>
            @endcan
            @if (auth()->user()->role === 'superadmin')
                <p class="nav-group-label">ผู้ดูแลระบบสูงสุด</p><a href="{{ route('superadmin.admins.index') }}" @if(request()->routeIs('superadmin.admins.*')) aria-current="page" @endif>จัดการสิทธิ์ Admin</a>
                <a href="{{ route('superadmin.audit.index') }}" @if(request()->routeIs('superadmin.audit.*')) aria-current="page" @endif>Audit Log</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout" type="submit">ออกจากระบบ</button>
            </form>
        </nav></aside><div class="app-workspace"><header class="app-topbar"><div><span class="topbar-caption">KKU DORM / ระบบหอพัก</span><p>ยินดีต้อนรับ {{ auth()->user()->name }}</p></div><span class="role-badge">{{ ['user' => 'นักศึกษา', 'admin' => 'Admin', 'superadmin' => 'Superadmin'][auth()->user()->role] ?? auth()->user()->role }}</span></header><main class="panel content" id="main-content">
        @if (session('success')) <p class="success" role="status">{{ session('success') }}</p> @endif
        @if (session('error')) <p class="error" role="alert">{{ session('error') }}</p> @endif
        @if ($errors->any())
            <div class="error" role="alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @yield('content')
    </main></div></div>
</body>
</html>






