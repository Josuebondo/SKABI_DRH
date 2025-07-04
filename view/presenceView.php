<?php
// Démarrage de la session et inclusion des dépendances
session_start();
require_once '../config/config.php';
require_once '../model/presences.php';

// --- GESTION DU POINTAGE (doit être AVANT tout HTML) ---
// Si un formulaire de pointage est soumis, mettre à jour la présence comme "Présent" avec l'heure d'arrivée actuelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pointer_id'])) {
    $presence_id = $_POST['pointer_id'];
    $stmt = $pdo->prepare("UPDATE presences SET statut = 'Présent', heure_arrivee = ? WHERE id = ?");
    $stmt->execute([date('H:i'), $presence_id]);
    header("Location: presenceView.php");
}

// --- Récupération des présences du jour et précédentes ---
$presencesModel = new presencesModel();
$presences = $presencesModel->getTodayPresences();
if (!is_array($presences)) {
    $presences = [];
}
$previousPresences = $presencesModel->getPreviousPresences();

// --- Ajout de présence via le formulaire modal ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_presence'])) {
    if (
        isset($_POST['employes_ids']) && isset($_POST['statut'])
    ) {
        $ids = $_POST['employes_ids'];
        $statut = $_POST['statut'];
        $heure = ($statut === 'Présent') ? ($_POST['heure'] ?? date('H:i')) : null;
        $date = date('Y-m-d');

        // Ajoute la présence pour chaque employé sélectionné
        foreach ($ids as $em_id) {
            $presencesModel->addPresence($em_id, $date, $heure, $statut);
        }
        $_SESSION['success_message'] = "Présence(s) ajoutée(s) avec succès.";
        header("Location: presenceView.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Erreur lors de l'ajout de la présence.";
    }
}

// --- Ajout de paiement via le formulaire modal ---
// (Traitement désactivé car la méthode addPayment n'existe pas encore)
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_paiement'])) {
//     if (
//         isset($_POST['paiement_employes_ids']) && isset($_POST['montant']) && isset($_POST['date_paiement']) && isset($_POST['statut_paiement'])
//     ) {
//         $ids = $_POST['paiement_employes_ids'];
//         $montant = $_POST['montant'];
//         $date_paiement = $_POST['date_paiement'];
//         $statut_paiement = $_POST['statut_paiement'];
//
//         // Ajoute le paiement pour chaque employé sélectionné
//         foreach ($ids as $em_id) {
//             $presencesModel->addPayment($em_id, $montant, $date_paiement, $statut_paiement);
//         }
//         $_SESSION['success_message'] = "Paiement(s) ajouté(s) avec succès.";
//         header("Location: presenceView.php");
//         exit();
//     } else {
//         $_SESSION['error_message'] = "Erreur lors de l'ajout du paiement.";
//     }
// }
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Présences du jour</title>
    <!-- Inclusion de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css" />
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- En-tête harmonisé -->
    <header class="bg-blue-800 text-white p-4 shadow flex items-center justify-between fixed w-full left-0 top-0 z-50 h-16">
        <div class="flex items-center gap-3">
            <img src="../public/images/logo.png" alt="Logo Société" class="h-10 w-auto rounded bg-white p-1 shadow" style="max-width:48px;">
            <div>
                <h1 class="text-xl font-bold">Société Kabipangi-Fils</h1>
                <p class="text-sm">Gestion du personnel</p>
                <p class="text-xs mt-1">Présences du <?php echo date('d/m/Y'); ?></p>
            </div>
        </div>
        <!-- Burger button for mobile -->
        <button id="openSidebarBtn" class="md:hidden text-white text-3xl focus:outline-none">
            <i class="fas fa-bars"></i>
        </button>
    </header>
    <div class="flex flex-col md:flex-row min-h-screen pt-16">
        <!-- Barre latérale de navigation responsive -->
        <aside id="sidebar" class="bg-white w-full md:w-64 border-r shadow-md p-4 fixed md:fixed md:top-16 md:left-0 h-screen md:h-[calc(100vh-64px)] z-40 transition-transform duration-300 -translate-x-full md:translate-x-0 overflow-y-auto md:mb-0">
            <button id="closeSidebarBtn" class="md:hidden absolute top-4 right-4 text-gray-500 text-2xl">&times;</button>
            <nav class="space-y-4 mt-8 md:mt-0">
                <a href="dashboard.php" class="block px-2 py-1 rounded transition font-semibold text-gray-700 hover:bg-blue-50 focus:outline-none focus:bg-blue-100 <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-blue-100 font-semibold' : ''; ?>">Dashboard</a>
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
        <main class="flex-1 p-6 bg-gray-100 min-h-screen md:ml-64">
            <div class="max-w-6xl mx-auto">
                <!-- Bouton pour ouvrir le modal d'ajout de présence et de paiement -->
                <div class="flex flex-col sm:flex-row gap-2 mb-4">
                    <button id="openPresenceModalBtn" class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition font-semibold w-full sm:w-auto">
                        Ajouter une présence
                    </button>
                    <button id="openDetteModalBtn" class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 transition font-semibold w-full sm:w-auto">
                        Ajouter un Dette
                    </button>
                </div>
                <!-- Modal d'ajout de présence -->
                <div id="presenceModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md relative mx-2">
                        <button id="closePresenceModalBtn" type="button" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
                        <h2 class="text-xl font-bold mb-4 text-center">Pointer des employés</h2>
                        <form method="POST" action="" class="space-y-4">
                            <div>
                                <label class="block mb-1 font-semibold">Sélectionner les employés :</label>
                                <select name="employes_ids[]" multiple required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <?php
                                    $allEmployes = $presencesModel->getAllEmployes();
                                    foreach ($allEmployes as $emp) {
                                        echo '<option value="' . $emp['id'] . '">' . htmlspecialchars($emp['nom_complet']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Heure d'arrivée :</label>
                                <input type="time" name="heure" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Statut :</label>
                                <select name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="Présent">Présent</option>
                                    <option value="Absent">Absent</option>
                                    <option value="malade">malade</option>
                                </select>
                            </div>
                            <button type="submit" name="ajouter_presence" class="w-full px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition font-semibold">
                                Valider
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Modal d'ajout de paiement -->
                <div id="detteModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md relative mx-2">
                        <button id="closeDetteModalBtn" type="button" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
                        <h2 class="text-xl font-bold mb-4 text-center">Ajouter un dette</h2>
                        <form method="POST" action="" class="space-y-4">
                            <div>
                                <label class="block mb-1 font-semibold">Sélectionner les employés :</label>
                                <select name="dette_employes_ids[]" multiple required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <?php
                                    $allEmployes = $presencesModel->getAllEmployes();
                                    foreach ($allEmployes as $emp) {
                                        echo '<option value="' . $emp['id'] . '">' . htmlspecialchars($emp['nom_complet']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Montant :</label>
                                <input type="number" name="montant" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Date :</label>
                                <input type="date" name="date_dette" class="w-full px-3 py-2 border border-gray-300 rounded-lg" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Statut :</label>
                                <select name="statut_dette" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="Payé">Payé</option>
                                    <option value="Non payé">Non payé</option>
                                </select>
                            </div>
                            <button type="submit" name="ajouter_dette" class="w-full px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 transition font-semibold">
                                Valider
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statistiques de présence du jour -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
                        <h3 class="text-sm text-gray-500">Présents aujourd’hui</h3>
                        <p class="text-2xl font-bold text-green-600">
                            <?php
                            $presentCount = 0;
                            foreach ($presences as $presence) {
                                if (strtolower($presence['statut']) === 'présent') $presentCount++;
                            }
                            echo $presentCount;
                            ?>
                        </p>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
                        <h3 class="text-sm text-gray-500">Absents</h3>
                        <p class="text-2xl font-bold text-red-600">
                            <?php
                            $absentCount = 0;
                            foreach ($presences as $presence) {
                                if (strtolower($presence['statut']) === 'absent') $absentCount++;
                            }
                            echo $absentCount;
                            ?>
                        </p>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
                        <h3 class="text-sm text-gray-500">tout les employés</h3>
                        <p class="text-2xl font-bold text-blue-600">
                            <?php
                            $totalSalaire = 0;
                            foreach ($presences as $presence) {
                                if (isset($presence['salaire']) && strtolower($presence['statut']) === 'présent') {
                                    $totalSalaire += $presence['salaire'];
                                }
                            }
                            echo number_format($totalSalaire, 0, ',', ' ') . ' FC';
                            ?>
                        </p>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
                        <h3 class="text-sm text-gray-500">Employés non payés</h3>
                        <p class="text-2xl font-bold text-orange-500">
                            <?php
                            $nonPayes = 0;
                            foreach ($presences as $presence) {
                                if (isset($presence['statut_paiement']) && $presence['statut_paiement'] === 'Non payé') {
                                    $nonPayes++;
                                }
                            }
                            echo $nonPayes;
                            ?>
                        </p>
                    </div>
                </div>

                <!-- Tableau des présences du jour -->
                <div class="bg-white p-6 rounded-xl shadow mb-8">
                    <h2 class="text-lg font-bold mb-4">Liste des présences</h2>
                    <div class="overflow-x-auto rounded-lg border border-gray-100 mb-6">
                        <table class="min-w-[600px] w-full text-left table-auto text-xs sm:text-sm">
                            <thead>
                                <tr class="text-sm text-gray-600 border-b">
                                    <th class="py-2">Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Poste</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($presences): ?>
                                    <?php foreach ($presences as $presence): ?>
                                        <tr class="border-b hover:bg-gray-50 transition">
                                            <td class="py-2"><?php echo htmlspecialchars($presence['nom_complet']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['email']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['telephone']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['poste']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['heure_arrivee']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['heure_depart']); ?></td>
                                            <td class="<?php echo strtolower($presence['statut']) === 'présent' ? 'text-green-600' : 'text-red-600'; ?>">
                                                <?php echo htmlspecialchars($presence['statut']); ?>
                                            </td>
                                            <td>
                                                <?php if (strtolower($presence['statut']) !== 'présent'): ?>
                                                    <!-- Formulaire pour pointer un employé absent -->
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="pointer_id" value="<?php echo isset($presence['id']) ? htmlspecialchars($presence['id']) : (isset($presence['presence_id']) ? htmlspecialchars($presence['presence_id']) : ''); ?>">
                                                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                                            Pointer
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-green-600 font-semibold">Pointé</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-gray-400">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Aucune présence enregistrée aujourd'hui.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Historique mensuel par employé -->
                <div class="bg-white p-6 rounded-xl shadow mb-8">
                    <h2 class="text-lg font-bold mb-4">Historique mensuel par employé</h2>
                    <!-- Formulaire de sélection du mois, de l'année et de l'employé -->
                    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-2 mb-4 items-center bg-gray-50 p-3 rounded-lg shadow-sm">
                        <div class="flex flex-col w-full sm:w-auto">
                            <label for="mois" class="font-semibold mb-1">Mois :</label>
                            <select name="mois" id="mois" class="border rounded px-2 py-1 w-full min-w-[120px]">
                                <?php
                                $moisActuel = isset($_GET['mois']) ? $_GET['mois'] : date('m');
                                for ($m = 1; $m <= 12; $m++) {
                                    $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                                    $selected = ($val == $moisActuel) ? 'selected' : '';
                                    echo "<option value='$val' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="flex flex-col w-full sm:w-auto">
                            <label for="annee" class="font-semibold mb-1">Année :</label>
                            <select name="annee" id="annee" class="border rounded px-2 py-1 w-full min-w-[100px]">
                                <?php
                                $anneeActuelle = isset($_GET['annee']) ? $_GET['annee'] : date('Y');
                                for ($a = date('Y') - 3; $a <= date('Y'); $a++) {
                                    $selected = ($a == $anneeActuelle) ? 'selected' : '';
                                    echo "<option value='$a' $selected>$a</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="flex flex-col w-full sm:w-auto">
                            <label for="employe_id" class="font-semibold mb-1">Employé :</label>
                            <select name="employe_id" id="employe_id" class="border rounded px-2 py-1 w-full min-w-[150px]">
                                <option value="">Tous</option>
                                <?php
                                $allEmployes = $presencesModel->getAllEmployes();
                                $employeFiltre = isset($_GET['employe_id']) ? $_GET['employe_id'] : '';
                                foreach ($allEmployes as $emp) {
                                    $selected = ($employeFiltre == $emp['id']) ? 'selected' : '';
                                    echo '<option value="' . $emp['id'] . '" ' . $selected . '>' . htmlspecialchars($emp['nom_complet']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="flex w-full sm:w-auto items-end">
                            <button type="submit" class="w-full sm:w-auto px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-semibold mt-2 sm:mt-0">Afficher</button>
                        </div>
                    </form>
                    <?php
                    $mois = isset($_GET['mois']) ? $_GET['mois'] : date('m');
                    $annee = isset($_GET['annee']) ? $_GET['annee'] : date('Y');
                    $employeFiltre = isset($_GET['employe_id']) ? $_GET['employe_id'] : '';
                    $allEmployes = $presencesModel->getAllEmployes();
                    foreach ($allEmployes as $emp) {
                        $employe_id = $emp['id'];
                        if ($employeFiltre && $employeFiltre != $employe_id) continue;
                        $historique = $presencesModel->getMonthPresences($employe_id, $mois, $annee);
                    ?>
                        <div class="mb-6">
                            <h3 class="font-bold text-blue-700 mb-1 sm:mb-2 text-sm sm:text-base"><?php echo htmlspecialchars($emp['nom_complet']); ?></h3>
                            <div class="overflow-x-auto rounded-lg border border-gray-100">
                                <table class="min-w-[400px] w-full text-left table-auto text-xs sm:text-sm mb-2">
                                    <thead>
                                        <tr class="text-sm text-gray-600 border-b">
                                            <th class="py-2">Date</th>
                                            <th>Arrivée</th>
                                            <th>Départ</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($historique): ?>
                                            <?php foreach ($historique as $jour): ?>
                                                <tr class="border-b hover:bg-gray-50 transition">
                                                    <td class="py-2"><?php echo htmlspecialchars($jour['date_presence']); ?></td>
                                                    <td><?php echo htmlspecialchars($jour['heure_arrivee']); ?></td>
                                                    <td><?php echo htmlspecialchars($jour['heure_depart']); ?></td>
                                                    <td class="<?php echo strtolower($jour['statut']) === 'présent' ? 'text-green-600' : 'text-red-600'; ?>">
                                                        <?php echo htmlspecialchars($jour['statut']); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-2 text-gray-500">Aucune donnée pour ce mois.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Historique des présences précédentes -->
                <div class="bg-white p-6 rounded-xl shadow mb-8">
                    <h2 class="text-lg font-bold mb-4">Historique des présences précédentes</h2>
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-[600px] w-full text-left table-auto text-xs sm:text-sm">
                            <thead>
                                <tr class="text-sm text-gray-600 border-b">
                                    <th class="py-2">Date</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Poste</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($previousPresences): ?>
                                    <?php foreach ($previousPresences as $presence): ?>
                                        <tr class="border-b hover:bg-gray-50 transition">
                                            <td class="py-2"><?php echo htmlspecialchars($presence['date_presence']); ?></td>
                                            <td class="py-2"><?php echo htmlspecialchars($presence['nom_complet']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['email']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['telephone']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['poste']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['heure_arrivee']); ?></td>
                                            <td><?php echo htmlspecialchars($presence['heure_depart']); ?></td>
                                            <td class="<?php echo strtolower($presence['statut']) === 'présent' ? 'text-green-600' : 'text-red-600'; ?>">
                                                <?php echo htmlspecialchars($presence['statut']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-gray-400">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Aucune présence précédente enregistrée.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Pied de page -->
    <footer class="bg-white text-gray-600 p-4 text-center mt-8">
        <p>&copy; <?php echo date('Y'); ?> Mon Entreprise. Tous droits réservés.</p>
    </footer>

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
        // Fermer la sidebar si on clique en dehors sur mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768 && sidebar && !sidebar.contains(e.target) && !openSidebarBtn.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
    <!-- Script pour le modal d'ajout de présence -->
    <script>
        document.getElementById('openPresenceModalBtn').onclick = function() {
            document.getElementById('presenceModal').classList.remove('hidden');
        };
        document.getElementById('closePresenceModalBtn').onclick = function() {
            document.getElementById('presenceModal').classList.add('hidden');
        };
        document.getElementById('presenceModal').onclick = function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        };
    </script>
    <!-- Script pour le modal d'ajout de paiement -->
    <script>
        document.getElementById('openPaiementModalBtn').onclick = function() {
            document.getElementById('paiementModal').classList.remove('hidden');
        };
        document.getElementById('closePaiementModalBtn').onclick = function() {
            document.getElementById('paiementModal').classList.add('hidden');
        };
        document.getElementById('paiementModal').onclick = function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        };
    </script>
</body>

</html>