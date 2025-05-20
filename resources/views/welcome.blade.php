<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body style="background-color: #1D5BD4;">
	<center><img src="" id="site-logo" style="height:100px;width: 100px; margin-top: 10px;">

		<h2 style="color:#fff">Please go to <span style="color:rgb(252, 248, 46);"> My Next Vote </span>app to access this url or download the app.</h2>
	</center>
</body>
</html>

<script>
    // var base_url = window.location.origin
    var base_url = window.location.origin + '/' + window.location.pathname.split ('/') [1] + '/';
    var site_logo = base_url + 'public/assets/common/images/logo.png';
    var site_img = document.querySelector('#site-logo');
    site_img.setAttribute('src', site_logo);
</script>
