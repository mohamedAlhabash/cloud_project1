import './bootstrap';


data = new FormData();
setInterval(() => {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        method: 'POST',
        url: '/cache-status',
        data: data,
        contentType: false,
        processData: false,

        success: function(data) {
            console.log(data);
        },
        error: function(data) {
            console.log('error ' + data);
        }
    });
}, 5 * 1000)


