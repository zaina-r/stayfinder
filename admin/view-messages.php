<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit();
}

// Fetch all submitted messages
$sql = "SELECT * FROM contactus ORDER BY sender_id DESC";
$result = mysqli_query($connect, $sql);

// Remove a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_message'])) {
    $sender_id = $_POST['sender_id'];
    $delete_query = "DELETE FROM contactus WHERE sender_id = '$sender_id'";
    if (mysqli_query($connect, $delete_query)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error removing message: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submitted Messages</title>
    <link rel="stylesheet" href="admin-css/messages-style.css">
    <link rel="stylesheet" href="admin-css/header.css">
    <link rel="stylesheet" href="admin-css/footer.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo-nav">
                <a href="dashboard.php"><img src="../src-image/logo-white.png" alt="site-logo" class="logo"></a>
                <button onclick="window.location.href='dashboard.php'" class="head-button" type="button"><span class="span-class"></span>Admin Dashboard</button>
            </div>
            
            <div class="user-info">
            <button onclick="window.location.href='../logout.php'" class="head-button" type="button"><span class="span-class"></span>Log Out</button>
            </div>
        </div>
    </header>
    <div class="container">
        <h1>Submitted Messages</h1>

        <table class="message-table">
            <tr>
                <th>Sender ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['sender_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars(substr($row['message'], 0, 50)) . '...'; ?></td>
                    <td>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="sender_id" value="<?php echo $row['sender_id']; ?>">
                            <button type="submit" name="remove_message" class="remove-button">Remove</button>
                        </form>
                        <button class="view-message" onclick="openModal('<?php echo htmlspecialchars($row['message']); ?>')">View Message</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Modal for displaying message -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Message Details</h2>
            <p id="modalMessageContent"></p>
        </div>
    </div>

    <script>
        // JavaScript to handle modal
        function openModal(message) {
            document.getElementById("modalMessageContent").textContent = message;
            document.getElementById("messageModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("messageModal").style.display = "none";
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("messageModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
<?php include "footer.php"; ?> 
</html>

<?php 
mysqli_close($connect);
?>
