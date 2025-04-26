<?php
// php/assign-evaluators.php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__.'/session_check.php';
require_once __DIR__.'/config.php';

// validate input
$requestId = filter_input(INPUT_POST,'request_id',FILTER_VALIDATE_INT);
$reviewers = filter_input(
  INPUT_POST,'reviewers',FILTER_DEFAULT,FILTER_REQUIRE_ARRAY
);

if (!$requestId || empty($reviewers)) {
  echo json_encode([
    'success'=>false,
    'message'=>'Επιλέξτε αίτηση και τουλάχιστον έναν αξιολογητή.'
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$reviewers = array_map('intval',$reviewers);

try {
  $pdo->beginTransaction();
  // wipe old
  $pdo->prepare("DELETE FROM evaluators WHERE request_id=?")
      ->execute([$requestId]);
  // insert new
  $ins = $pdo->prepare("INSERT INTO evaluators(user_id,request_id) VALUES(?,?)");
  foreach($reviewers as $u) {
    $ins->execute([$u,$requestId]);
  }
  $pdo->commit();
  echo json_encode([
    'success'=>true,
    'message'=>'Οι αξιολογητές ανατέθηκαν με επιτυχία.'
  ], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
  $pdo->rollBack();
  echo json_encode([
    'success'=>false,
    'message'=>'Σφάλμα βάσης: '.$e->getMessage()
  ], JSON_UNESCAPED_UNICODE);
}
