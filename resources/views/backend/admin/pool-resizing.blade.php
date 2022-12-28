@extends('backend.admin.layouts.admin')
@section('content')
    <input type="checkbox" id="pool_resizing" class="" style="margin-top: 3%"> Automatic mode

    <section id="manual" class=" details container" style="height: fit-content">
        <div class="one" style="width: 100%;">
            <form>

                <div class="progression" style="width: 50%;">
                    <p class="progress-text" style="margin-bottom: 10px;">
                        The Memcache Pool Size is
                        <span name="ec2Count" id="ec2Count"
                            style="padding: 5px ; background: transparent; border: none; outline: none; font-size:16px; font-weight: bold;color: red">{{ $ec2Count }}</span>
                    </p>

                    <div style="display: flex; gap: 10px;margin-top: 15px;">
                        <button style="width:200px;align-self: center; " class="btn" id="btnDecrease">shrinking</button>
                        <button style="width:200px; align-self: center;" class="btn" id="btnIncrease">growing</button>
                    </div>
                </div>

            </form>

        </div>
    </section>

    <section id="automatic" class="cache- container" style="display: none">
        <form id="auto_form">

            <div class="config-cache">
                <h1> Automatic mode </h1>
                <br>
                <label for="cache-capcity">Max Miss Rate threshold:</label>
                <div>
                    <input class="slider" type="range" value="{{ $statistics->max_miss ? $statistics->max_miss : 0}}" name="max_miss" max="100" min="0"
                        step="1" id="cache-capcity" oninput="slider_value()">
                    <span id="value">{{ $statistics->max_miss}} %</span>
                </div>
            </div>

            <div class="config-cache">
                <label for="cache-capcity1">Min Miss Rate threshold:</label>
                <div>
                    <input class="slider" type="range" value="{{ $statistics->min_miss}}" name="min_miss" max="100" min="0"
                        step="1" id="cache-capcity1" oninput="slider_value1()">
                    <span id="value1">{{ $statistics->min_miss}} %</span>
                </div>
            </div>

            {{-- <div class="config-cache">
                <label for="cache-capcity2">Ratio by which to expand the pool:</label>
                <div>
                    <input class="slider" type="range" value="0" name="expand"
                        max="2.0" min="0" step="0.5" id="cache-capcity2" oninput="slider_value2()">
                    <span id="value2">0 %</span>

                </div>
            </div>

            <div class="config-cache">
                <label for="cache-capcity3">Ratio by which to shrink the pool:</label>
                <div>
                    <input class="slider" type="range" value="0" name="shrink"
                        max="1.0" min="0" step="0.5" id="cache-capcity3" oninput="slider_value3()">
                    <span id="value3">0 %</span>
                </div>
            </div> --}}
            <button style="width:200px; align-self: center;" type="submit" class="btn" id="auto_submit">Ok</button>
        </form>
    </section>

    <div style="display: flex; gap: 10px;margin-top: 15px; margin-left:2%">
        <button class="btn" style="height: 70px ; margin-top: 20px;background-color:rgb(235, 41, 41)">Deleting</button>
        <button class="btn" style="height: 70px ; margin-top: 20px;background-color:#5f6566">Clear</button>
    </div>

@stop
@section('script')
    <script>
        let increaseBtn = document.getElementById('btnIncrease')
        let decreaseBtn = document.getElementById('btnDecrease')

        let ec2Count = document.getElementById('ec2Count')

        increaseBtn.onclick = function() {

            if (ec2Count.textContent < 8) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: 'POST',
                    url: "{{ route('increasePools') }}",
                    contentType: false,
                    processData: false,

                    beforeSend: function(data) {
                        increaseBtn.textContent = 'growing...'
                    },
                    success: function(data) {
                        console.log('success');
                        ec2Count.textContent = parseInt(ec2Count.textContent) + 1;
                        increaseBtn.textContent = 'growing';
                    },
                    error: function(data) {
                        console.log('error: ' + data);
                    }
                });
            }

        }

        decreaseBtn.onclick = function() {
            if (ec2Count.textContent > 1) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: 'POST',
                    url: "{{ route('decreasePools') }}",
                    contentType: false,
                    processData: false,

                    beforeSend: function(data) {
                        decreaseBtn.textContent = 'shrinking...'
                    },
                    success: function(data) {
                        console.log('success');
                        ec2Count.textContent = parseInt(ec2Count.textContent) - 1;
                        decreaseBtn.textContent = 'shrinking';

                    },
                    error: function(data) {
                        console.log('error: ' + data);
                    }
                });
            }
        }

        let checkBoxToggle = document.getElementById('pool_resizing');
        let automaticMode = document.getElementById('manual');
        let manualMode = document.getElementById('automatic');

        checkBoxToggle.onclick = function() {
            if (!checkBoxToggle.checked) {
                manualMode.style.display = 'none';
                automaticMode.style.display = 'block';
            } else {
                manualMode.style.display = 'block';
                automaticMode.style.display = 'none';
            }
        }

        let max_miss = document.getElementById('cache-capcity')
        let min_miss = document.getElementById('cache-capcity1');
        let expand = document.getElementById('cache-capcity2');
        let shrink = document.getElementById('cache-capcity3');

        let auto_form = document.getElementById('auto_form');
        $('#auto_form').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                method: 'POST',
                url: "{{ route('autoScalling') }}",
                data: new FormData(this),
                contentType: false,
                processData: false,

                success: function(max_miss) {
                    console.log('success');
                    console.log(max_miss);
                },
                error: function(data) {
                    console.log('error: ' + data);
                }
            });
        })
    </script>
@stop
