@extends('layouts.app')
@section('title', 'จัดการกิจกรรม | KKU DORM')
@section('content')
    <header class="activity-page-header">
        <div><h1>จัดการกิจกรรม</h1><p>ค้นหา ดูรายละเอียด และจัดการการเช็คชื่อกิจกรรม</p></div>
        <a class="activity-action-button" href="{{ route('admin.activities.create') }}">สร้างกิจกรรม</a>
    </header>
    <section class="activity-block" aria-labelledby="activity-filter-heading">
        <h2 id="activity-filter-heading">ค้นหาและกรองกิจกรรม</h2>
        <form class="filters activity-filters" method="GET">
            <label>ชื่อกิจกรรม<input name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อกิจกรรม"></label>
            <label>สถานะ<select name="status"><option value="">ทุกสถานะ</option>
                @foreach (App\Models\Activity::STATUS_LABELS as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach
            </select></label>
            <label>ปีการศึกษา<select name="academic_year_id"><option value="">ทุกปีการศึกษา</option>
                @foreach ($years as $year)<option value="{{ $year->id }}" @selected(request('academic_year_id') == $year->id)>{{ $year->year }}</option>@endforeach
            </select></label>
            <button type="submit">ค้นหา</button><a class="activity-action-button" href="{{ route('admin.activities.index') }}">ล้างตัวกรอง</a>
        </form>
    </section>
    <section class="activity-block" aria-labelledby="activity-list-heading">
        <h2 id="activity-list-heading">รายการกิจกรรม <small>ทั้งหมด {{ $activities->total() }} รายการ</small></h2>
        <div class="table-wrapper"><table><thead><tr><th>กิจกรรม / สถานที่</th><th>หอพัก / ปี</th><th>ช่วงเวลา</th><th>สถานะ</th><th>คะแนน</th><th>ผู้เข้าร่วม</th><th>จัดการ</th></tr></thead><tbody>
            @forelse ($activities as $activity)
                <tr>
                    <td><strong>{{ $activity->title }}</strong><br><span class="activity-muted">{{ $activity->location }}</span></td>
                    <td>{{ $activity->dorm->name }}<br>{{ $activity->academicYear->year }}</td>
                    <td>เริ่ม {{ $activity->starts_at->timezone('Asia/Bangkok')->format('d/m/Y H:i') }}<br>สิ้นสุด {{ $activity->ends_at->timezone('Asia/Bangkok')->format('d/m/Y H:i') }}</td>
                    <td><span class="activity-status activity-status-{{ $activity->status }}">{{ App\Models\Activity::STATUS_LABELS[$activity->status] ?? $activity->status }}</span></td>
                    <td>{{ $activity->score }} คะแนน</td><td>{{ $activity->attendances_count }} คน</td>
                    <td><a class="activity-action-button" href="{{ route('admin.activities.show', $activity) }}">ดูรายละเอียด</a></td>
                </tr>
            @empty
                <tr><td colspan="7">ไม่พบกิจกรรมตามตัวกรองที่เลือก</td></tr>
            @endforelse
        </tbody></table></div>
        {{ $activities->links() }}
    </section>
@endsection
