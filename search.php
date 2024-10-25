<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_query = $_GET['search_query'];

    $sql = "SELECT * FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%'";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Search Results</title>
    <link rel="stylesheet" type="text/css" href="styles/search.css">


</head>

<body>
    <header>
        <link href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap" rel="stylesheet">
        <h1>Library Management System</h1>
        <button onclick="goBack()">Go Back</button>
        <script>
            function goBack() {
                window.history.back();
            }
        </script>
    </header>
    <main>
        <h2>Search Results</h2>
        <form method="get" action="search.php">
            <label for="search_query">Search:</label>
            <input type="text" id="search_query" name="search_query" required>
            <input type="submit" value="Search" name="search">
        </form>

        <?php
        if (isset($result) && $result->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Title</th><th>Author</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['author'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "No results found.";
        }
        ?>
        <div id="customAlert" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p id="bookTitles">Book Titles:</p>
            </div>
        </div>
    </main>
    <footer>
        <div>
            <h3>Contact Us</h3>
            <p>Email: drelentro@gmail.com</p>
            <p>Phone: +60196941763</p>
        </div>


    </footer>
</body>

</html>