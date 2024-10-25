<?php
session_start();
include('db.php');

if ($_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['borrow'])) {
    $book_id = $_POST['book_id'];

    $check_quantity_query = "SELECT * FROM books WHERE id = $book_id AND quantity > 0";
    $quantity_result = $conn->query($check_quantity_query);

    if ($quantity_result->num_rows > 0) {
        $update_quantity_query = "UPDATE books SET quantity = quantity - 1 WHERE id = $book_id";
        if ($conn->query($update_quantity_query) === TRUE) {
            header("Location: user_dashboard.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Book is currently not available for borrowing.";
    }
}
