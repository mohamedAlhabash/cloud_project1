@extends('backend.layouts.app')
@section('content')
    <!-- Start Upload section -->
    <section class="upload container">

        <div class="card">
            <form action="{{ route('showImage') }}" method="Post">
                @csrf
                <div>
                    <label for="key">Key</label>
                    <input type="text" name="key" id="key" value="{{ old('key') }}" placeholder="Enter Your Key">
                    @error('key')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn submit">Ok submit</button>
            </form>
        </div>
        <div class="preview-image">
            @if($source == 'DB')
                <img width="550" height="550"
                    src="{{ $attachment != null ? asset('uploads/' . $attachment) : asset('backend/img/no-image.jpg') }}">
            @else
                <img width="550" height="550" src="{{ $attachment != null ? $attachment : asset('backend/img/no-image.jpg') }}">
            @endif
        </div>
        <div>{{$source}}</div>


    </section>
    <!-- End Upload section -->
@stop
