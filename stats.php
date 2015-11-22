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
    <link href="css/jquery.tidy.table.css" rel="stylesheet" type="text/css">

    <!-- Local JS -->
    <!-- <script src="js/tasker.js"></script> -->
    <script src="js/jquery.tidy.table.min.js"></script>

    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <style type="text/css"> .gradient { filter: none; } </style>
    <![endif]-->


    <script>
        $(function() {

            var statsData;
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


            // Initialize date picker
            $( ".datepicker" ).datepicker({ 
                                            changeMonth: true, 
                                            changeYear: true, 
                                            dateFormat: "dd-mm-yy", 
                                            minDate: new Date(2013, 11 - 1, 1),
                                            // monthNames: [ "Tammi", "Helmi", "Maalis", "Huhti", "Touko", "Kesä", "Heinä", "Elo", "Syys", "Loka", "Marras", "Joulu" ],
                                            showAnim: "fadeIn"
                                         });


            $('#stats').on('submit', function (e) {
                var postData = $('#stats').serialize();
                console.log('#stats: Post DATA: '+postData);
                post(postData);
                return false;
            });


            function post(pData) {
                pData += "&userId="<?= $_SESSION['userId'] ?>;
                console.log('Posting DATA: '+pData);
                $.ajax({
                    type: 'post',
                    url: 'inc/dbStore.php',
                    data: pData,
                    success: function (data) {
                        console.log('form was submitted DATA:'+data.message);
                        tmpData = data.data;
                        if (tmpData.length > 0) {
                            statsData = data.data;
                            tmpData = "";
                            // arrange to table
                            tidify();
                        } else {
                            var msg = "Ei tuloksia. Voit yrittää toisella ajan jaksolla.";
                            $('#notes').html(msg);
                        }
                    }
                });
            }

            function tidify() {
                
                // put results into an array
                var rows = [];
                $.each(statsData, function(idx, rs) {
                    var row = [];
                    var address = rs.siteAddress+"<br>"+rs.siteZip+" "+rs.siteCity;
                    row.push(rs.entryDate, rs.taskType, rs.siteName, rs.siteType, address, rs.notes);
                    rows.push(row);
                });

                // console.log("rows 1 : " + JSON.stringify(rows));

                $('#tidyContainer').TidyTable({
                    enableCheckbox : false,
                    enableMenu     : true
                    },
                    {
                    columnTitles : ['Päivämäärä','Tehtävä tyyppi','Kohde','Kohde Tyyppi','Osoite','Muistiinpanot'], 
                    // osoite : siteAddress, s.siteZip, s.siteCity
                    columnValues : rows
                    ,
                    menuOptions : [
                        ['- Valitse -', null],
                        ['Exceliin CSV:nä', { callback : doSomething1 }],
                        // ['Tulosta', { callback : doSomething2 }]
                        ['Tulosta Taulukko', { callback : doSomething2 }]
                    ],
                    postProcess : {
                        table  : doSomething3,
                        column : doSomething4,
                        menu   : doSomething5
                    }
                });
            }
            // do something with selected results
            function doSomething1(rows) {
                var csv = JSON2CSV(statsData);
                var name = "tasker_export.csv";
                window.open("data:text/csv;charset=utf-8," + escape(csv));

                // http://stackoverflow.com/questions/14964035/how-to-export-javascript-array-info-to-csv-on-client-side
                // var encodedUri = encodeURI(csvContent);
                // var link = document.createElement("a");
                // link.setAttribute("href", encodedUri);
                // link.setAttribute("download", "my_data.csv");

                // link.click(); // This will download the data file named "my_data.csv".

            }

            // function doSomething2(rows) {
            function doSomething2(rows) {
                // alert('callback2(rows=' + rows.length + "')");
                var rawTable = $('#tidyContainer table').html();
                // rawTable = rawTable.replace(/<th><input type="checkbox"><\/th>/g, '');
                // rawTable = rawTable.replace(/<td><input type="checkbox"><\/td>/g, '');
                rawTable = rawTable.replace(/<td title=""><\/td>/g, '<td>&nbsp;<\/td>');
                
                var button = "<button id=\"close\" onclick='window.close()'>Sulje Ikkuna</button>";

                var header = "<html><head><title>Print</title><style>";
                header += "table td, table th { border: 1px solid #EEEEEE; padding: 3px; }";
                header += "#close { clear:both; position:absolute; right:100px; bottom:60px; }";
                header += "</style></head><body>";
                var footer = "</body></html>";

                newWin = window.open(", ", 'popup', 'toolbar = no, status = no');
                newWin.document.write(header + "<table>" + rawTable + "</table>" + footer);
                newWin.window.location.reload();    // this is the secret ingredient
                newWin.focus();
                newWin.print();
            }

            // post-process DOM elements
            function doSomething3(table) {
                // alert(table[0]);
            }

            function doSomething4(col) {
                //alert(col[0]);
            }

            function doSomething5(menu) {
                //alert(menu[0]);
            }

            function JSON2CSV(objArray) {
                // http://jsfiddle.net/sturtevant/vUnF9/
                var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
                var str = '';
                var line = '';
                var labels = true;
                var quotes = true;

                if (labels) {
                    var head = array[0];
                    if (quotes) {
                        for (var index in array[0]) {
                            var value = index + "";
                            line += '"' + value.replace(/"/g, '""') + '",';
                        }
                    } else {
                        for (var index in array[0]) {
                            line += index + ',';
                        }
                    }

                    line = line.slice(0, -1);
                    str += line + '\r\n';
                }

                for (var i = 0; i < array.length; i++) {
                    var line = '';

                    if (quotes) {
                        for (var index in array[i]) {
                            var value = array[i][index] + "";
                            line += '"' + value.replace(/"/g, '""') + '",';
                        }
                    } else {
                        for (var index in array[i]) {
                            line += array[i][index] + ',';
                        }
                    }

                    line = line.slice(0, -1);
                    str += line + '\r\n';
                }
                return str;
                
            }
                    
            // $("#convert").click(function() {
            //     var json = $.parseJSON($("#json").val());
            //     var csv = JSON2CSV(json);
            //     $("#csv").val(csv);
            // });
                
            // $("#download").click(function() {
            //     var json = $.parseJSON($("#json").val());
            //     var csv = JSON2CSV(json);
            //     window.open("data:text/csv;charset=utf-8," + escape(csv))
            // });

        });
    </script>

</head>
<body>

<div id="pageContainer">

    <header id="pageHeader">
        <h2>Tehtävän tallettajan raportit</h2>
    </header>

    <nav id="pageNav">
        <p>
            <form id="stats" action="" method="POST">
                Valitse ajanjakso: 
                <input id="dateStart" class="datepicker" type="text" name="dateStart" size="10" title="Haettavan ajanjakson alkamisajankohta">
                -
                <input id="dateEnd" class="datepicker" type="text" name="dateEnd" size="10" title="Haettavan ajanjakson päättymisajankohta">
                <input id="removed" type="checkbox" name="removed" value="yes" title="Merkkaa tämä sisällyttääksesi myös poistetut kohteet">
                <input id="taskAct" name="action" value="selectStats" type="hidden">
                <input id="fetchButton" type="submit" value="Hae Raportti">
            </form>
        </p>
        <button style="float:right" type="button" onclick="location.href='./tasker.php';" rel="external">Tallettajaan</button>
    </nav>

    <div id="contentContainer" class="clearfix">

        <section id="pageSection">

            <header class="sectionHeader"><div id="notes"></div></header>
        
            <article class="sectionArticle">

                <div id="tidyContainer"></div>

            </article>

        </section>

    </div>

    <footer id="pageFooter">
        <!-- <button style="margin:5px auto 5px auto;">Export</button> -->
    </footer>

</div>

</body>
</html>
