@extends('backend.layouts.app')
@section('content')
    <!-- Start statistics  -->
    <section class="statistics container">
        <div class="card">

            {{-- <div class="items">
                <span>Number Of Items In Cache : {{ $num_items }} </span>
            </div> --}}

            <div class="stats">
                <div class="stats-box">
                    <div class="stats-name">
                        <div class=""></div> Number Of Items In Cache :
                    </div>
                    <div class="stats-progress">
                        <span>{{ $num_items}}</span>
                    </div>
                </div>
                <hr>
                <div class="stats-box">
                    <div class="stats-name">
                        <div class=""></div> Replacement policy :
                    </div>
                    <div class="" style="height: 30px">
                        <span>{{ $replacment_policy }}</span>
                    </div>
                </div>
                <hr>

                <div class="stats-box">
                    <div class="stats-name">
                        <div class=""></div> Hit Rate:
                    </div>
                    <div class="stats-progress">
                        <span>{{ round($hit_rate) }}%</span>
                    </div>
                </div>
                <hr>

                <div class="stats-box">
                    <div class="stats-name">
                        <div class=""></div> Miss Rate:
                    </div>
                    <div class="stats-progress">
                        <span>{{ round($miss_rate) }}%</span>
                    </div>
                </div>
                <hr>

                <div class="stats-box">
                    <div class="stats-name">
                        <div class=""></div> Items size in the cache:
                    </div>
                    <div class="stats-progress">
                        <span>{{ number_format($current_capacity / 1000000, 2) }}Mb</span>
                    </div>
                </div>

            </div>

        </div>
    </section>
    <!-- End statistics  -->
@stop
