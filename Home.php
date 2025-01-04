<?php
require_once 'connection/db_connectionm.php';

// Open database connection
$conn = OpenCon();

// Initialize search variables
$search_results = null;
$search_query = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM friends WHERE friend_name LIKE ?");
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $search_results = $stmt->get_result();

    // Debug statement
    if ($search_results === false) {
        die("Error executing query: " . $stmt->error);
    }

    // Debug statement
    echo "Number of results: " . $search_results->num_rows;
}

// Fetch posts from the database
$sql_posts = "SELECT * FROM posts ORDER BY created_at DESC";
$result_posts = $conn->query($sql_posts);

// Fetch friends from the database (assuming you have a 'friends' table)
$sql_friends = "SELECT * FROM friends"; // Adjust the query based on your table structure
$result_friends = $conn->query($sql_friends);

// Close database connection
CloseCon($conn);

// Determine which content to show based on the button clicked
$show_friends = isset($_GET['show']) && $_GET['show'] === 'friends';
$show_feeds = isset($_GET['show']) && $_GET['show'] === 'feeds';
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
            <img src="./assets/images/2.png" alt="Logo">
        </div>
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" value="<?= htmlspecialchars($search_query); ?>" placeholder="Search...">
                <button type="submit">Search</button>
            </form>
        </div>
    </nav>
    <br>

    <!-- Display Search Results -->
    <?php if (!empty($search_query)): ?>
        <div class="search-results">
            <h3>Search Results for "<?= htmlspecialchars($search_query); ?>"</h3>
            <?php if ($search_results && $search_results->num_rows > 0): ?>
                <?php while ($row = $search_results->fetch_assoc()): ?>
                    <div class="friend-profile">
                        <?php if (!empty($row['image_url'])): ?>
                            <img src="<?= $row['image_url']; ?>" alt="Profile Image">
                        <?php endif; ?>
                        <p><?= htmlspecialchars($row['friend_name']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No profiles found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Left Sidebar -->
        <div class="left-sidebar">
            <a href="?show=friends"><button class="friends-button">Friends</button></a><br>
            <button class="friends-button">My Profile</button><br>
            <a href="?show=feeds"><button class="friends-button">Feeds</button></a>
        </div>

        <!-- Posts Section -->
        <div class="posts-container">
            <?php if ($show_feeds || !$show_friends): ?>
                <?php
                if ($result_posts->num_rows > 0) {
                    while ($row = $result_posts->fetch_assoc()) {
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
            <?php endif; ?>

            <?php if ($show_friends): ?>
                <?php
                if ($result_friends->num_rows > 0) {
                    while ($row = $result_friends->fetch_assoc()) {
                        echo '<div class="right-sidebar">';
                        echo '<div>';
                        if ($row['image_url']) {
                            echo '<img src="' . $row['image_url'] . '" alt="Friend Image">';
                        }
                        echo '<p>' . $row['friend_name'] . '</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No friends found.</p>';
                }
                ?>
            <?php endif; ?>
        </div>

        <!-- Right Sidebar -->
        <div class="right-sidebar">
            <button class="friends-button">Friend requests</button>
            <div>
                <img src="./assets/images/1.jpeg" alt="">
                <p>John Doe</p>
                <button class="accept">Accept</button>
                <button class="reject">Reject</button>
            </div>
        </div>
    </div>
</body>

</html>