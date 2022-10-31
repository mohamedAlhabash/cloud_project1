@extends('backend.layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
@section('content')
    <!-- Start statistics  -->
        <div class="card">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Num items</th>
                            <th>Hit rate</th>
                            <th>Miss rate</th>
                            <th>Current capacity</th>
                            <th>Replcment policy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cachedItem as $recored)
                            <tr>
                                <td>{{$recored->num_items}}</td>
                                <td>{{round($recored->hit_rate)}}%</td>
                                <td>{{round($recored->miss_rate)}}%</td>
                                <td>{{number_format($recored->current_capacity / 1000000, 2) }}Mb</td>
                                <td>{{$replacment_policy}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center"></td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <div class="float-right">
                                    {{$cachedItem->links()}}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        </div>
    <!-- End statistics  -->
@stop

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
@stop
