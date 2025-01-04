
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Social";
function OpenCon()
{
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
function CloseCon($conn)
{
    $conn->close();
}
INSERT INTO posts (username, content, image_url) VALUES 
('Lione Messi', 'Messi is the GOAT', './assets/images/AA1qzbvd.jpeg'),
('Lonel Messi', 'An Argentine international, Messi is the national team\'s all-time leading goalscorer and most-capped player.', NULL),
('Lionel Messi', 'Exploring the mountains!', './assets/images/leo.jpg');