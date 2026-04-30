<?php
// ============================================================
// STEP 1: Connect to the database
// ============================================================
require_once 'db.php';

// ============================================================
// STEP 2: Get the student ID from the URL
// The link in index.php looks like: delete.php?id=5
// We cast it to (int) to make sure it is a whole number.
// If the id is missing or 0, redirect away immediately.
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit();
}

// ============================================================
// STEP 3: Make sure the student actually exists before deleting
// This prevents errors if someone types a random id in the URL.
// ============================================================
$stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result(); // store_result() lets us call num_rows

if ($stmt->num_rows === 0) {
    // No student found — go back to the list
    $stmt->close();
    header('Location: index.php');
    exit();
}
$stmt->close();

// ============================================================
// STEP 4: Run the DELETE query
// We use a prepared statement (with ?) for safety.
// ============================================================
$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param('i', $id); // 'i' = integer
$stmt->execute();
$stmt->close();

// ============================================================
// STEP 5: Redirect back to the student list
// Whether the delete worked or not, the user goes back to index.
// ============================================================
header('Location: index.php');
exit();
