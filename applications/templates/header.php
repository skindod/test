<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>Redang Holiday Beach Villa | Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo PUBLIC_URL; ?>css/assets/css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="<?php echo PUBLIC_URL; ?>css/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="<?php echo PUBLIC_URL; ?>css/assets/css/zabuto_calendar.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PUBLIC_URL; ?>css/assets/js/gritter/css/jquery.gritter.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo PUBLIC_URL; ?>css/assets/lineicons/style.css">    
    
    <!-- Custom styles for this template -->
    <link href="<?php echo PUBLIC_URL; ?>css/assets/css/style.css" rel="stylesheet">
    <link href="<?php echo PUBLIC_URL; ?>css/assets/css/style-responsive.css" rel="stylesheet">

    <script src="<?php echo PUBLIC_URL; ?>css/assets/js/chart-master/Chart.js"></script>

  </head>

  <body>

  <section id="container" >

      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <!--logo start-->
            <a href="dashboard" class="logo"><b>Redang Holiday Beach Villa</b></a>
            <!--logo end-->
            
            <div class="top-menu">
            	<ul class="nav pull-right top-menu">
                    <li><a class="logout" href="logout">Logout</a></li>
            	</ul>
            </div>
        </header>

      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
              
              	  <p class="centered"><a href="profile.html"><img src="<?php echo PUBLIC_URL; ?>css/assets/img/ui-sam.jpg" class="img-circle" width="60"></a></p>
              	  <h5 class="centered">Admin</h5>
              	  	
                  <li class="mt">
                      <a <?php if(isset($data["summary_marker"])){ ?>class="active" <?php } ?> href="dashboard">
                          <i class="fa fa-home"></i>
                          <span>Summary</span>
                      </a>
                  </li>
                  
                  <li class="sub-menu">
                      <a <?php if(isset($data["booking_marker"])){ ?>class="active" <?php } ?> href="booking">
                          <i class="fa fa-book"></i>
                          <span>Booking</span>
                      </a>
                  </li>

                  <li class="sub-menu">
                      <a <?php if(isset($data["price_marker"])){ ?>class="active" <?php } ?> href="price" >
                          <i class="fa fa-tag"></i>
                          <span>Prices</span>
                      </a>
                  </li>

                  <li class="sub-menu">
                      <a <?php if(isset($data["season_marker"])){ ?>class="active" <?php } ?> href="season" >
                          <i class="fa fa-cogs"></i>
                          <span>Seasons</span>
                      </a>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:;" >
                          <i class="fa fa-book"></i>
                          <span>Admin</span>
                      </a>
                  </li>
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->