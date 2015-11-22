<?php require_once "inc/auth.http.php" ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Statistiikat</title>
  
    <!-- Remote CSS and JS -->
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/vader/jquery-ui.min.css" rel="stylesheet">

    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

    <!-- Local CSS -->
    <link href="css/tasker.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
    <link href="css/icomoon.css" rel="stylesheet">

    <!-- Local JS -->
    <!-- <script src="js/tasker.js"></script> -->

    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <style type="text/css"> .gradient { filter: none; } </style>
    <![endif]-->

    <script>
        $(function() {

            var tooltipTimeOut = 1500;

            $(document).tooltip({
                show: {
                    effect: 'fade'
                },
                track: true,
                open: function (event, ui) {
                    setTimeout(function () {
                        $(ui.tooltip).hide('fade');
                    }, tooltipTimeOut);
                }
            });

            $('#form').on('submit', function (e) {
                var postData = $('#form').serialize();
                console.log('#form: Post DATA: '+postData);

                $.ajax({
                    type: 'post',
                    url: 'inc/auth.http.php',
                    data: postData,
                    success: function (data) {
                        console.log('form was submitted DATA:'+data.message);
                        $('#rmessage').text(data.message);
                    }
                });

                return false;
            });
        });
    </script>

</head>
<body>

<div id="pageContainer">

    <header id="pageHeader">
        <h2>Kirjaudu sisään</h2>
    </header>

    <nav id="pageNav">
        <p id="rmessage">
        </p>
    </nav>

    <div id="contentContainer" class="clearfix">

        <section id="pageSection">

            <header class="sectionHeader"><div id="notes"></div></header>

            <article class="sectionArticle clearfix">

                <div id="regform">
					 <form id="form" method="post" action="">
					    <label for="username">Username:</label>
					    <input id="username" type="text" name="username" />
					    <br>

					    <label for="password">Password:</label>
					    <input id="password" type="password" name="password" />
					    <br>

					    <label for="auto">Remember me?:</label>
					    <input id="auto" type="checkbox" name="auto" />
					    <br>

                        <label for="submit">&nbsp;</label>
					    <input type="hidden" name="mode" value="login" />
					    <input id="submit" type="submit" value="login" />
					  </form>
                </div>
            </article>

            <article class="sectionArticle clearfix">
                <div>
                    <span class="icon-pencil"></span> <a href="/adduser.php">Rekisteröidy</a>
                </div>
            </article>

        </section>

    </div>

    <footer id="pageFooter">
        <!-- <button style="margin:5px auto 5px auto;">Export</button> -->
    </footer>

</div>

</body>
</html>