<?php require_once "inc/auth.http.php" ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>Tehtävän tallettaja</title>
  
  <!-- Remote CSS and JS -->
  <link href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  <script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>

  <!-- Local JS -->
  <script type="text/javascript">
        // global variable if set
        var userId = <?= $_SESSION['userId'] ?>;
  </script>
  <script src="js/tasker.js"></script>

</head>
<body>

<!-- Home -->
<div data-role="page" id="home">
  <div id="header" data-theme="" data-role="header">
    <h1>Tehtävän tallettaja</h1>
        <a href="logout.php" rel="external" data-icon="gear" class="ui-btn-right">Lopeta</a>
    </a>
  </div>
  <div data-role="content">
    <div id="collapSet" data-role="collapsible-set" data-content-theme="d">

      <div data-role="collapsible" id="locationMultiSelect">
        <h3>Usean kohteen valinta</h3>

        <form id="taskerMulti" action="" method="POST">

            <div data-role="fieldcontain">
                <label for="siteSelectType">
                    Valitse kohde tyyppi:
                </label>
                <select id="siteSelectType" name="siteSelectType">
                    <option value="">Valitse...</option>
                    <option value="rivitalo">Rivitalo</option>
                    <option value="yksityinen">Yksityinen</option>
                    <option value="firma">Firma</option>
                    <option value="muu">Muu</option> 
                </select>
            </div>

            <div data-role="fieldcontain">
                <fieldset data-role="controlgroup" id="multiCheckboxes">
                    <legend>Valitse kohteet:</legend>
                </fieldset>
            </div>

            <div data-role="fieldcontain">
                <label for="taskSelectType">
                    Työn Valinta:
                </label>
                <select id="taskSelectType" name="taskSelectType">
                    <option value="">Valitse</option>
                    <option value="auraus">Auraus</option>
                    <option value="hiekotus">Hiekotus</option>
                    <option value="lumen siirto">Lumen Siirto</option>
                    <option value="muu">Muu</option>
                </select>
            </div>

            <div data-role="fieldcontain">
                <label for="taskSelectNote">
                    Muistiinpanoja:
                </label>
                <textarea id="taskSelectNote" name="taskSelectNote"></textarea>
            </div>

            <input id="formSelectTasker" name="formName" value="taskerMulti" type="hidden">
            <input id="taskSelectAct" name="action" value="insertMulti" type="hidden">
            <input id="btnSelectSubmit" type="submit" value="Talleta">

        </form>

      </div>

      <div data-role="collapsible" id="locationSelect">
        <h3>Yhden kohteen valinta</h3>

        <form id="tasker" action="" method="POST">
            <div data-role="fieldcontain">
                <label for="siteId">
                    Valitse kohde:
                </label>
                <select id="siteId" name="siteId">
                </select>
                <div id="siteTaskType" style="font-weight:bold;margin-left:24%;padding:4px;display:none;"></div>
            </div>


            <div data-role="fieldcontain">
                <label for="taskType">
                    Työn Valinta:
                </label>
                <select id="taskType" name="taskType">
                    <option value="">Valitse</option>
                    <option value="auraus">Auraus</option>
                    <option value="hiekotus">Hiekotus</option>
                    <option value="lumen siirto">Lumen Siirto</option>
                    <option value="muu">Muu</option>
                </select>
            </div>

            <div data-role="fieldcontain">
                <label for="taskNote">
                    Muistiinpanoja:
                </label>
                <textarea id="taskNote" name="taskNote"></textarea>
            </div>

            <input id="formTasker" name="formName" value="tasker" type="hidden">
            <input id="taskAct" name="action" value="insert" type="hidden">
            <input id="btnSubmit" type="submit" value="Talleta">

        </form>

      </div>

      <div data-role="collapsible" id="newLocation">
        <h3>Luo uusi kohde</h3>

        <form id="editInsertForm" name="editInsertForm" action="" method="POST">
            <div data-role="fieldcontain">
                <label for="siteInsertName">
                    Nimi:
                </label>
                <input name="siteName" id="siteInsertName" placeholder="" value="" type="text">
            </div>
            <div data-role="fieldcontain">
                <label for="siteInsertType">
                    Tyyppi:
                </label>
                <select id="siteInsertType" name="siteType">
                    <option value="">Valitse...</option>
                    <option value="rivitalo">Rivitalo</option>
                    <option value="yksityinen">Yksityinen</option>
                    <option value="firma">Firma</option>
                    <option value="muu">Muu</option> 
                </select>
            </div>
            <div data-role="fieldcontain">
                <label for="siteInsertAddress">
                    Katuosoite:
                </label>
                <input name="siteAddress" id="siteInsertAddress" placeholder="" value="" type="text">
            </div>
            <div data-role="fieldcontain">
                <label for="siteInsertZip">
                    Postinumero:
                </label>
                <input name="siteZip" id="siteInsertZip" placeholder="" value="" type="number">
            </div>
            <div data-role="fieldcontain">
                <label for="siteInsertCity">
                    Kaupunki:
                </label>
                <input name="siteCity" id="siteInsertCity" placeholder="" value="" type="text">
            </div>

            <input id="formInsertEditor" name="formName" value="creator" type="hidden">
            <input id="siteInsertAct" name="action" value="insert" type="hidden">
            <input id="siteInsertSave" type="submit" value="Talleta">
        </form>

      </div>

      <div data-role="collapsible" id="locationEdit">
        <h3>Editoi kohdetta</h3>

        <form id="editForm" name="editForm" action="" method="POST">
            <div data-role="fieldcontain">
                <label for="siteName">
                    Valitse kohde:
                </label>
                <select id="editId" name="id">
                </select>
            </div>
            <div data-role="fieldcontain">
                <label for="siteName">
                    Nimi:
                </label>
                <input name="siteName" id="siteName" placeholder="" value="" type="text">
            </div>
            <div data-role="fieldcontain">
                <label for="siteType">
                    Tyyppi:
                </label>
                <select id="siteType" name="siteType">
                    <option value="">Valitse...</option>
                    <option value="rivitalo">Rivitalo</option>
                    <option value="yksityinen">Yksityinen</option>
                    <option value="firma">Firma</option>
                    <option value="muu">Muu</option>
                </select>
            </div>
            <div data-role="fieldcontain">
                <label for="siteAddress">
                    Katuosoite:
                </label>
                <input name="siteAddress" id="siteAddress" placeholder="" value="" type="text">
            </div>
            <div data-role="fieldcontain">
                <label for="siteZip">
                    Postinumero:
                </label>
                <input name="siteZip" id="siteZip" placeholder="" value="" type="number">
            </div>
            <div data-role="fieldcontain">
                <label for="siteCity">
                    Kaupunki:
                </label>
                <input name="siteCity" id="siteCity" placeholder="" value="" type="text">
            </div>

            <div data-role="fieldcontain">
                <fieldset data-role="controlgroup">
                    <legend>Poista Kohde:</legend>
                    <input id="siteDelete" type="checkbox" name="delete" value="yes" data-mini="true" data-inline="true">
                    <label for="siteDelete">
                        Poista kohde listalta.
                    </label>
                </fieldset>
            </div>

            <input id="formEditor" name="formName" value="editor" type="hidden">
            <input id="siteAct" name="action" value="update" type="hidden">
            <input id="siteSave" type="submit" value="Päivitä">
        </form>

      </div>


      <div data-role="collapsible" id="statsLink">
        <h3>Raportit</h3>
        <p>
            Painamalla nappia, pääset lukemaan raportti taulua. Taulu ei tosin ole vielä optimoitu pieni ruutuisille mobiililaitteille.
        </p>
        <p>
            <button type="button" onclick="location.href='./stats.php';" rel="external">Raportit</button>
        </p>
      </div>
    
    </div>
  </div>
</div>

</body>
</html>