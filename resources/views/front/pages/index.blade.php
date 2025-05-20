<!DOCTYPE html>
<html lang="{{ str_replace('', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> <?php echo $row->title; ?></title>
    <link rel="shortcut icon" href="{{ asset('assets/common/images/logo.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
   <style>
        html,
        body {
            background-color: #1C1B1B;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            /*font-weight: 200;*/
            text-align: justify;
            /* height: 100vh; */
            margin: 0;
        }

        .container {
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        @media (min-width: 768px) {
            .container {
                width: 750px;
            }
        }

        @media (min-width: 992px) {
            .container {
                width: 970px;
            }
        }

        @media (min-width: 1200px) {
            .container {
                width: 1170px;
            }
        }
    </style>
</head>

<body>
    <div class="container font-sans antialiased">
        <div class="mt-8">           
            <div style="text-align: center; padding-top:10px" >
                <!-- <img src="{{ url('public/assets/common/images/logo.png') }}" alt="tag" style="width:100px"> -->
            </div>
            <div class="mt-4 p-6">
                <?php echo $row->description; ?>
            </div>
        </div>
    </div>
</body>

</html>