<?php
session_start();
include('db.php');

if ($_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['return'])) {
    $book_id = $_POST['book_id'];

    $update_quantity_query = "UPDATE books SET quantity = quantity + 1 WHERE id = $book_id";
    if ($conn->query($update_quantity_query) === TRUE) {
        header("Location: user_dashboard.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
