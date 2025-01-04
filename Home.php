<?php
require_once 'connection/db_connectionm.php';

// Open database connection
$conn = OpenCon();

// Fetch posts from the database
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);

// Close database connection
CloseCon($conn);
?>


<!DOCTYPE html>
<html>
<link rel="stylesheet" href="styles/styles.css">

<head>
    <title>Social Media</title>
</head>

<body class="container">
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <img src="./assets/images/geometric-logo-design.png" alt="Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <button>Search</button>
        </div>
    </nav>
    <br>
    <!-- Main Content -->
    <div class="main-container">
        <!-- Left Sidebar -->
        <div class="left-sidebar">
            <button class="friends-button">Friends</button><br>
            <button class="friends-button">My Profile</button> <br>

            <button class="friends-button">feeds</button>
        </div>

        <!-- Posts Section -->
        <div class="posts-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="post">';
                    echo '<h3>' . $row['username'] . '</h3>';
                    if ($row['image_url']) {
                        echo '<img src="' . $row['image_url'] . '" alt="Post Image">';
                    }
                    echo '<p>' . $row['content'] . '</p>';
                    echo '<div class="post-actions">';
                    echo '<button>Like</button>';
                    echo '<button>Comment</button>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No posts found.</p>';
            }
            ?>
        </div>
        <!-- Right Sidebar -->

        <div class="right-sidebar">
            <button class="friends-button"> Friend requests</button>
            <div>
                <img src="./assets/images/AA1qzbvd.jpeg" alt="">
                <p>John Doe</p>
                <button class="accept">Accept</button>
                <button class="reject">Reject</button>
            </div>
        </div>
    </div>
</body>

</html>