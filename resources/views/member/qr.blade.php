@extends('layouts.app')
@section('title', 'QR ของฉัน | KKU DORM')
@section('content')
    <header class="page-heading">
        <h1>QR ของฉัน</h1>
        <p>แสดง QR ให้เจ้าหน้าที่สแกนเพื่อเช็กชื่อเข้าร่วมกิจกรรม</p>
    </header>
    <section class="member-qr-card panel" aria-label="QR ประจำตัวนักศึกษา">
        <div class="member-qr-info">
            <span class="member-qr-badge">บัตรเช็กชื่อกิจกรรม</span>
            <h2>{{ auth()->user()->name }}</h2>
            <p>รหัสนักศึกษา <strong>{{ auth()->user()->student_id ?? 'ยังไม่มีรหัสนักศึกษา' }}</strong></p>
        </div>
        <div class="member-qr-frame">
            <img src="{{ route('member.qr.image') }}" alt="QR ประจำตัวสำหรับเช็กชื่อกิจกรรม" width="300" height="300">
        </div>
        <p class="member-qr-note">QR นี้เป็นของคุณเท่านั้น กรุณาไม่ส่งต่อให้ผู้อื่น</p>
        <a class="member-qr-link" href="{{ route('member.qr.image') }}" target="_blank" rel="noopener">เปิด QR ขนาดเต็ม</a>
    </section>
@endsection
