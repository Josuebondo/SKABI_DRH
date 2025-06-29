<?php
require_once '../config/config.php';
require_once '../model/employes.php';
require_once '../model/presences.php';



?>



<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Présences & Paiements</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="style.css" />
</head>

<body class="bg-gray-100">

  <!-- Navbar -->
  <header class="bg-blue-800 text-white p-4 shadow">
    <h1 class="text-xl font-bold">Société Kabipangi-Fils</h1>
    <p class="text-sm">Gestion du personnel</p>
  </header>

  <!-- Layout principal -->
  <div class="flex flex-col md:flex-row min-h-screen">
    <!-- Sidebar -->
    <aside class="bg-white w-full md:w-64 border-r shadow-md">
      <a href="dashboard.php" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Dashboard</a>
      <nav class="p-4 space-y-4">
        <a href="employesView.php" class=" text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Employés</a>
        <a href="presenceView.php" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Présences</a>
        <a href="#" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Paiements</a>
        <a href="#" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Rapports</a>
      </nav>
    </aside>

    <!-- Contenu principal -->
    <main class="flex-1 p-6">
      <h2 class="text-2xl font-bold mb-4">Résumé du mois</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition">
          <h3 class="text-sm text-gray-500">Présents aujourd’hui</h3>
          <p class="text-2xl font-bold text-green-600">12</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition">
          <h3 class="text-sm text-gray-500">Absents</h3>
          <p class="text-2xl font-bold text-red-600">3</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition">
          <h3 class="text-sm text-gray-500">Salaire total</h3>
          <p class="text-2xl font-bold text-blue-600">4 500 000 FC</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition">
          <h3 class="text-sm text-gray-500">Employés non payés</h3>
          <p class="text-2xl font-bold text-orange-500">2</p>
        </div>
      </div>

      <!-- Historique -->
      <div class="mt-8 bg-white p-6 rounded-xl shadow">
        <h3 class="text-lg font-semibold mb-2">Historique des présences</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left table-auto">
            <thead>
              <tr class="text-sm text-gray-600 border-b">
                <th class="py-2">Nom</th>
                <th>Date</th>
                <th>Arrivée</th>
                <th>Départ</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-b hover:bg-gray-50 transition">
                <td class="py-2">Manya Pierre</td>
                <td>2025-06-28</td>
                <td>08:12</td>
                <td>17:00</td>
                <td class="text-green-600">Présent</td>
              </tr>
              <tr class="border-b hover:bg-gray-50 transition">
                <td class="py-2">Bondo Josué</td>
                <td>2025-06-28</td>
                <td>--</td>
                <td>--</td>
                <td class="text-red-600">Absent</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

</body>

</html>