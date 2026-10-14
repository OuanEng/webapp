@extends('layouts.app')
@section('title', 'เปลี่ยนรหัสผ่าน | KKU DORM')
@section('content')
    <h1>เปลี่ยนรหัสผ่าน</h1>
    <p>กรอกรหัสผ่านเดิมสองครั้ง แล้วตั้งรหัสผ่านใหม่อย่างน้อย 8 ตัวอักษร</p>
    <form method="POST" action="{{ route('member.password.update') }}">
        @csrf
        @method('PUT')
        <label class="field">รหัสผ่านเดิม<input type="password" name="current_password" autocomplete="current-password" maxlength="255" required></label>
        <label class="field">ยืนยันรหัสผ่านเดิมอีกครั้ง<input type="password" name="current_password_confirmation" autocomplete="current-password" maxlength="255" required></label>
        <label class="field">รหัสผ่านใหม่<input type="password" name="new_password" autocomplete="new-password" minlength="8" maxlength="255" required></label>
        <div class="actions"><button type="submit">เปลี่ยนรหัสผ่าน</button><a class="member-action-button" href="{{ route('member.profile') }}">กลับข้อมูลของฉัน</a></div>
    </form>
@endsection
