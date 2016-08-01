<?php
//
$environment = 'DEBUG';

setlocale(LC_ALL, 'fr_FR');

include 'app/functions.php';

// Check the environment for error displays
if ($environment === 'DEBUG') {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Check mission session
    if (!isset($_SESSION['idMission'])) {
        $_SESSION['idMission'] = 53;
        $_SESSION['id'] = 1;
    }
}


session_start();

?>

<?php require 'vendor/autoload.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="public/css/normalize.css">
    <link rel="stylesheet" href="public/css/skeleton.css">
    <link rel="stylesheet" href="public/css/main.css">
    <title>Traitement</title>
</head>
<body>

<?php if (isset($_SESSION['idMission']) && isset($_SESSION['id'])): ?>
    <form class="circularisation-content" method="post" action="public/pages/frns_circularisation.php">
        <div class="section">
            <div class="box-title">
                FOURNISSEURS : Comptes 40
            </div>
            <div class="box-subtitle">
                Sélectionner fournisseurs à circulariser
            </div>
        </div>
        <div class="section">
            <div class="box-container">
                <div class="box-row">
                    <?php $controller = new \App\Controllers\CircularisationController();
                    echo $controller->index($_SESSION['idMission']); ?>
                </div>
            </div>
        </div>

        <footer>
            <div class="box">
                <div class="box-content">
                    <input id="frns-back" class="button button-back" type="button" value="Retour">
                    <input id="frns-save" class="button button-primary control" type="button" value="Circulariser">
                </div>
            </div>
        </footer>
    </form>

    <script type="application/javascript" src="public/js/ajax.js"></script>
    <script type="application/javascript">
        // Circularisation
        (function (document,window) {
            document.querySelector('#frns-save').addEventListener('click',function () {
                var selected = [];

                var $inputs = document.querySelectorAll('input[type="checkbox"]:checked');
                $inputs.forEach(function (element) {
                    var row = element.parentNode.parentNode;
                    selected.push(row.getAttribute('id'));
                });

                if (selected.length > 0) {
                    window.location.href = 'public/pages/frns_circularisation.php?data='
                        + encodeURIComponent(JSON.stringify(selected));
                }
                else {
                    alert('Vous devriez sélectionner au moins un element à circulariser');
                }
            });

            document.querySelector('#frns-back').addEventListener('click',function () {

                console.log(window.history);

                if (window.history) {
                    window.history.back();
                }
            });
        })(document,window);
    </script>
<?php else: ?>
    <div class="section">
        <div class="box-container">
            <div class="box-row">
                <h4>Veuillez sélectionner une mission.</h4>
            </div>
        </div>
    </div>
<?php endif; ?>

</body>
</html>