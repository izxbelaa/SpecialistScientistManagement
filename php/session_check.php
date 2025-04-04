<?php
// Ελέγχει αν έχει ξεκινήσει session και ξεκινά αν χρειάζεται
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
