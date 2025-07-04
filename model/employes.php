<?php
require_once '../config/config.php';


class employesModel
{
    protected $pdo;
    public function __construct()
    {
        // Assuming $pdo is defined in config.php or you need to create a new PDO instance here
        global $pdo;
        $this->pdo = $pdo;
    }


    public function getAllEmployes()
    {
        $stmt = $this->pdo->query("SELECT * FROM employees ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getEmployesCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM employees");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count']; // Returns the total number of employees
    }
        
    public function getEmployesByPage($page, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->pdo->prepare("SELECT * FROM employees ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function searchEmployes($search)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM employees WHERE nom_complet LIKE ? OR telephone LIKE ? or email LIKE ? or id LIKE ?");
        $searchTerm = '%' . $search . '%';
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function deleteEmploye($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0; // Returns true if a row was deleted
    }



    public function addEmploye($nom_complet, $email, $telephone, $poste, $salaire)
    {
        // Validate inputs
        if (empty($nom_complet) || empty($email) || empty($telephone) || empty($poste) || empty($salaire)) {
            throw new InvalidArgumentException("All fields are required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format.");
        }
        if (!is_numeric($telephone)) {
            throw new InvalidArgumentException("Telephone must be numeric.");
        }
        if (!is_numeric($salaire) || $salaire < 0) {
            throw new InvalidArgumentException("Salary must be a positive number.");
        }
        // Prepare and execute the insert statement
        // Assuming date_embauche is set to the current date
        $nom_complet = htmlspecialchars($nom_complet);
        $email = htmlspecialchars($email);
        $telephone = htmlspecialchars($telephone);
        $poste = htmlspecialchars($poste);
        $salaire = htmlspecialchars($salaire);

        $stmt = $this->pdo->prepare("INSERT INTO employees (nom_complet, email, telephone, poste, salaire, date_embauche) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom_complet, $email, $telephone, $poste, $salaire, date('Y-m-d')]);
        return $this->pdo->lastInsertId(); // Returns the ID of the newly added employee
    }
    public function updateEmploye($id, $nom_complet, $email, $telephone, $poste, $salaire)
    {
        $stmt = $this->pdo->prepare("UPDATE employees SET nom_complet = ?, email = ?, telephone = ?, poste = ?, salaire = ?, date_embauche = ? WHERE id = ?");
        $stmt->execute([$nom_complet, $email, $telephone, $poste, $salaire, date('Y-m-d'), $id]);
        return $stmt->rowCount() > 0; // Returns true if a row was updated
    }
    public function getEmployeById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Returns the employee data or false if not found
    }
    public function getTodayPresences()
    {
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT e.nom_complet, e.email, e.telephone, e.poste, e.salaire, p.date_presence, p.heure_arrivee, p.heure_depart, p.statut
                                     FROM presences p
                                     JOIN employees e ON p.employee_id = e.id
                                     WHERE p.date_presence = ?");
        $stmt->execute([$today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns all presences for today
    }
    public function getAllPresences()
    {
        $stmt = $this->pdo->query("SELECT e.nom, e.prenom, p.date_presence, p.heure_arrivee, p.heure_depart, p.statut
                                   FROM presences p
                                   JOIN employees e ON p.employee_id = e.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns all presences
    }
}
