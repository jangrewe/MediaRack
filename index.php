<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>MediaRack</title>
	
	<meta property="og:title" content="MediaRack" />
	<meta property="og:description" content="My TV Show and Movie library" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="http://<?php echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>" />
	<meta property="og:image" content="http://<?php echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>img/opengraph.jpg" />

    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-slate.min.css" rel="stylesheet">
    <link href="css/mediarack.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><img alt="MediaRack" src="img/logo.png" /></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
			<li class="active"><a href="#home">Home</a></li>
            <li><a href="#shows">TV Shows</a></li>
            <li><a href="#movies">Movies</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container">

		<div class="content" id="home">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Newest TV Show Episodes</h3>
				</div>
				<div class="panel-body">
					<div class="jcarousel-wrapper">
						<div class="jcarousel jcShows">
							<ul id="latestShows"></ul>
						</div>
						<a href="#" class="jcarousel-control-prev jcShows">&lsaquo;</a>
						<a href="#" class="jcarousel-control-next jcShows">&rsaquo;</a>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Newest Movies</h3>
				</div>
				<div class="panel-body">
					<div class="jcarousel-wrapper">
						<div class="jcarousel jcMovies">
							<ul id="latestMovies"></ul>
						</div>
						<a href="#" class="jcarousel-control-prev jcMovies">&lsaquo;</a>
						<a href="#" class="jcarousel-control-next jcMovies">&rsaquo;</a>
					</div>
				</div>
			</div>
		</div>

		<div class="content" id="shows"></div>

		<div class="content" id="movies"></div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.jcarousel.min.js"></script>
	<script src="js/jquery.lazyload.min.js"></script>
	<script src="js/jquery.scrollstop.js"></script>
	<script src="js/mediarack.js"></script>
	
	<!-- Piwik -->
	<script type="text/javascript">
	  var _paq = _paq || [];
	  _paq.push(["trackPageView"]);
	  _paq.push(["enableLinkTracking"]);

	  (function() {
		var u=(("https:" == document.location.protocol) ? "https" : "http") + "://stats.faked.org/";
		_paq.push(["setTrackerUrl", u+"piwik.php"]);
		_paq.push(["setSiteId", "7"]);
		var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
		g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
	  })();
	</script>
	<!-- End Piwik Code -->
	
  </body>
</html>
