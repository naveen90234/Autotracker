<header class="header">
    <div class="container-fluid">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".js-navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="mnu">Menu</span>
                </button>
                <a class="navbar-brand" href="index.html"><img src="{{ url('assets/common/images/logo.png') }}"></a>
            </div>
            <div class="collapse navbar-collapse js-navbar-collapse ">

                <ul class="nav navbar-nav pull-right">
                    <li class="active"><a href="home.html">Main <small>Home page</small></a></li>
                    <li class=""><a href="our-team.html">Our Team <small>Team Members</small></a></li>
                    <li class=""><a href="how-it-works.html">How it work <small>Our work</small></a></li>
                    <li class=""><a href="hiring.html">Hiring <small>What we do?</small></a></li>
                    <li><a href="contact-us.html">Contact <small>Connect with us</small></a></li>
                    <li class="login"><a href="javascript:void(0)" data-toggle="modal" data-target="#login">Login</a></li>
                </ul>

            </div>
            <!-- /.nav-collapse -->
        </nav>
    </div>
</header>
@if (session('success_msg'))
    <div class="alert alert-success">
        {{ session('success_msg') }}
    </div>
@endif
@if (session('error_msg'))
    <div class="alert alert-danger">
        {{ session('error_msg') }}
    </div>
@endif
