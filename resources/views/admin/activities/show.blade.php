@extends('layouts.app')
@section('content')
    <h1>{{ $activity->title }}</h1>
    <section class="activity-block"><h2>รายละเอียดกิจกรรม</h2><p style="white-space: pre-wrap">{{ $activity->description }}</p>
    </section><section class="activity-block"><h2>ข้อมูลการเข้าร่วม</h2><dl class="activity-detail-grid">
        <dt>หอพัก / ปีการศึกษา</dt><dd>{{ $activity->dorm->name }} / {{ $activity->academicYear->year }}</dd>
        <dt>เวลา</dt><dd>{{ $activity->starts_at->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} – {{ $activity->ends_at->timezone('Asia/Bangkok')->format('d/m/Y H:i') }}</dd>
        <dt>สถานที่</dt><dd>{{ $activity->location }}</dd>
        <dt>สถานะ</dt><dd>{{ App\Models\Activity::STATUS_LABELS[$activity->status] ?? $activity->status }}</dd>
        <dt>คะแนน / ผู้เข้าร่วม</dt><dd>{{ $activity->score }} คะแนน / {{ $activity->attendances_count }} คน</dd>
    </dl></section>
    <section class="activity-block"><h2>จัดการกิจกรรม</h2><div class="actions">
        <a class="activity-action-button" href="{{ route('admin.activities.edit', $activity) }}">แก้ไข / เปิด / ปิดกิจกรรม</a>
        <a class="activity-action-button" href="{{ route('admin.activities.attendances.index', $activity) }}">สแกน QR / ผู้เข้าร่วม</a>
        <form method="POST" action="{{ route('admin.activities.destroy', $activity) }}" onsubmit="return confirm('ยืนยันการลบกิจกรรม?')">@csrf @method('DELETE')<button>ลบกิจกรรม</button></form>
        <a class="activity-action-button" href="{{ route('admin.activities.index') }}">กลับรายการ</a>
    </div></section>
@endsection
