<?php
require_once '../config/config.php';
require_once '../model/presences.php';
class presencesModel
{
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }
    public function addPresence($employe_id, $date, $heure, $statut) {
        $stmt = $this->pdo->prepare("INSERT INTO presences (employee_id, date_presence, heure_arrivee, statut) VALUES (?, ?, ?, ?)");
        $stmt->execute([$employe_id, $date, $heure, $statut]);
    }
    public function getTodayPresences()
    {
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT e.nom_complet, e.email, e.telephone, e.poste, e.salaire, p.date_presence, p.heure_arrivee, p.heure_depart, p.statut
                                      FROM presences p
                                      JOIN employees e ON p.employee_id = e.id
                                      WHERE p.date_presence = ?");
        $stmt->execute([$today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getMonthPresences($employe_id, $mois, $annee)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM presences WHERE employee_id = ? AND MONTH(date_presence) = ? AND YEAR(date_presence) = ?");
        $stmt->execute([$employe_id, $mois, $annee]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPreviousPresences()
    {
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT e.nom_complet, e.email, e.telephone, e.poste, p.date_presence, p.heure_arrivee, p.heure_depart, p.statut
                                     FROM presences p
                                     JOIN employees e ON p.employee_id = e.id
                                     WHERE p.date_presence < ?
                                     ORDER BY p.date_presence DESC");
        $stmt->execute([$today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllPresences()
    {
        $stmt = $this->pdo->prepare("SELECT e.nom_complet, e.email, e.telephone, e.poste, p.date_presence, p.heure_arrivee, p.heure_depart, p.statut
                                     FROM presences p
                                     JOIN employees e ON p.employee_id = e.id
                                     ORDER BY p.date_presence DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllEmployes()
    {
        $stmt = $this->pdo->prepare("SELECT id, nom_complet FROM employees");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
