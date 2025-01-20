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

// Validate search input
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    if (!empty($search_query)) {
        $stmt = $conn->prepare("SELECT * FROM friends WHERE friend_name LIKE ?");
        $search_param = '%' . $search_query . '%';
        $stmt->bind_param("s", $search_param);
        $stmt->execute();
        $search_results = $stmt->get_result();

        if ($search_results === false) {
            die("Error executing query: " . $stmt->error);
        }
    }
}

// Load posts from JSON file
$posts_file = 'data/posts.json';
$posts = file_exists($posts_file) ? json_decode(file_get_contents($posts_file), true) : [];

// Fetch posts from the database
$sql_posts = "SELECT * FROM posts ORDER BY created_at DESC"; // Assuming you have a 'posts' table
$result_posts = $conn->query($sql_posts);

// Fetch friends from the database 
$sql_friends = "SELECT * FROM friends";
$result_friends = $conn->query($sql_friends);

// Close database connection
CloseCon($conn);

// Combine posts from the database and JSON
if ($result_posts->num_rows > 0) {
    while ($row = $result_posts->fetch_assoc()) {
        $posts[] = [
            'username' => $row['username'],
            'content' => $row['content'],
            'image_url' => $row['image_url'],
            'created_at' => $row['created_at'],
        ];
    }
}

// Determine which content to show based on the button clicked
$show_friends = isset($_GET['show']) && $_GET['show'] === 'friends';
$show_feeds = isset($_GET['show']) && $_GET['show'] === 'feeds';
$show_profile = isset($_GET['show']) && $_GET['show'] === 'profile';

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post']) && $show_profile) {
    $post_content = $_POST['post_content'];
    $image_url = ''; // Default to empty if no image is uploaded

    // Check if an image was uploaded
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['post_image']['tmp_name'];
        $image_name = basename($_FILES['post_image']['name']);
        $upload_dir = 'uploads/'; // Directory to store uploaded images

        // Ensure the upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Define the path to store the uploaded image
        $image_path = $upload_dir . $image_name;

        // Move the uploaded image to the directory
        if (move_uploaded_file($image_tmp_name, $image_path)) {
            $image_url = $image_path; // Save the image URL in the post
        } else {
            // Handle error if image upload fails
            echo "Failed to upload image.";
        }
    }

    // Save the new post to the JSON file
    $new_post = [
        'username' => $profile_data['name'],
        'content' => $post_content,
        'image_url' => $image_url,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    $posts[] = $new_post;

    // Save posts to JSON file
    file_put_contents($posts_file, json_encode($posts, JSON_PRETTY_PRINT));
}
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
            <img src="<?= htmlspecialchars($profile_data['img']); ?>" alt="Profile Image" style="width: 100px; height: 100px; border-radius: 50%; border: 2px solid #1fa387;" class="profileimage">
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

        </div>

        <!-- New Post Section -->
        <div class="new-post">
            <h3>Create a New Post</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <textarea name="post_content" placeholder="What's on your mind?" required></textarea>
                <input type="file" name="post_image" accept="image/*">
                <button type="submit" name="submit_post">Post</button>
            </form>
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

        <!-- Posts Section (Only for Feeds) -->
        <?php if ($show_feeds): ?>
            <div class="posts-container">
                <?php
                if (!empty($posts)) {
                    foreach ($posts as $post) {
                        echo '<div class="post">';
                        echo '<h3>' . htmlspecialchars($post['username']) . '</h3>';
                        if (!empty($post['image_url'])) {
                            echo '<img src="' . htmlspecialchars($post['image_url']) . '" alt="Post Image">';
                        }
                        echo '<p>' . htmlspecialchars($post['content']) . '</p>';
                        echo '<small>' . htmlspecialchars($post['created_at']) . '</small>';
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

        <!-- Friends Section (Only for Friends) -->
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