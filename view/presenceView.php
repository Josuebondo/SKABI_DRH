<?php
session_start();
require_once '../config/config.php';
require_once '../model/presences.php';

// --- GESTION DU POINTAGE (doit être AVANT tout HTML) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pointer_id'])) {
    $presence_id = $_POST['pointer_id'];
    $stmt = $pdo->prepare("UPDATE presences SET statut = 'Présent', heure_arrivee = ? WHERE id = ?");
    $stmt->execute([date('H:i'), $presence_id]);
    header("Location: presenceView.php");
}



// --- Récupération des présences ---
$presencesModel = new presencesModel();
$presences = $presencesModel->getTodayPresences();
if (!is_array($presences)) {
    $presences = [];
}
$previousPresences = $presencesModel->getPreviousPresences();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_presence'])) {
    if (
        isset($_POST['employes_ids']) && isset($_POST['statut'])
    ) {
        $ids = $_POST['employes_ids'];
        $statut = $_POST['statut'];
        $heure = ($statut === 'Présent') ? ($_POST['heure'] ?? date('H:i')) : null;
        $date = date('Y-m-d');

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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Présences du jour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css" />
</head>

<body class="bg-gray-100">

    <header class="bg-blue-800 text-white p-4 shadow mb-8">
        <h1 class="text-xl font-bold text-center">Présences du <?php echo date('d/m/Y'); ?></h1>
    </header>
    <div class="flex flex-col md:flex-row min-h-screen">
        <aside class="bg-white w-full md:w-64 border-r shadow-md p-4">
            <nav class="space-y-4">
                <a href="dashboard.php" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Dashboard</a>
                <a href="employesView.php" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Employés</a>
                <a href="presenceView.php" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Présences</a>
                <a href="#" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Paiements</a>
                <a href="#" class="block text-gray-700 hover:bg-blue-50 rounded px-2 py-1 transition">Rapports</a>
            </nav>
        </aside>
        <main class="flex-1 w-full max-w-5xl mx-auto bg-white p-2 sm:p-6 rounded-xl shadow">


            <button
                id="openPresenceModalBtn"
                class="mb-4 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition font-semibold">
                Ajouter une présence
            </button>

            <!-- Modal d'ajout de présence -->
            <div
                id="presenceModal"
                class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md relative">
                    <button
                        id="closePresenceModalBtn"
                        type="button"
                        class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
                    <h2 class="text-xl font-bold mb-4 text-center">Pointer des employés</h2>


                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="block mb-1 font-semibold">Sélectionner les employés :</label>
                            <select name="employes_ids[]" multiple required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <?php
                                // Récupère tous les employés
                                $allEmployes = $presencesModel->getAllEmployes(); // À créer dans le modèle si besoin
                                foreach ($allEmployes as $emp) {
                                    echo '<option value="' . $emp['id'] . '">' . htmlspecialchars($emp['nom_complet']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <input type="hidden" name="date" value=" <?php echo date('d/m/Y'); ?>">
                            <input type="datetime" name="heure">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1 font-semibold">Heure d'arrivée :</label>
                            <input type="time" name="heure" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        </div>
                        <div class="mb-4">
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4 mb-4 sm:mb-8">
                <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition">
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
                <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition">
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
                <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition">
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
                <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition">
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

            <h2 class="text-base sm:text-lg font-semibold mb-2 sm:mb-4">Liste des présences</h2>
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

            <h2 class="text-base sm:text-lg font-semibold mt-8 mb-2 sm:mb-4">Historique mensuel par employé</h2>
            <?php
            $mois = date('m');
            $annee = date('Y');
            foreach ($presences as $presence) {
                $employe_id = $presence['employee_id'] ?? null;
                if (!$employe_id) continue;
                $historique = $presencesModel->getMonthPresences($employe_id, $mois, $annee);
            ?>
                <div class="mb-6">
                    <h3 class="font-bold text-blue-700 mb-1 sm:mb-2 text-sm sm:text-base"><?php echo htmlspecialchars($presence['nom_complet']); ?></h3>
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

            <h2 class="text-base sm:text-lg font-semibold mb-2 sm:mb-4">Historique des présences précédentes</h2>
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

                <!-- Bouton pour ouvrir le modal d'ajout de présence -->


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
            </div>
        </main>
    </div>
    <footer class="bg-white text-gray-600 p-4 text-center mt-8">
        <p>&copy; <?php echo date('Y'); ?> Mon Entreprise. Tous droits réservés.</p>
    </footer>
</body>

</html>