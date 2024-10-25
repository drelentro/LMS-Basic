<?php include('header.php'); ?>
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
            $user_id = $_SESSION['user_id'];
            $insert_borrowing_query = "INSERT INTO borrowings (user_id, book_id) VALUES ($user_id, $book_id)";
            if ($conn->query($insert_borrowing_query) === TRUE) {
                header("Location: user_dashboard.php");
                exit();
            } else {
                echo "Error inserting borrowing record: " . $conn->error;
            }
        } else {
            echo "Error updating book quantity: " . $conn->error;
        }
    } else {
        echo "Book is currently not available for borrowing.";
    }
}

if (isset($_POST['return'])) {
    $book_id = $_POST['book_id'];

    $update_quantity_query = "UPDATE books SET quantity = quantity + 1 WHERE id = $book_id";
    if ($conn->query($update_quantity_query) === TRUE) {
        //remove
        $user_id = $_SESSION['user_id'];
        $delete_borrowed_query = "DELETE FROM borrowings WHERE book_id = $book_id AND user_id = $user_id";
        if ($conn->query($delete_borrowed_query) === TRUE) {
            header("Location: user_dashboard.php");
            exit();
        } else {
            echo "Error deleting borrowed book: " . $conn->error;
        }
    } else {
        echo "Error updating book quantity: " . $conn->error;
    }
}

$sql_available_books = "SELECT * FROM books WHERE quantity > 0";
$result_available_books = $conn->query($sql_available_books);

//getting books borrowed by users
$user_id = $_SESSION['user_id'];
$sql_borrowed_books = "SELECT books.id, books.title, books.author FROM books
                       INNER JOIN borrowings ON books.id = borrowings.book_id
                       WHERE borrowings.user_id = $user_id";
$result_borrowed_books = $conn->query($sql_borrowed_books);
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap" rel="stylesheet">
    <link href="styles/u_dashboard.css" rel="stylesheet" />

</head>

<body>
    <h1 class="welcome">Welcome, <?php echo $_SESSION['username']; ?> (User)!</h1>

    <h2>Available Books</h2>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_available_books->num_rows > 0) {
            while ($row = $result_available_books->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['author'] . "</td>";
                echo "<td>";
                echo "<form method='post' action='user_dashboard.php'>";
                echo "<input type='hidden' name='book_id' value='" . $row['id'] . "'>";
                echo "<input class='action' type='submit' value='Borrow' name='borrow'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No books available for borrowing</td></tr>";
        }
        ?>
    </table>

    <br>

    <h2>Borrowed Books</h2>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_borrowed_books->num_rows > 0) {
            while ($borrowed_row = $result_borrowed_books->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $borrowed_row['title'] . "</td>";
                echo "<td>" . $borrowed_row['author'] . "</td>";
                echo "<td>";
                echo "<form method='post' action='user_dashboard.php'>";
                echo "<input type='hidden' name='book_id' value='" . $borrowed_row['id'] . "'>";
                echo "<input class='action' type='submit' value='Return' name='return'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>You have not borrowed any books</td></tr>";
        }
        ?>
    </table>

    <br>
    <a href="logout.php">Logout</a>

    <script src="scripts/u_dashboard.js"></script>
</body>

</html>
<?php include('footer.php'); ?>