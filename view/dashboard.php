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
  <header class="bg-blue-800 text-white p-4 shadow flex items-center justify-between fixed w-full left-0 top-0 z-50 h-16">
    <div class="flex items-center gap-3">
      <img src="../public/images/logo.png" alt="Logo Société" class="h-10 w-auto rounded bg-white p-1 shadow" style="max-width:48px;">
      <div>
        <h1 class="text-xl font-bold">Société Kabipangi-Fils</h1>
        <p class="text-sm">Gestion du personnel</p>
      </div>
    </div>
    <!-- Burger button for mobile -->
    <button id="openSidebarBtn" class="md:hidden text-white text-3xl focus:outline-none">
      <i class="fas fa-bars"></i>
    </button>
  </header>

  <!-- Layout principal -->
  <div class="flex flex-col md:flex-row min-h-screen pt-16">
    <!-- Sidebar responsive unique -->
    <aside id="sidebar" class="bg-white w-half h-half md:w-64 border-r shadow-md p-4 mb-4 md:mb-0 fixed md:static top-0 left-0 h-screen md:h-auto z-50 transition-transform duration-300 -translate-x-full md:translate-x-0 overflow-y-auto">
      <button id="closeSidebarBtn" class="md:hidden absolute top-4 right-4 text-gray-500 text-2xl">&times;</button>
      <nav class="space-y-4 mt-8 md:mt-0">
        <a href="dashboard.php" class="block px-2 py-1 rounded transition font-semibold text-gray-700 hover:bg-blue-50 focus:outline-none focus:bg-blue-100 bg-blue-100">Dashboard</a>
        <a href="employesView.php" class="block px-2 py-1 rounded transition text-gray-700 hover:bg-blue-50 focus:outline-none focus:bg-blue-100 <?php echo basename($_SERVER['PHP_SELF']) === 'employesView.php' ? 'bg-blue-100 font-semibold' : ''; ?>">Employés</a>
        <a href="presenceView.php" class="block px-2 py-1 rounded transition text-gray-700 hover:bg-blue-50 focus:outline-none focus:bg-blue-100 <?php echo basename($_SERVER['PHP_SELF']) === 'presenceView.php' ? 'bg-blue-100 font-semibold' : ''; ?>">Présences</a>
        <a href="payementView.php" class="block px-2 py-1 rounded transition text-gray-700 hover:bg-blue-50 focus:outline-none focus:bg-blue-100 <?php echo basename($_SERVER['PHP_SELF']) === 'payementView.php' ? 'bg-blue-100 font-semibold' : ''; ?>">Paiements</a>
        <a href="#" class="block px-2 py-1 rounded transition text-gray-700 hover:bg-blue-50 focus:outline-none focus:bg-blue-100">Rapports</a>
      </nav>
    </aside>
    <!-- Bouton burger pour mobile -->
    <button id="openSidebarBtn" class="md:hidden fixed top-4 left-4 z-50 bg-blue-700 text-white p-2 rounded shadow-lg focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <!-- Contenu principal -->
    <main class="flex-1 p-6">
      <h2 class="text-2xl font-bold mb-4">Résumé du mois</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
          <h3 class="text-sm text-gray-500">Présents aujourd’hui</h3>
          <p class="text-2xl font-bold text-green-600">12</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
          <h3 class="text-sm text-gray-500">Absents</h3>
          <p class="text-2xl font-bold text-red-600">3</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
          <h3 class="text-sm text-gray-500">Salaire total</h3>
          <p class="text-2xl font-bold text-blue-600">4 500 000 FC</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
          <h3 class="text-sm text-gray-500">Employés non payés</h3>
          <p class="text-2xl font-bold text-orange-500">2</p>
        </div>
      </div>

      <!-- Historique -->
      <div class="mt-8 bg-white p-6 rounded-xl shadow">
        <h3 class="text-lg font-semibold mb-2">Historique des présences</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left table-auto text-xs sm:text-sm">
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
                <td class="py-2 whitespace-nowrap">Manya Pierre</td>
                <td class="whitespace-nowrap">2025-06-28</td>
                <td class="whitespace-nowrap">08:12</td>
                <td class="whitespace-nowrap">17:00</td>
                <td class="text-green-600 font-semibold">Présent</td>
              </tr>
              <tr class="border-b hover:bg-gray-50 transition">
                <td class="py-2 whitespace-nowrap">Bondo Josué</td>
                <td class="whitespace-nowrap">2025-06-28</td>
                <td class="whitespace-nowrap">--</td>
                <td class="whitespace-nowrap">--</td>
                <td class="text-red-600 font-semibold">Absent</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Script pour la sidebar responsive -->
  <script>
    const sidebar = document.getElementById('sidebar');
    const openSidebarBtn = document.getElementById('openSidebarBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    if (openSidebarBtn && sidebar) {
      openSidebarBtn.onclick = function() {
        sidebar.classList.remove('-translate-x-full');
      };
    }
    if (closeSidebarBtn && sidebar) {
      closeSidebarBtn.onclick = function() {
        sidebar.classList.add('-translate-x-full');
      };
    }
    document.addEventListener('click', function(e) {
      if (window.innerWidth < 768 && sidebar && !sidebar.contains(e.target) && !openSidebarBtn.contains(e.target)) {
        sidebar.classList.add('-translate-x-full');
      }
    });
  </script>
</body>

</html>