<?php
require_once '../php/config.php';  // Ensure the correct path

class Users {
    private $pdo;

/*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Constructor for the Users class. Sets the PDO object
     *
     * @param PDO $db A PDO object
     */
/*******  bff22442-9f60-45c9-aa93-56747e4faaf5  *******/    public function __construct($db) {
        $this->pdo = $db;
    }

    public function getAllUsers() {
        try {
            $query = "SELECT id, first_name, last_name, middle_name, email, type_of_user, disabled_user FROM users";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if (!$users) {
                return ["error" => "No users found in the database"];
            }
    
            return $users;
        } catch (PDOException $e) {
            return ["error" => "Query failed: " . $e->getMessage()];
        }
    }
    
}
?>
