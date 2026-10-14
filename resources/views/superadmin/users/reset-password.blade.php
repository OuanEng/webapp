@extends('layouts.app')
@section('title', 'ตั้งรหัสผ่านให้นักศึกษา | KKU DORM')
@section('content')
    <h1>ตั้งรหัสผ่านให้นักศึกษา</h1>
    <p>{{ $member->name }} / รหัสนักศึกษา {{ $member->student_id }}</p>
    <p>ตั้งรหัสผ่านใหม่แล้วแจ้งให้นักศึกษาทราบ โดยไม่ต้องใช้รหัสผ่านเดิม</p>
    <form method="POST" action="{{ route('superadmin.users.password.update', $member) }}">
        @csrf
        @method('PUT')
        <label class="field">รหัสผ่านใหม่<input type="password" name="new_password" minlength="8" maxlength="255" autocomplete="new-password" required></label>
        <label class="field">ยืนยันรหัสผ่านใหม่<input type="password" name="new_password_confirmation" minlength="8" maxlength="255" autocomplete="new-password" required></label>
        <div class="actions"><button type="submit">ตั้งรหัสผ่านใหม่</button><a class="member-action-button" href="{{ route('admin.users.show', $member) }}">กลับข้อมูลสมาชิก</a></div>
    </form>
@endsection
