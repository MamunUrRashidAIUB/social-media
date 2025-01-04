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
            <!-- Image Post -->
            <div class="post">
                <h3>Lione Messi</h3>
                <img src="./assets/images/AA1qzbvd.jpeg" alt="Post Image">
                <p>Messi is the GOAT</p>
                <div class="post-actions">
                    <button>Like</button>

                    <button>Comment</button>
                </div>
            </div>

            <!-- Text Post -->
            <div class="post">
                <h3>Lonel Messi</h3>
                <p>An Argentine international, Messi is the national team's all-time leading goalscorer and most-capped player. His style of play as a diminutive, left-footed dribbler drew career-long comparisons with compatriot Diego Maradona, who described Messi as his successor.
                    An Argentine international, Messi is the national team's all-time leading goalscorer and most-capped player. His style of play as a diminutive, left-footed dribbler drew career-long comparisons with compatriot Diego Maradona, who described Messi as his successor.
                </p>
                <div class="post-actions">
                    <button>Like</button>

                    <button>Comment</button>
                </div>
            </div>

            <!-- Another Image Post -->
            <div class="post">
                <h3>Lionel Messi</h3>
                <img src="./assets/images/leo.jpg" alt="Post Image">
                <p>Exploring the mountains!</p>
                <div class="post-actions">
                    <button>Like</button>

                    <button>Comment</button>
                </div>
            </div>
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