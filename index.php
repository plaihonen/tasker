<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Kirjaudu</title>
  
    <!-- Remote CSS and JS -->
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/vader/jquery-ui.min.css" rel="stylesheet">

    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

    <!-- Local CSS -->
    <link href="css/tasker.css" rel="stylesheet">
    <link href="css/icomoon.css" rel="stylesheet">

    <!-- Local JS -->
    
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <style type="text/css"> .gradient { filter: none; } </style>
    <![endif]-->


    <script>
        $(function() {
            console.log('Entering Tallettaja');
        }
    </script>

</head>
<body>

<div id="pageContainer">

    <header id="pageHeader">
        <h2>Tallettaja Kotisivu</h2>
    </header>

    <nav id="pageNav">
    </nav>

    <div id="contentContainer" class="clearfix">

        <section id="pageSection">

            <header class="sectionHeader"><div id="notes"></div></header>
        
            <article class="sectionArticle">
                <a href="tasker.php" title="Avaa Tallettaja">
                    <div id="taskerLogo" style="margin-left:auto;margin-right:auto;"></div>
                </a>

                <a href="stats.php" title="Avaa Statistiikat">
                    <div id="statsLogo" style="margin-left:auto;margin-right:auto;"></div>
                </a>
            </article>

        </section>

    </div>

    <footer id="pageFooter">
        <!-- <button style="margin:5px auto 5px auto;">Export</button> -->
    </footer>

</div>

</body>
</html>