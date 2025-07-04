<?php
require_once '../config/config.php';
require_once '../model/payement.php';
require_once '../model/employes.php';
require_once '../controler/payementControler.php';

$paiementModel = new PayementModel($pdo);
$employeModel = new EmployesModel($pdo);
$payementControler = new payementControler();

$paiements = $paiementModel->getAllPaiements();
$dettes = $paiementModel->getAllDettesNonPayees();
$employes = $employeModel->getAllEmployes();

$message = '';

// Ajout d’un paiement
if (isset($_POST['add_payment'])) {
    if (empty($_POST['employe_id']) || empty($_POST['montant']) || empty($_POST['date'])) {
        $message = "Tous les champs sont requis.";
    } else {
        $employee_id = $_POST['employe_id'];
        $montant = $_POST['montant'];
        $date = $_POST['date'];
        $paiementModel->addPayment($employee_id, date('F', strtotime($date)), $montant, $date);
        $message = "Paiement enregistré.";
        header("Location: payementView.php");
        exit;
    }
}

// Ajout d’une dette
if (isset($_POST['add_dette'])) {
    $paiementModel->addDette($_POST['employe_id'], $_POST['montant'], $_POST['date']);
    $message = "Dette enregistrée.";
    header("Location: payementView.php");
    exit;
}

// Payer une dette
if (isset($_POST['pay_dette'])) {
    $dette_id = $_POST['dette_id'];
    $montant = $_POST['montant'];
    $dette = $_POST['dette'];
    $date = $_POST['date'];

    $payementControler->paydette($dette_id, $montant, $date, $dette);


    header("Location: payementView.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Paiements & Dettes - Kabipangi-Fils</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <header class="bg-blue-800 text-white p-4 shadow flex items-center justify-between fixed w-full left-0 top-0 z-50 h-16">
        <div class="flex items-center gap-3">
            <img src="../public/images/logo.png" alt="Logo" class="h-10 w-auto rounded bg-white p-1 shadow" />
            <div>
                <h1 class="text-xl font-bold">Société Kabipangi-Fils</h1>
                <p class="text-sm">Gestion du personnel</p>
            </div>
        </div>
        <button id="openSidebarBtn" class="md:hidden text-white text-3xl focus:outline-none">
            &#9776;
        </button>
    </header>

    <div class="flex flex-col md:flex-row min-h-screen pt-16">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-white w-full md:w-64 border-r shadow-md p-4 fixed md:fixed md:top-16 md:left-0 h-screen md:h-[calc(100vh-64px)] z-40 transition-transform duration-300 -translate-x-full md:translate-x-0 overflow-y-auto md:mb-0">
            <button id="closeSidebarBtn" class="md:hidden absolute top-4 right-4 text-gray-500 text-2xl">&times;</button>
            <nav class="space-y-4 mt-8">
                <a href="dashboard.php" class="block px-2 py-1 rounded text-gray-700 hover:bg-blue-50">Dashboard</a>
                <a href="employesView.php" class="block px-2 py-1 rounded text-gray-700 hover:bg-blue-50">Employés</a>
                <a href="presenceView.php" class="block px-2 py-1 rounded text-gray-700 hover:bg-blue-50">Présences</a>
                <a href="payementView.php" class="block px-2 py-1 rounded bg-blue-100 font-semibold text-gray-700">Paiements</a>
                <a href="../rapport.php" class="block px-2 py-1 rounded text-gray-700 hover:bg-blue-50">Rapports</a>
                <a href="#" class="block px-2 py-1 rounded text-gray-700 hover:bg-blue-50">Paramètres</a>
            </nav>
            <a href="#" class="block px-2 py-1 rounded text-gray-700 bottom-0 hover:bg-blue-50">Déconnexion</a>
        </aside>

        <!-- Contenu -->
        <main class="flex-1 p-6 bg-gray-100 min-h-screen md:ml-64">
            <h2 class="text-2xl font-bold mb-4">Paiements & Dettes Employés</h2>

            <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-wrap gap-4 mb-6">
                <button onclick="openModal('modalAddPayment')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ajouter Paiement</button>
                <button onclick="openModal('modalAddDette')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Enregistrer Dette</button>
            </div>

            <!-- Statistiques de présence du jour -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
                    <h3 class="text-sm text-gray-500">Présents aujourd’hui</h3>
                    <p class="text-2xl font-bold text-green-600">
                        <!-- <?php
                                // $presentCount = 0;
                                // foreach ($presences as $presence) {
                                //     if (strtolower($presence['statut']) === 'présent') $presentCount++;
                                // }
                                // echo $presentCount;
                                ?> -->
                    </p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
                    <h3 class="text-sm text-gray-500">Absents</h3>
                    <p class="text-2xl font-bold text-red-600">
                        <?php
                        // $absentCount = 0;
                        // foreach ($presences as $presence) {
                        //     if (strtolower($presence['statut']) === 'absent') $absentCount++;
                        // }
                        // echo $absentCount;
                        ?>
                    </p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
                    <h3 class="text-sm text-gray-500">tout les employés</h3>
                    <p class="text-2xl font-bold text-blue-600">
                        <?php
                        // $totalSalaire = 0;
                        // foreach ($presences as $presence) {
                        //     if (isset($presence['salaire']) && strtolower($presence['statut']) === 'présent') {
                        //         $totalSalaire += $presence['salaire'];
                        //     }
                        // }
                        // echo number_format($totalSalaire, 0, ',', ' ') . ' FC';
                        ?>
                    </p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow hover:scale-105 transition flex flex-col items-center">
                    <h3 class="text-sm text-gray-500">Employés non payés</h3>
                    <p class="text-2xl font-bold text-orange-500">
                        <?php
                        // $nonPayes = 0;
                        // foreach ($presences as $presence) {
                        //     if (isset($presence['statut_paiement']) && $presence['statut_paiement'] === 'Non payé') {
                        //         $nonPayes++;
                        //     }
                        // }
                        // echo $nonPayes;
                        ?>
                    </p>
                </div>
            </div>

            <!-- Historique des paiements -->
            <div class="bg-white p-6 rounded-xl shadow mb-8">
                <h2 class="text-lg font-bold mb-4">Historique des Paiements</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-100 mb-6">
                    <table class="min-w-[600px] w-full text-left table-auto text-xs ms:text-sm">
                        <thead>
                            <tr class="text-sm text-gray-600 border-b">
                                <th class="py-2">Employé</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                                <th>Rapport</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paiements as $p): ?>
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="py-2"><?= htmlspecialchars($p['nom_complet']) ?></td>
                                    <td class="py-2"><?= htmlspecialchars($p['montant']) ?> FC</td>
                                    <td class="py-2"><?= htmlspecialchars($p['date_paiement']) ?></td>
                                    <td class="py-2"><?= htmlspecialchars($p['statut']) ?></td>
                                    <td class="py-2">
                                        <form method="post">
                                            <input type="hidden" name="paiement_id" value="<?= $p['id'] ?>">
                                            <button type="submit" name="annuler_paiement" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Annuler</button>
                                        </form>
                                        <form method="post">
                                            <input type="hidden" name="paiement_id" value="<?= $p['id'] ?>">
                                            <button type="submit" name="confirmer_paiement" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Confirmer</button>
                                        </form>
                                    </td>
                                    <td class="py-2">
                                        <form method="post">
                                            <input type="hidden" name="paiement_id" value="<?= $p['id'] ?>">
                                            <button type="submit" name="generer_rapport" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Générer Rapport</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Dettes -->
            <!-- payer un dete -->
            <div id="modalPaydette" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
                <div class="bg-white rounded-xl p-6 w-full max-w-md relative">
                    <button onclick="closeModal('modalPaydette')" class="absolute top-2 right-2 text-xl text-gray-600 hover:text-black">&times;</button>
                    <h3 class="text-lg font-semibold mb-4">payer une dette</h3>
                    <form method="post" class="space-y-4">
                        <div>
                            <label class="block mb-1">Employé :</label>
                            <select name="dette_id" required class="w-full border rounded px-3 py-2">
                                <option value="">-- Sélectionner --</option>

                                <?php foreach ($dettes as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nom_complet']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <input type="hidden" name="dette" value="<?= $d['montant'] ?>">
                            <label class="block mb-1">Montant :</label>
                            <input type="number" name="montant" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block mb-1">Date :</label>
                            <input type="date" name="date" required class="w-full border rounded px-3 py-2">
                        </div>
                        <button type="submit" name="pay_datte" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Valider</button>
                    </form>
                </div>
            </div>



            <h3 class="text-xl font-semibold mb-2">Dettes non payées</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm text-left bg-white shadow-md rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Employé</th>
                            <th class="px-4 py-2 border">Montant</th>
                            <th class="px-4 py-2 border">Date</th>
                            <th class="px-4 py-2 border">Statut</th>
                            <th class="px-4 py-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dettes as $d): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($d['nom_complet']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($d['montant']) ?> FC</td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($d['date_dette']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($d['statut']) ?></td>
                                <td class="px-4 py-2 border">

                                    <button onclick="openModal('modalPaydette')" class="bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700">Payer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modales -->
    <div id="modalAddPayment" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md relative">
            <button onclick="closeModal('modalAddPayment')" class="absolute top-2 right-2 text-xl text-gray-600 hover:text-black">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Ajouter un Paiement</h3>
            <form method="post" class="space-y-4">
                <div>
                    <label class="block mb-1">Employé :</label>
                    <select name="employe_id" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($employes as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom_complet']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-1">Montant :</label>
                    <input type="number" name="montant" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1">Date :</label>
                    <input type="date" name="date" required class="w-full border rounded px-3 py-2">
                </div>
                <button type="submit" name="add_payment" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Valider</button>
            </form>
        </div>
    </div>

    <div id="modalAddDette" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md relative">
            <button onclick="closeModal('modalAddDette')" class="absolute top-2 right-2 text-xl text-gray-600 hover:text-black">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Enregistrer une Dette</h3>
            <form method="post" class="space-y-4">
                <div>
                    <label class="block mb-1">Employé :</label>
                    <select name="employe_id" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($employes as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom_complet']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-1">Montant :</label>
                    <input type="number" name="montant" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1">Date :</label>
                    <input type="date" name="date" required class="w-full border rounded px-3 py-2">
                </div>
                <button type="submit" name="add_dette" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Valider</button>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('flex');
            document.getElementById(id).classList.add('hidden');
        }
        const sidebar = document.getElementById('sidebar');
        document.getElementById('openSidebarBtn').onclick = () => sidebar.classList.remove('-translate-x-full');
        document.getElementById('closeSidebarBtn').onclick = () => sidebar.classList.add('-translate-x-full');
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768 && sidebar && !sidebar.contains(e.target) && !openSidebarBtn.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>

</body>

</html>