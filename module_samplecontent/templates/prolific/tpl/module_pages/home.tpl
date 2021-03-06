<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 3.0 License

Name       : Prolific
Description: A two-column, fixed-width design with a bright color scheme.
Version    : 1.0
Released   : 20120719
-->
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
        <title>%%additionalTitle%%%%title%% | Kajona</title>
                <link rel="canonical" href="%%canonicalUrl%%" />
		<link href="http://fonts.googleapis.com/css?family=Abel" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" href="_webpath_/templates/prolific/css/style.css" />
        %%kajona_head%%
	</head>
	<body>
		<div id="outer">
		<div id="wrapper">
			<div id="menu">
				<ul>
                    %%masterportalnavi_navigation%%
					<!--<li class="first"><a href="#">Home</a></li>-->
					<!--<li><a href="#">Products</a></li>-->
					<!--<li><a href="#">Services</a></li>-->
					<!--<li><a href="#">Portfolio</a></li>-->
					<!--<li><a href="#">Links</a></li>-->
					<!--<li><a href="#">Blog</a></li>-->
					<!--<li><a href="#">About</a></li>-->
					<!--<li><a href="#">Careers</a></li>-->
					<!--<li class="last"><a href="#">Contact</a></li>-->
				</ul>
				<br class="clearfix" />
			</div>
			<div id="headerHome">
				<div id="logo">
					<h1><a href="_webpath_">Kajona v4</a></h1>
				</div>
				<div id="search">
					<form action="_webpath_" method="get" >
						<div>
							<input type="text" class="form-text" name="searchterm" size="32" maxlength="64" />
                            <input type="hidden" name="page" value="search"/>
						</div>
					</form>
				</div>
			</div>
			<div id="page">
				<div id="sidebar">
					<div class="box">
						<ul class="list">
                            %%mastermainnavi_navigation%%

						</ul>
					</div>
				</div>
				<div id="content">

                    <!-- Please note that the following list is only for demo-purposes.
                When using the template for "real" installations, the list of
                placeholders should be stripped down to a minimum. -->

                    %%headline_row%%
                    %%content_paragraph|image%%
                    %%special_news|guestbook|downloads|gallery|galleryRandom|form|tellafriend|maps|search|navigation|faqs|postacomment|votings|userlist|rssfeed|tagto|portallogin|portalregistration|portalupload|directorybrowser|lastmodified|tagcloud|downloadstoplist|flash|mediaplayer|tags|eventmanager%%

                    <div class="twoColumns">
                        <div>
                            %%column1_paragraph|image%%
                        </div>
                        <div>
                            %%column2_paragraph|image%%
                        </div>
                    </div>



					<!--<br class="clearfix" />-->
				</div>
				<br class="clearfix" />
			</div>
		</div>
		<div id="footer">
			&copy; 2012 Untitled | Design by <a href="http://www.freecsstemplates.org/">FCT</a></a>
		</div>
		</div>
	</body>
</html>