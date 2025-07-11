<?php
require_once '../config/config.php';
require_once '../model/employes.php';
require_once '../model/presences.php';



$employemodel = new employesModel();
$employes = $employemodel->getAllEmployes();
foreach ($employes as $employe) {
    $employe['date_embauche'] = date('d/m/Y', strtotime($employe['date_embauche']));
}


$presencesModel = new presencesModel();
$presences = $presencesModel->getTodayPresences();
$today = date('Y-m-d');
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $employes = $employemodel->searchEmployes($search);
} else {
    $employes = $employemodel->getAllEmployes();
}
if (isset($_GET['submit'])) {
    $search = $_GET['search'];
    $employes = $employemodel->searchEmployes($search);
} else {
    $employes = $employemodel->getAllEmployes();
}
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $employemodel->deleteEmploye($id);
    header("Location: employesView.php");
    exit();
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $employeData = [
            'nom_complet' => $_POST['nom_complet'] ?? '',
            'email' => $_POST['email'] ?? '',
            'telephone' => $_POST['telephone'] ?? '',
            'poste' => $_POST['poste'] ?? '',
            'salaire' => $_POST['salaire'] ?? ''
        ];
        $employemodel->addEmploye(
            $employeData['nom_complet'],
            $employeData['email'],
            $employeData['telephone'],
            $employeData['poste'],
            $employeData['salaire']
        );
        header("Location: employesView.php");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    try {
        $id = $_POST['update_id'];
        $nom = $_POST['update_nom_complet'];
        $email = $_POST['update_email'];
        $telephone = $_POST['update_telephone'];
        $poste = $_POST['update_poste'];
        $salaire = $_POST['update_salaire'];
        $employemodel->updateEmploye($id, $nom, $email, $telephone, $poste, $salaire);
        header("Location: employesView.php");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employés - Gestion RH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Sidebar overlay for mobile */
        .sidebar-overlay { display: none; }
        @media (max-width: 768px) {
            .sidebar-overlay { display: block; position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 40; }
            .sidebar-responsive { position: fixed; left: 0; top: 0; height: 100vh; z-index: 50; transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar-open { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-blue-800 text-white p-4 shadow flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="../public/images/logo.png" alt="Logo Société" class="h-10 w-auto rounded bg-white p-1 shadow" style="max-width:48px;">
            <div>
                <h1 class="text-xl font-bold">Société Kabipangi-Fils</h1>
                <p class="text-sm">Gestion du personnel</p>
            </div>
        </div>
        <!-- Burger button for mobile -->
        <button id="burgerBtn" class="md:hidden text-white text-3xl focus:outline-none">
            <i class="fas fa-bars"></i>
        </button>
    </header>

    <!-- Sidebar overlay (mobile) -->
    <div id="sidebarOverlay" class="sidebar-overlay hidden"></div>

    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-responsive bg-white w-64 border-r shadow-md h-full md:static md:translate-x-0 md:h-auto md:block">
            <nav class="p-4 space-y-4">
                <a href="dashboard.php" class="block px-2 py-1 rounded transition nav-link" data-link="dashboard.php">Dashboard</a>
                <a href="employesView.php" class="block px-2 py-1 rounded transition nav-link" data-link="employesView.php">Employés</a>
                <a href="presenceView.php" class="block px-2 py-1 rounded transition nav-link" data-link="presenceView.php">Présences</a>
                <a href="payementView.php" class="block px-2 py-1 rounded transition nav-link" data-link="payementView.php">Paiements</a>
                <a href="../rapport.php" class="block px-2 py-1 rounded transition nav-link" data-link="rapport.php">Rapports</a>
            </nav>
        </aside>
        <!-- Main content -->
        <main class="flex-1 p-6 md:ml-0 w-full">
            <h2 class="text-2xl font-bold mb-4">Touts employés</h2>
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
                <h3 class="text-lg font-semibold mb-2">Liste des employees</h3>
                <form action="" class="flex items-center gap-2 mb-4">
                    <input
                        type="text"
                        name="search"
                        placeholder="Recherchez un employé..."
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition w-full md:w-64">
                    <input
                        type="submit"
                        name="submit"
                        value="Recherchez"
                        class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition cursor-pointer font-semibold">
                </form>
                <!-- Bouton pour ouvrir le modal -->
                <button 
                    id="openModalBtn"
                    type="button"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition cursor-pointer font-semibold mb-4"
                >
                    Ajouter un employé
                </button>

                <!-- Modal -->
                <div 
                    id="addEmployeModal"
                    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden"
                >
                    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md relative">
                        <button 
                            id="closeModalBtn"
                            type="button"
                            class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl font-bold"
                        >&times;</button>
                        <h2 class="text-xl font-bold mb-4 text-center">Ajouter un Employé</h2>
                        <form action="" method="POST" class="space-y-4">
                            <div>
                                <label for="nom_complet" class="block mb-1 font-semibold">Nom Complet:</label>
                                <input type="text" name="nom_complet" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <div>
                                <label for="email" class="block mb-1 font-semibold">Email:</label>
                                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <div>
                                <label for="telephone" class="block mb-1 font-semibold">Téléphone:</label>
                                <input type="text" name="telephone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <div>
                                <label for="poste" class="block mb-1 font-semibold">Poste:</label>
                                <input type="text" name="poste" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <div>
                                <label for="salaire" class="block mb-1 font-semibold">Salaire:</label>
                                <input type="number" name="salaire" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <button type="submit" name="add" class="w-full px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition font-semibold">Ajouter</button>
                        </form>
                    </div>
                </div>

                <!-- Modal Update Employé -->
                <div 
                    id="updateEmployeModal"
                    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden"
                >
                    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md relative">
                        <button 
                            id="closeUpdateModalBtn"
                            type="button"
                            class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl font-bold"
                        >&times;</button>
                        <h2 class="text-xl font-bold mb-4 text-center">Modifier un Employé</h2>
                        <form id="updateEmployeForm" action="" method="POST" class="space-y-4">
                            <input type="hidden" name="update_id" id="update_id" />
                            <div>
                                <label class="block mb-1 font-semibold">Nom Complet:</label>
                                <input type="text" name="update_nom_complet" id="update_nom_complet" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Email:</label>
                                <input type="email" name="update_email" id="update_email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Téléphone:</label>
                                <input type="text" name="update_telephone" id="update_telephone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Poste:</label>
                                <input type="text" name="update_poste" id="update_poste" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Salaire:</label>
                                <input type="number" name="update_salaire" id="update_salaire" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            </div>
                            <button type="submit" name="update" class="w-full px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition font-semibold">Mettre à jour</button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

                <div class="overflow-x-auto">
                    <table class="w-full text-left table-auto">
                        <thead>
                            <tr class="text-sm text-gray-600 border-b">
                                <th class="py-2">Nom complet</th>
                                <th>telephone</th>
                                <th>email</th>
                                <th>poste</th>
                                <th>Salaire</th>
                                <th>date d'embauche</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employes as $employe): ?>
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="py-2"><?php echo htmlspecialchars($employe['nom_complet']); ?></td>
                                    <td><?php echo htmlspecialchars($employe['telephone']); ?></td>
                                    <td><?php echo htmlspecialchars($employe['email']); ?></td>
                                    <td><?php echo htmlspecialchars($employe['poste']); ?></td>
                                    <td><?php echo htmlspecialchars($employe['salaire']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($employe['date_embauche']))); ?></td>
                                    <td class="text-center">
                                        <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 editBtn"
                                            data-id="<?php echo $employe['id']; ?>"
                                            data-nom="<?php echo htmlspecialchars($employe['nom_complet']); ?>"
                                            data-email="<?php echo htmlspecialchars($employe['email']); ?>"
                                            data-telephone="<?php echo htmlspecialchars($employe['telephone']); ?>"
                                            data-poste="<?php echo htmlspecialchars($employe['poste']); ?>"
                                            data-salaire="<?php echo htmlspecialchars($employe['salaire']); ?>"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cet employé ?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $employe['id']; ?>">
                                            <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
    <script>
    // Sidebar responsive logic (factorisable)
    (function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const burgerBtn = document.getElementById('burgerBtn');
    // Ouvre la sidebar sur mobile
    burgerBtn && burgerBtn.addEventListener('click', function() {
        sidebar.classList.add('sidebar-open');
        overlay.classList.remove('hidden');
    });
    // Ferme la sidebar en cliquant sur l'overlay
    overlay && overlay.addEventListener('click', function() {
        sidebar.classList.remove('sidebar-open');
        overlay.classList.add('hidden');
    });
    // Ferme la sidebar si on resize en desktop
    window.addEventListener('resize', function() {
        if(window.innerWidth >= 768) {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.add('hidden');
        }
    });
    // Lien actif dynamique
    const links = document.querySelectorAll('.nav-link');
    const current = window.location.pathname.split('/').pop();
    links.forEach(link => {
        if(link.getAttribute('data-link') === current) {
            link.classList.add('bg-blue-100', 'text-blue-800', 'font-semibold');
        } else {
            link.classList.remove('bg-blue-100', 'text-blue-800', 'font-semibold');
        }
    });
})();

    // Ouvre le modal d'ajout
    document.getElementById('openModalBtn').onclick = function() {
        document.getElementById('addEmployeModal').classList.remove('hidden');
    };
    // Ferme le modal d'ajout
    document.getElementById('closeModalBtn').onclick = function() {
        document.getElementById('addEmployeModal').classList.add('hidden');
    };
    // Fermer en cliquant sur le fond noir (ajout)
    document.getElementById('addEmployeModal').onclick = function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    };

    // Ouvre le modal de modification et pré-remplit les champs
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.onclick = function() {
            document.getElementById('update_id').value = this.dataset.id;
            document.getElementById('update_nom_complet').value = this.dataset.nom;
            document.getElementById('update_email').value = this.dataset.email;
            document.getElementById('update_telephone').value = this.dataset.telephone;
            document.getElementById('update_poste').value = this.dataset.poste;
            document.getElementById('update_salaire').value = this.dataset.salaire;
            document.getElementById('updateEmployeModal').classList.remove('hidden');
        }
    });
    // Ferme le modal de modification
    document.getElementById('closeUpdateModalBtn').onclick = function() {
        document.getElementById('updateEmployeModal').classList.add('hidden');
    };
    // Fermer en cliquant sur le fond noir (modif)
    document.getElementById('updateEmployeModal').onclick = function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    };
    </script>
</body>
</html>