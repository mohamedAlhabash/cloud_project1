@extends('backend.layouts.app')
@section('content')
    <!-- Start Upload section -->
    <section class="upload container">
        <div class="card">
            <form action="{{route('storeImage')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <label for="key">Key</label>
                    <input type="text" name="key" id="key" placeholder="Enter Your Key">
                    @error('key')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="second">
                    <label for="upload" class="i-lable">Upload</label>
                    <input type="file" name="value" id="upload" placeholder="Enter Your image" >
                    @error('value')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn submit">Ok submit</button>
            </form>
        </div>
        <div class="preview-image">
            <img id="imageUploaded" width="550" height="500" src="{{ asset('backend/img/no-image.jpg') }}">
        </div>
    </section>
    <!-- End Upload section -->
@endsection
@section('script')
    <script>
        let imgInp = document.getElementById('upload')
        let image = document.getElementById('imageUploaded')

        imgInp.onchange = evt => {
            console.log(imgInp.files);
            const [file] = imgInp.files
            if (file) {
                image.src = URL.createObjectURL(file)
            }
        }
    </script>
@endsection
