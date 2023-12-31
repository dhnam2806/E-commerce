<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="shortcut icon" href="images/favicon.png" type="">
    <title>N T N Fashion</title>
    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="home/css/bootstrap.css" />
    <!-- font awesome style -->
    <link href="home/css/font-awesome.min.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="home/css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="home/css/responsive.css" rel="stylesheet" />
    <!-- sweetalest2 -->
    <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>

</head>

<body>

    @include('sweetalert::alert')


    <div class="hero_area">
        <!-- header section strats -->
        @include('home.header')
        <!-- end header section -->
        <!-- slider section -->
        @include('home.slide')
        <!-- end slider section -->
    </div>
    <!-- why section -->
    @include('home.why')
    <!-- end why section -->

    <!-- arrival section -->
    @include('home.arrival')
    <!-- end arrival section -->

    <!-- product section -->
    @include('home.product')
    <!-- end product section -->

    <!-- subscribe section -->
    @include('home.subscribe')
    <!-- end subscribe section -->

    <!-- client section -->
    @include('home.client')
    <!-- end client section -->

    <!-- footer start -->
    @include('home.footer')
    <!-- footer end -->

    <!-- jQery -->
    <script src="home/js/jquery-3.4.1.min.js"></script>
    <!-- popper js -->
    <script src="home/js/popper.min.js"></script>
    <!-- bootstrap js -->
    <script src="home/js/bootstrap.js"></script>
    <!-- custom js -->
    <script src="home/js/custom.js"></script>
</body>

</html>