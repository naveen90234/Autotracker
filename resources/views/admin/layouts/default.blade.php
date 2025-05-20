<!DOCTYPE html>
  <html>

  <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>::@yield('title','Cook Up')::</title>
      <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
      @include('Admin.layout.css')
  </head>
 <body>
  <div class="container-scroller">
	  <!-- Navbar-->
	   @include('Admin.layout.header')
	  <div class="container-fluid page-body-wrapper">
	    <!-- Sidebar menu-->
	  @include('Admin.layout.sidebar')
	    <!-- <main class="app-content">

			  <?php //echo  $pageContent ?>


	  </main> -->
	  
	        <div class="main-panel">
			
			 @include('Admin.layout.footer')
			</div>
	  
	  </div>
	  </div>
  @include('Admin.layout.js')
  </body>
</html>
