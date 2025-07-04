<?php
require_once '../config/config.php';
require_once '../controler/payementControler.php';

class PayementModel
{
    protected $pdo;
    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Ajouter un paiement pour un employé
    public function addPayment($employe_id, $mois, $montant, $date, $statut = 'effectué')
    {
        $stmt = $this->pdo->prepare("INSERT INTO paiements (employee_id, mois, montant, date_paiement, statut) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$employe_id, $mois, $montant, $date, $statut]);
    }

    // Enregistrer une dette pour un employé
    public function addDette($employe_id, $montant, $date, $statut = 'non payée')
    {
        $stmt = $this->pdo->prepare("INSERT INTO dettes (employee_id, montant, date_dette, statut) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$employe_id, $montant, $date, $statut]);
    }

    // Payer une dette (mettre à jour le statut)
    public function payerDette($dette_id, $montant, $date)
    {
        if ($montant > 0) {
            $stmt = $this->pdo->prepare("UPDATE dettes SET montant = :montant, date_paiement = :date WHERE id = :dette_id");
            return $stmt->execute(
                [
                    'dette_id' => $dette_id,
                    'montant' => $montant,
                    'date' => $date


                ]
            );
        }
        if ($montant <= 0) {
            $stmt = $this->pdo->prepare("UPDATE dettes SET statut = 'payée', date_paiement = :date WHERE id = :dette_id");
            return $stmt->execute(
                [
                    'dette_id' => $dette_id,
                    'date' => $date


                ]
            );
        }
    }

    // Récupérer l'historique des paiements d'un employé
    public function getPaiementsByEmploye($employe_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM paiements WHERE employee_id = ? ORDER BY date_paiement DESC");
        $stmt->execute([$employe_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer l'historique des dettes d'un employé
    public function getDettesByEmploye($employe_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM dettes WHERE employee_id = ? ORDER BY date_dette DESC");
        $stmt->execute([$employe_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer toutes les dettes non payées
    public function getAllDettesNonPayees()
    {
        $stmt = $this->pdo->query("SELECT d.*, e.nom_complet FROM dettes d JOIN employees e ON d.employee_id = e.id WHERE d.statut = 'non payée' ORDER BY d.date_dette DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les paiements
    public function getAllPaiements()
    {
        $stmt = $this->pdo->query("SELECT p.*, e.nom_complet FROM paiements p JOIN employees e ON p.employee_id = e.id ORDER BY p.date_paiement DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
