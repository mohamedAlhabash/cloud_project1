@extends('backend.admin.layouts.admin')
@section('content')
<section class=" details container" style="height: fit-content">
    <div class="items">
        <p>charts to show the number of workers as well as to aggregate statistics for the memcache pool</p>
        <canvas id="myChart" aria-valuetext="100" style="width:100%"></canvas>
    </div>
</section>
@endsection
