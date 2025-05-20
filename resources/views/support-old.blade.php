<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background: #1C272B;
        }

        section.banner_sec {
            background: #1C272B;
            padding: 10% 150px;
        }

        .main {
            display: flex;
            align-items: center;

            justify-content: space-between;
        }

        .banner_img img {
            width: 100%;
        }

        .banner_img {
            width: 40%;
            padding: 0 15px;
        }

        .banner_text {
            width: 50%;
            padding: 0 15px;
        }

        .banner_text h2 {
            color: white;
            margin-bottom: 20px;
            font-size: 40px;
            font-family: sans-serif;
        }

        .banner_text p {
            color: white;
            font-size: 20px;
            margin-bottom: 60px;
            font-family: sans-serif;
        }

        .banner_text a {
            background: #7EAF92;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 17px;
            font-family: sans-serif;
            display: inline-block;
            margin-top: 10px;
        }

        @media only screen and (min-width: 1680px) and (max-width: 2580px) {
            .banner_text h2 {
                color: white;
                font-size: 72px;
            }
        }

        @media only screen and (max-width: 992px) {
            section.banner_sec {
                padding: 8% 15px;
            }
        }

        @media only screen and (max-width: 767px) {
            .banner_text h2 {
                text-align: center;
                color: white;
                font-size: 35px;
            }

            .main {

                flex-wrap: wrap;

            }

            .banner_img {
                width: 80%;
                margin: 70px auto 0;
            }

            .banner_text p {
                margin-bottom: 30px;
            }

            .banner_text {
                width: 100%;
                padding: 0 15px;
            }

            section.banner_sec {
                text-align: center;
                padding: 15px 15px;
            }
        }
    </style>
</head>

<body>

    <section class="banner_sec">
        <div class="main">
            <div class="banner_text">
                <h2>Support Center</h2>
                <p>Need help? Submit a support request and we'll get back to you ASAP. Also you can directly mail us on {{ $adminData->email }}</p>
                <a href="{{ 'mailto:'.$adminData->email }}">Submit a Request</a>
            </div>
            <div class="banner_img">
                <img src="{{asset('public/uploads/support.png')}}">
            </div>

        </div>

    </section>
    <center>
        <p style="color:white;">Support Business Hours: </p>
        <p style="color:white;">Monday to Friday 10 AM EST to 06 PM EST</p>
    </center>
</body>

</html>
