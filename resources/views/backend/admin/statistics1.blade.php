<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

    <style>
        header {
            position: static;
            left: 0;
            top: 0;
            background-color: var(--cyan);
            box-shadow: 0 2px 10px 0 rgba(0, 0, 0, .3);
            height: 10vh;
            z-index: 1000;
        }

        nav {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }


        nav ul {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        nav ul li {
            padding: 10px 15px;
            transition: var(--main-transition);
            min-width: fit-content;
            max-width: 100%;
            border-radius: 10px;
            margin-left: 10px;
            cursor: pointer;

        }

        nav ul li a {
            color: black;
            white-space: nowrap;
            font-size: 18px;
            font-weight: 600;
            transition: var(--main-transition);
            display: inline-block;
            width: 100%;
            height: 100%;
        }


        nav ul li:hover,
        nav ul li.active {
            background-color: var(--primary);
        }
    </style>
    <title>Document</title>
</head>

<body>

    <header>
        <nav class="container">
            <ul class="links">
                <li class=""><a href="{{ route('cacheStatus') }}">Statstics</a></li>
                <li class=""><a href="{{ route('poolResizing') }}">Pool resizing</a></li>
                <li class=""><a href="{{ route('cache-config') }}">Cache configiration</a></li>
            </ul>
        </nav>
    </header>
    <div class="chartCard">
        <div class="chartBox">
            <canvas id="myChart"></canvas>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-12 border">
                <div class="chartCard">
                    <div class="chartBox">
                        <canvas id="myChart1"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12 border">
                <div class="chartCard">
                    <div class="chartBox">
                        <canvas id="myChart2"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 border">
                <div class="chartCard">
                    <div class="chartBox">
                        <canvas id="myChart3"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 border">
                <div class="chartCard">
                    <div class="chartBox">
                        <canvas id="myChart4"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 border">
                <div class="chartCard">
                    <div class="chartBox">
                        <canvas id="myChart5"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let label = <?php echo json_encode($getTimeStamp) ?? []; ?>;
        let value1 = <?php echo json_encode($MissRate) ?? []; ?>;
        let value2 = <?php echo json_encode($HitRate) ?? []; ?>;
        let value3 = <?php echo json_encode($TotalItemSize) ?? []; ?>;
        let value4 = <?php echo json_encode($CountRequests) ?? []; ?>;
        let value5 = <?php echo json_encode($NumberOfItems) ?? []; ?>;

        const data1 = {
            labels: label,
            datasets: [{
                label: "miss rate",
                data: value1,
                backgroundColor: ["rgba(255, 26, 104, 0.2)"],
                borderColor: ["rgba(255, 26, 104, 1)"],
                borderWidth: 1,
            }, ],
        };
        const data2 = {
            labels: label,
            datasets: [{
                label: "number of items",
                data: value2,
                backgroundColor: ["rgba(255, 26, 104, 0.2)"],
                borderColor: ["rgba(255, 26, 104, 1)"],
                borderWidth: 1,
            }, ],
        };
        const data3 = {
            labels: label,
            datasets: [{
                label: "total size",
                data: value3,
                backgroundColor: ["rgba(255, 26, 104, 0.2)"],
                borderColor: ["rgba(255, 26, 104, 1)"],
                borderWidth: 1,
            }, ],
        };
        const data4 = {
            labels: label,
            datasets: [{
                label: "number of items",
                data: value4,
                backgroundColor: ["rgba(255, 26, 104, 0.2)"],
                borderColor: ["rgba(255, 26, 104, 1)"],
                borderWidth: 1,
            }, ],
        };
        const data5 = {
            labels: label,
            datasets: [{
                label: "requests served per minute",
                data: value5,
                backgroundColor: ["rgba(255, 26, 104, 0.2)"],
                borderColor: ["rgba(255, 26, 104, 1)"],
                borderWidth: 1,
            }, ],
        };

        const config1 = {
            type: "line",
            data: data1,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };
        const config2 = {
            type: "line",
            data: data2,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };
        const config3 = {
            type: "line",
            data: data3,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };
        const config4 = {
            type: "line",
            data: data4,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };
        const config5 = {
            type: "line",
            data: data5,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };

        // render init block
        const myChart1 = new Chart(document.getElementById("myChart1"), config1);
        const myChart2 = new Chart(document.getElementById("myChart2"), config2);
        const myChart3 = new Chart(document.getElementById("myChart3"), config3);
        const myChart4 = new Chart(document.getElementById("myChart4"), config4);
        const myChart5 = new Chart(document.getElementById("myChart5"), config5);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
    {{-- @stop --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
