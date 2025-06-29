<?php
require_once 'config/config.php';

// Exemple d'IDs d'employés existants (à adapter selon ta base)
$employee_ids = [1, 2, 3, 8, 9];

$mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai'];
$montants = [50000, 60000, 55000, 70000, 65000];
$date_paiements = [
    '2024-01-31',
    '2024-02-28',
    '2024-03-31',
    '2024-04-30',
    '2024-05-31'
];

for ($i = 0; $i < 5; $i++) {
    $stmt = $pdo->prepare("INSERT INTO paiements (employee_id, mois, montant, date_paiement) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $employee_ids[$i],
        $mois[$i],
        $montants[$i],
        $date_paiements[$i]
    ]);
}

echo "5 paiements insérés avec succès.";
