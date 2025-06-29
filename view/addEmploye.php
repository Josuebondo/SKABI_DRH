<?php
require_once '../config/config.php';
require_once '../model/employes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeData = [
        'nom_complet' => $_POST['nom_complet'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telephone' => $_POST['telephone'] ?? '',
        'poste' => $_POST['poste'] ?? '',
        'salaire' => $_POST['salaire'] ?? ''
    ];
    $employemodel = new employesModel();
    $employemodel->addEmploye(
        $employeData['nom_complet'],
        $employeData['email'],
        $employeData['telephone'],
        $employeData['poste'],
        $employeData['salaire']
    );
    header("Location: employesView.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ajouter un Employé</title>
    <script src="ht