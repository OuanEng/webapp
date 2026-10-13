<p>{{ auth()->user()->name }} / {{ auth()->user()->email }}</p>
<form method="GET" class="filters">
    <label>ปีการศึกษา<select name="academic_year_id"><option value="">ทุกปีการศึกษา</option>
        @foreach ($years as $year)<option value="{{ $year->id }}" @selected(request('academic_year_id') == $year->id)>{{ $year->year }}</option>@endforeach
    </select></label>
    <button type="submit">แสดงข้อมูล</button><a href="{{ route('dashboard') }}">ล้างตัวกรอง</a>
</form>

