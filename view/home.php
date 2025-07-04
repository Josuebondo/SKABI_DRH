<?php
require_once '../config/config.php';
require_once '../model/employes.php';
require_once '../model/presences.php';

$employemodel= $this->employeModel;

$employes = $employemodel->getAllEmployes($pdo);
$presences = $employemodel->getTodayPresences($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Gestion du personnel</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<header class="bg-blue-800 text-white p-4">
  <h1 class="text-xl font-bold">Dashboard RH</h1>
</header>

<div class="p-6">
  <h2 class="text-2xl font-bold mb-4">Présences aujourd'hui</h2>
  <table class="w-full bg-white rounded shadow table-auto">
    <thead>
      <tr class="text-left bg-gray-200">
        <th class="p-2">Nom</th>
        <th>Date</th>
        <th>Arrivée</th>
        <th>Départ</th>
        <th>Statut</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($presences as $p): ?>
      <tr class="border-b">
        <td class="p-2"><?= htmlspecialchars($p['nom']) ?> <?= htmlspecialchars($p['prenom']) ?></td>
        <td><?= $p['date_presence'] ?></td>
        <td><?= $p['heure_arrivee'] ?? '--' ?></td>
        <td><?= $p['heure_depart'] ?? '--' ?></td>
        <td><?= ucfirst($p['statut']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
