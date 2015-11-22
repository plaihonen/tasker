<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Tallettaja Add User</title>
  
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

                var errors = 0;
                console.log('Submitted registration');

                var email = $('#form input[name=username]').val();
                console.log('e-mail: ' + email);

                if (!isValidEmailAddress(email)) {
                    console.log('Validating e-mail');
                    var error = 'E-mail formaatti väärä.';
                    $('#form input[name=email]').next('span').text(error);
                    $('#rmessage').text(error);
                    errors = 1;
                }

                if (errors == 0) {
                    console.log('Validating passwords');
                    var pass1 = $('#form input[name=password]').val();
                    var pass2 = $('#form input[name=password2]').val();
                    console.log('PW1: ' + pass1 + ' PW2: ' + pass2);
                    
                    if (pass1.length < 6) {
                        var error = 'Salasana liian lyhyt. pitää olla vähintään 6 merkkiä';
                        $('#form input[name=password]').next('span').text(error);
                        $('#rmessage').text(error);
                        errors = 2;
                    } else {
                        if (pass1 == '' || pass1 != pass2) {
                            //show error
                            var error = 'Salasana tarkistus epäonnistui. Varmista niiden samanlaisuus.';
                            $('#form input[name=password]').next('span').text(error);
                            $('#form input[name=password2]').next('span').text(error);
                            $('#rmessage').text(error);
                            errors = 3;
                        } 
                    }
                }

                if (errors == 0) {

                    var postData = $('#form').serialize();
                    console.log('#form: Post DATA: '+postData);

                    $.ajax({
                        type: 'post',
                        url: 'inc/dbStore.php',
                        data: postData,
                        success: function (data) {
                            console.log('form was submitted DATA:'+data.message);
                            $('#rmessage').text(data.message);
                        }
                    });
                }

                return false;
            });

            function isValidEmailAddress(emailAddress) {
                var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
                return pattern.test(emailAddress);
            };

        });

    </script>

</head>
<body>

<div id="pageContainer">

    <header id="pageHeader">
        <h2>Lisää käyttäjä</h2>
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
                        <legend class="icon-pencil">&nbsp;Rekisteröi uusi Käyttäjä</legend>

                        <label for="username">Käyttäjänimi (e-mail):</label>
                        <input id="username" type="text" name="username" />
                        <!-- <span class="error"></span> -->

                        <label for="password">Salasana:</label>
                        <input id="password" type="password" name="password" />
                        <!-- <span class="error"></span> -->

                        <label for="password2">Salasana Uudelleen:</label>
                        <input id="password2" type="password" name="password2" />
                        <!-- <span class="error"></span> -->
                        <!--
                         <label>Email: </label>
                        <input type="text" name="email" />
                        -->
                        <!-- 
                        <label for="group_id">Ryhmä: </label>
                        <select id="group_id" name="group_id">
                            <option value="1">Käyttäjä</option>
                            <option value="2">Kehittäjä</option>
                            <option value="3">Administraattori</option>
                        </select>
                        -->
                        <label for="submit">&nbsp;</label>
                        <input type="hidden" name="action" value="register" />
                        <input type="hidden" name="group_id" value="1" />
                        <input id="submit" type="submit" value="Rekisteröi" />
                    </form>
                </div>
            </article>
            <!--
            <article class="sectionArticle clearfix">
                <div>
                    <i class="icon-table"></i> Icon test 1<br>
                    <i class="icon-paragraph-justify"></i> Icon test 2<br>
                    <i class="icon-list"></i> Icon test 3<br>
                    <i class="icon-calendar"></i> Icon test 4<br>
                    <i class="icon-file"></i> Icon test 5<br>
                    <i class="icon-home"></i> Icon test 6<br>
                    <i class="icon-pencil"></i> Icon test 7<br>
                </div>
            </article>
            -->
        </section>

    </div>

    <footer id="pageFooter">
        <!-- <button style="margin:5px auto 5px auto;">Export</button> -->
    </footer>

</div>

</body>
</html>