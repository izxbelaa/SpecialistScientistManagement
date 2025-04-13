<?php

class Academy {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    private function validateInput($name, $code, $excludeId = null) {
        // Validate name length
        if (strlen($name) < 2 || strlen($name) > 255) {
            throw new Exception("Academy name must be between 2 and 255 characters");
        }

        // Validate code format (alphanumeric, 2-50 characters)
        if (!preg_match('/^[A-Za-z0-9]{2,50}$/', $code)) {
            throw new Exception("Academy code must be alphanumeric and between 2-50 characters");
        }

        // Check for duplicate name
        $nameQuery = "SELECT id FROM academies WHERE academy_name = ?";
        if ($excludeId !== null) {
            $nameQuery .= " AND id != ?";
        }
        $nameStmt = $this->db->prepare($nameQuery);
        $params = [$name];
        if ($excludeId !== null) {
            $params[] = $excludeId;
        }
        $nameStmt->execute($params);
        if ($nameStmt->fetchColumn()) {
            throw new Exception("An academy with this name already exists");
        }

        // Check for duplicate code
        $codeQuery = "SELECT id FROM academies WHERE academy_code = ?";
        if ($excludeId !== null) {
            $codeQuery .= " AND id != ?";
        }
        $codeStmt = $this->db->prepare($codeQuery);
        $params = [$code];
        if ($excludeId !== null) {
            $params[] = $excludeId;
        }
        $codeStmt->execute($params);
        if ($codeStmt->fetchColumn()) {
            throw new Exception("An academy with this code already exists");
        }
    }

    public function create($name, $code) {
        try {
            // Trim inputs
            $name = trim($name);
            $code = strtoupper(trim($code));

            // Validate inputs
            $this->validateInput($name, $code);

            // Insert new academy
            $stmt = $this->db->prepare("INSERT INTO academies (academy_name, academy_code) VALUES (?, ?)");
            $success = $stmt->execute([$name, $code]);
            
            if (!$success) {
                $error = $stmt->errorInfo();
                throw new Exception("Database error: " . $error[2]);
            }
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database error in Academy::create: " . $e->getMessage());
            throw new Exception("Error creating academy: " . $e->getMessage());
        }
    }

    public function update($id, $name, $code) {
        try {
            // Trim inputs
            $name = trim($name);
            $code = strtoupper(trim($code));

            // Validate inputs (excluding current record)
            $this->validateInput($name, $code, $id);

            $stmt = $this->db->prepare("UPDATE academies SET academy_name = ?, academy_code = ? WHERE id = ?");
            $success = $stmt->execute([$name, $code, $id]);
            
            if (!$success) {
                $error = $stmt->errorInfo();
                throw new Exception("Database error: " . $error[2]);
            }
            
            return $success;
        } catch (PDOException $e) {
            throw new Exception("Error updating academy: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM academies WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting academy: " . $e->getMessage());
        }
    }

    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM academies ORDER BY academy_name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching academies: " . $e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM academies WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching academy: " . $e->getMessage());
        }
    }
} 