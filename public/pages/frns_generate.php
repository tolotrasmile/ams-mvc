<?php

/*
 * Generate the word report for Circularisations
 * */

require '../../vendor/autoload.php';

use App\Helpers\Debugger;
use App\Reporting\WordReporting;

$result = array('result' => null, 'error' => true);
session_start();

$type = array(40 => 'fournisseur', 41 => 'client', 42 => 'banque');

$fileType = (isset($type[$_GET['type']])) ? $type[$_GET['type']] : 'fournisseur';

if (isset($_GET['name']) && isset($_GET['adresse']) && isset($_GET['idBalAux'])) {

    $now = new DateTime();
    $name = array($_SESSION['idMission'], $_GET['idBalAux']);
    $faker = Faker\Factory::create();

    $dateLimite = new DateTime($faker->date());

    // TODO : Change the date info's to real data
    $options = array(
        'dateLimite' => ucwords(strftime("%d %B %Y", $dateLimite->getTimestamp())),
        'date' => ucwords(strftime("%d %B %Y", $now->getTimestamp())),
        'nom' => $_GET['name'],
        'coordonnees' => $_GET['adresse'],
        'template' => 'template_lettre_fournisseur'
    );

    $result = WordReporting::render($name, $options, $fileType);
}

if ($result && $result['error'] !== true) {

    // Insert data into database
    $data = array(
        'fileName' => str_replace('\\', DIRECTORY_SEPARATOR,  $result['result']),
        'fileIdMission' => $_SESSION['idMission'],
        'fileDestName' => $_GET['name'],
        'fileDestCoord' => $_GET['adresse'],
        'fileCategory' => $fileType,
        'fileTimeCreation' => $now->format('Y-m-d'),
        'bal_aux_id' => $_GET['idBalAux']
    );

    $model = new \App\Model\CircularisationModel();

    $result['fileType'] = $fileType;
    if ($model->exists($_GET['idBalAux'])) {
        $result['error'] = $model->update($data, 'bal_aux_id=' . $_GET['idBalAux']);
        $result['action'] = 'UPDATE';
    } else {
        $result['error'] = $model->insert($data);
        $result['action'] = 'INSERTION';
    }
    header($result['result'], false, 200);
} else {
    header('Error for generating file', false, 404);
}

Debugger::json($result);