<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('assets/common/css/contact-us.css') }}" type="text/css" media="screen">
    <link rel="shortcut icon" href="{{ asset('assets/common/images/logo.png') }}" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .supportImage {
            max-width: 100%;
            height: auto;
        }
        .btn-primary{
            float:none !important;
        }
    </style>
</head>

<body>
    <div class="cover-wrap">
        <header style="text-align: center; color:#fff;margin-top: 10px;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; padding:5px">
                <img src="{{ asset('assets/common/images/logo.png') }}" alt="logo" style="height: 60px; width: 60px;"> 
                <h1 style="margin: 0; font-size: 1.2em;">{{ env('APP_NAME') }}</h1>
            </div>
        </header>
        <div class="container">
            @if (session()->has('message'))
                <div class="alert alert-success" id="Message">
                    {{ session()->get('message') }}
                </div>
            @endif
            <div class="main-container">
                <div class="box-one">
                    <form class="contact100-form" action="{{ route('submit_support-form') }}" method="POST">
                        @csrf
                        <span class="contact100-form-title"> Send Us A Message</span>
                        <div class="wrap-input100 validate-input" data-validate="Please enter your name">
                            <input class="input100" type="text" name="name" placeholder="Full Name" value="{{ old('name') }}"/>
                            <span class="focus-input100"></span>
                        </div>
                        <div style="margin-top:5px; margin-bottom:5px;" class="form-group">
                            <span id="title_error" class="text-danger alert-danger" role="alert">
                                @error('name')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>


                        <div class="wrap-input100 validate-input" data-validate="Please enter your email: e@a.x">
                            <input class="input100" type="email" name="email" placeholder="E-mail" value="{{ old('email') }}"/>
                            <span class="focus-input100"></span>
                        </div>
                        <div style="margin-top:5px; margin-bottom:5px;" class="form-group">
                            <span id="title_error" class="text-danger alert-danger" role="alert">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="wrap-input100 validate-input" data-validate="Please enter your message">
                            <textarea class="input100" name="message" placeholder="Your Message">{{ old('message') }}</textarea>
                            <span class="focus-input100"></span>
                        </div>
                        <div style="margin-top:5px; margin-bottom:5px;" class="form-group">
                            <span id="title_error" class="text-danger alert-danger" role="alert">
                                @error('message')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                      
                        <div class="container-contact100-form-btn">
                            <button type="submit" name="submit" class="contact100-form-btn">
                                <span>
                                    <i class="fa fa-paper-plane-o m-r-6" aria-hidden="true"></i>
                                    Send
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="box-two">
                    <img class="supportImage" src="{{ asset('public/uploads/support_bg.png') }}" />
                </div>
            </div>
        </div>

        <footer class="cover-footer" style="color: #fff; text-align: center; padding:10px 0px;">
            <p><i class="fa fa-copyright" aria-hidden="true"></i> <script type="text/javascript">document.write( new Date().getFullYear() );</script> {{ env('APP_NAME') }}. All rights reserved.</p>
            <p>
                <a href="{{url('/page/about-us')}}" target="_blank" style="color: #FFD700; text-decoration: none;">About Us</a> | 
                <a href="{{url('/page/terms-and-condition')}}" target="_blank" style="color: #FFD700; text-decoration: none;">Terms & Conditions</a> | 
                <a href="{{url('/page/privacy-policy')}}" target="_blank" style="color: #FFD700; text-decoration: none;">Privacy Policy</a>
            </p>
        </footer>
    </div>
    


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <script>
        $(document).ready(function(){
            $('#Message').delay(1500).slideUp(300);
        });
    </script>
</body>


</html>

