<?php include('headeradmin.php'); ?>
<?php
session_start();
include('db.php');

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

function sanitize($conn, $data)
{
    return mysqli_real_escape_string($conn, htmlspecialchars($data));
}

if (isset($_POST['remove_borrowing'])) {
    $borrowing_id = $_POST['borrowing_id'];

    $remove_query = "DELETE FROM borrowings WHERE id = $borrowing_id";

    if ($conn->query($remove_query) === TRUE) {
        echo "User removed from borrowing successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}



if (isset($_POST['remove_user'])) {
    $user_id = $_POST['user_id'];

    $remove_user_query = "DELETE FROM users WHERE id = $user_id AND role = 'user'";

    if ($conn->query($remove_user_query) === TRUE) {
        echo "User removed successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    $title = sanitize($conn, $_POST['title']);
    $author = sanitize($conn, $_POST['author']);
    $quantity = (int)$_POST['quantity'];

    $sql = "INSERT INTO books (title, author, quantity) VALUES ('$title', '$author', $quantity)";
    if ($conn->query($sql) === TRUE) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $sql = "DELETE FROM books WHERE id = $delete_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error deleting book: " . $conn->error;
    }
}

$sql = "SELECT * FROM books";
$result = $conn->query($sql);

$fetch_borrowing_query = "SELECT borrowings.id AS borrowing_id, users.username, books.title 
                          FROM borrowings 
                          INNER JOIN users ON borrowings.user_id = users.id 
                          INNER JOIN books ON borrowings.book_id = books.id";
$result_borrowing = $conn->query($fetch_borrowing_query);

// Getting all users but admin from db
$fetch_users_query = "SELECT * FROM users WHERE role = 'user'";
$result_users = $conn->query($fetch_users_query);
?>



<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap" rel="stylesheet">
    <link href="styles/a_dashboard.css" rel="stylesheet" />

    <title>Admin Dashboard</title>
</head>

<body>
    <h1 class="welcome">Welcome, <?php echo $_SESSION['username']; ?> (Admin)!</h1>

    <h2>Add Book</h2>
    <form class="form" method="post" action="">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="author">Author:</label><br>
        <input type="text" id="author" name="author" required><br><br>

        <label for="quantity">Quantity:</label><br>
        <input type="number" id="quantity" name="quantity" required><br><br>

        <input class="submit" type="submit" value="Add Book" name="add_book">
    </form>

    <h2>Book List:</h2>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['author'] . "</td>";
                echo "<td>" . $row['quantity'] . "</td>";
                echo "<td>";
                echo "<a href='edit_book.php?id=" . $row['id'] . "'>Edit</a> | ";
                echo "<a href='admin_dashboard.php?delete_id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this book?')\">Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No books available</td></tr>";
        }
        ?>
    </table>
    <h2>Borrowing Users and Books</h2>
    <table border="1">
        <tr>
            <th>User</th>
            <th>Book</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_borrowing->num_rows > 0) {
            while ($row = $result_borrowing->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>";
                echo "<form method='post' action='admin_dashboard.php'>";
                echo "<input type='hidden' name='borrowing_id' value='" . $row['borrowing_id'] . "'>";
                echo "<input class='act' type='submit' value='Remove Borrowing' name='remove_borrowing'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No users currently borrowing books</td></tr>";
        }
        ?>
    </table>

    <br>

    <h2>Remove Users</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_users->num_rows > 0) {
            while ($row = $result_users->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>";
                echo "<form method='post' action='admin_dashboard.php'>";
                echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
                echo "<input class='act' type='submit' value='Remove User' name='remove_user' onclick='return confirmDelete();'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No users found</td></tr>";
        }
        ?>

        <script>
            function confirmDelete() {
                return confirm("Are you sure you want to delete this user?");
            }
        </script>

    </table>
    </main>

    <br>
    <a href="logout.php">Logout</a>
    <script src="scripts/a_dashboard.js"></script>
</body>

</html>