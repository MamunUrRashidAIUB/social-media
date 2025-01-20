<?php
require_once 'connection/db_connectionm.php';

$conn = OpenCon();

$search_results = null;
$search_query = '';
$profile_data = null;

// Check if the user wants to view their profile
if (isset($_GET['show']) && $_GET['show'] === 'profile') {
    $profile_data = json_decode(file_get_contents('data/mydata.json'), true);
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM friends WHERE friend_name LIKE ?");
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $search_results = $stmt->get_result();

    if ($search_results === false) {
        die("Error executing query: " . $stmt->error);
    }
}

// Fetch posts from the database
$sql_posts = "SELECT * FROM posts ORDER BY created_at DESC";
$result_posts = $conn->query($sql_posts);

// Fetch friends from the database 
$sql_friends = "SELECT * FROM friends";
$result_friends = $conn->query($sql_friends);

// Close database connection
CloseCon($conn);

// Determine which content to show based on the button clicked
$show_friends = isset($_GET['show']) && $_GET['show'] === 'friends';
$show_feeds = isset($_GET['show']) && $_GET['show'] === 'feeds';
$show_profile = isset($_GET['show']) && $_GET['show'] === 'profile';
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

    <!-- Display Profile Data -->
    <?php if ($show_profile && $profile_data): ?>
        <div class="profile-data">
            <h3>Profile Information</h3>

            <p>Name: <?= htmlspecialchars($profile_data['name']); ?></p>
            <p>Age: <?= htmlspecialchars($profile_data['age']); ?></p>
            <p>Current Location: <?= htmlspecialchars($profile_data['current_location']); ?></p>
            <p>Hometown: <?= htmlspecialchars($profile_data['hometown']); ?></p>
            <h4>Education:</h4>
            <ul>
                <?php foreach ($profile_data['education'] as $school): ?>
                    <li><?= htmlspecialchars($school); ?></li>
                <?php endforeach; ?>
            </ul>
            <img src="<?= htmlspecialchars($profile_data['img']); ?>" alt="Profile Image" class="profile-image">
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Left Sidebar -->
        <div class="left-sidebar">
            <a href="?show=friends"><button class="friends-button">Friends</button></a><br>
            <a href="?show=profile"><button class="friends-button">My Profile</button></a> <br>
            <a href="?show=feeds"><button class="friends-button">Feeds</button></a>
        </div>

        <!-- Posts Section -->
        <?php if ($show_feeds): ?>
            <div class="posts-container">
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
            </div>
        <?php endif; ?>

        <!-- Friends Section -->
        <?php if ($show_friends): ?>
            <div class="right-sidebar">
                <?php
                if ($result_friends->num_rows > 0) {
                    while ($row = $result_friends->fetch_assoc()) {
                        echo '<div>';
                        if ($row['image_url']) {
                            echo '<img src="' . $row['image_url'] . '" alt="Friend Image">';
                        }
                        echo '<p>' . $row['friend_name'] . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No friends found.</p>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>