<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'errors' => ['general' => 'Δεν είστε συνδεδεμένος.']]);
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Sanitize and trim inputs
function clean($key) {
    return trim($_POST[$key] ?? '');
}

$fields = [
    'dob', 'gender', 'social_security_number', 'cypriot_id',
    'postal_code', 'street_address', 'city', 'country',
    'municipality', 'community', 'nationality', 'university_email',
    'mobile_phone', 'landline_phone'
];

// Assign values
$data = [];
foreach ($fields as $field) {
    $data[$field] = clean($field);
}
// Add main email
$data['email'] = clean('email');

// Set university_email to null if empty
if ($data['university_email'] === '') {
    $data['university_email'] = null;
}

// Basic validations
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['dob'])) {
    $errors['dob'] = 'Μη έγκυρη ημερομηνία.';
}

if (!in_array($data['gender'], ['M', 'F'], true)) {
    $errors['gender'] = 'Μη έγκυρο φύλο.';
}

if ($data['university_email'] !== null && !filter_var($data['university_email'], FILTER_VALIDATE_EMAIL)) {
    $errors['university_email'] = 'Μη έγκυρη διεύθυνση email.';
}

// Validate password only if changing
$old_password = clean('old_password');
$new_password = clean('password');
$confirm_password = clean('confirm_password');
$change_password = false;

if ($new_password !== '') {
    if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[\W]/', $new_password)) {
        $errors['password'] = 'Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες, 1 κεφαλαίο, 1 πεζό και 1 σύμβολο.';
    } elseif ($new_password !== $confirm_password) {
        $errors['confirm_password'] = 'Οι κωδικοί δεν ταιριάζουν.';
    } else {
        // Verify old password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($old_password, $row['password'])) {
            $errors['old_password'] = 'Ο τρέχων κωδικός είναι λανθασμένος.';
        } else {
            $change_password = true;
        }
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Perform update
try {
    $sql = "UPDATE users SET 
        dob = :dob,
        gender = :gender,
        social_security_number = :social_security_number,
        cypriot_id = :cypriot_id,
        postal_code = :postal_code,
        street_address = :street_address,
        city = :city,
        country = :country,
        municipality = :municipality,
        community = :community,
        nationality = :nationality,
        email = :email,
        university_email = :university_email,
        mobile_phone = :mobile_phone,
        landline_phone = :landline_phone";

    if ($change_password) {
        $sql .= ", password = :password";
        $data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = :user_id";

    $stmt = $pdo->prepare($sql);
    $data['user_id'] = $user_id;
    $stmt->execute($data);

    echo json_encode(['success' => true]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'errors' => ['general' => 'Σφάλμα βάσης δεδομένων: ' . $e->getMessage()]]);
    exit;
}
?>
