@extends('backend.layouts.app')
@section('content')
    <!-- Start Keys -->
    <section class="keys container">
        <div class="keys-g">
            @foreach ($attachments as $attachment)
            <div>
                <span>
                    {{$attachment->key}}
                </span>
            </div>
            @endforeach
        </div> 

    </section>
    <!-- End Keys -->
@stop
