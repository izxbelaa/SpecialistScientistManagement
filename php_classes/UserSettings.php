<?php

class UserSettings {
    private $userId;
    private $settings;
    private $db;

    // Default settings
    private $defaultSettings = [
        'theme' => 'light',
        'notifications' => true,
        'language' => 'en',
        'itemsPerPage' => 10,
        'emailNotifications' => true,
        'timezone' => 'UTC'
    ];

    public function __construct($userId, $db) {
        $this->userId = $userId;
        $this->db = $db;
        $this->loadSettings();
    }

    private function loadSettings() {
        // Load settings from database
        $stmt = $this->db->prepare("SELECT settings FROM user_settings WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $this->settings = json_decode($result['settings'], true);
        } else {
            // If no settings exist, use defaults and create new entry
            $this->settings = $this->defaultSettings;
            $this->saveSettings();
        }
    }

    public function getSetting($key) {
        return $this->settings[$key] ?? $this->defaultSettings[$key] ?? null;
    }

    public function getAllSettings() {
        return $this->settings;
    }

    public function updateSetting($key, $value) {
        $this->settings[$key] = $value;
        return $this->saveSettings();
    }

    public function updateSettings($newSettings) {
        $this->settings = array_merge($this->settings, $newSettings);
        return $this->saveSettings();
    }

    public function resetToDefault() {
        $this->settings = $this->defaultSettings;
        return $this->saveSettings();
    }

    private function saveSettings() {
        $stmt = $this->db->prepare("
            INSERT INTO user_settings (user_id, settings) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE settings = ?
        ");
        
        $settingsJson = json_encode($this->settings);
        return $stmt->execute([$this->userId, $settingsJson, $settingsJson]);
    }
} 