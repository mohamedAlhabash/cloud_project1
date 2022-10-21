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

data1 = new FormData();
setInterval(() => {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        method: 'GET',
        url: '/cache-status',
        data: data1,
        contentType: false,
        processData: false,

        success: function(data1) {
            console.log(data1);
        },
        error: function(data1) {
            console.log('error ' + data1);
        }
    });
}, 10 * 1000 * 60)

