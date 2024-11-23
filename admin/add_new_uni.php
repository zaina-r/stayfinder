<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login-registration.php");
    exit();
}

// Fetch current universities
$sql = "SELECT * FROM nearestuni";
$data_set = mysqli_query($connect, $sql);

// Add new university
if (isset($_POST['add_university'])) {
    $uni_name = trim($_POST['uni_name']);
    
    $stmt = mysqli_prepare($connect, "INSERT INTO nearestuni (uni_name) VALUES (?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $uni_name);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: ./add_new_uni.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($connect);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($connect);
    }
}

// Update university
if (isset($_POST['update_university'])) {
    $uni_id = (int) $_POST['uni_id'];
    $new_uni_name = trim($_POST['new_uni_name']);

    if ($uni_id > 0 && !empty($new_uni_name)) {
        $stmt = mysqli_prepare($connect, "UPDATE nearestuni SET uni_name = ? WHERE uni_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $new_uni_name, $uni_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('University updated successfully');</script>";
                header("Location: ./add_new_uni.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($connect);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($connect);
        }
    } else {
        echo "<script>alert('Invalid university ID or university name');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Universities</title>
    <link rel="stylesheet" href="admin-css/university-style.css">
    <link rel="stylesheet" href="admin-css/header.css">
    <link rel="stylesheet" href="admin-css/footer.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
<?php include "header.php"; ?>
    <div class="container">
        <div class="container-head">
            <h1>Manage Universities</h1>
            <button class="add-button" onclick="openAddModal()">Add New University</button>
        </div>
        <!-- Display Existing Universities -->
        <div class="table-div">
            <table class="university-table">
                <tr>
                    <th>University ID</th>
                    <th>University Name</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($data_set)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['uni_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['uni_name']); ?></td>
                        <td>
                            <!-- Update University Button -->
                            <button class="update-button" onclick="openUpdateModal('<?php echo $row['uni_id']; ?>', '<?php echo htmlspecialchars($row['uni_name']); ?>')">Update</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- Modal for Adding University -->
        <div id="addUniversityModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h2>Add New University</h2>
                <form action="" method="post">
                    <label for="uni_name">University Name:</label>
                    <input type="text" name="uni_name" id="uni_name" required>
                    <button type="submit" name="add_university" class="submit-button">Add University</button>
                </form>
            </div>
        </div>

        <!-- Modal for Updating University -->
        <div id="updateUniversityModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeUpdateModal()">&times;</span>
                <h2>Update University</h2>
                <form action="" method="post">
                    <input type="hidden" name="uni_id" id="update_uni_id">
                    <label for="new_uni_name">New University Name:</label>
                    <input type="text" name="new_uni_name" id="new_uni_name" required>
                    <button type="submit" name="update_university" class="submit-button">Update University</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to handle modals
        function openAddModal() {
            document.getElementById("addUniversityModal").style.display = "block";
        }

        function closeAddModal() {
            document.getElementById("addUniversityModal").style.display = "none";
        }

        function openUpdateModal(uniId, uniName) {
            document.getElementById("updateUniversityModal").style.display = "block";
            document.getElementById("update_uni_id").value = uniId;
            document.getElementById("new_uni_name").value = uniName;
        }

        function closeUpdateModal() {
            document.getElementById("updateUniversityModal").style.display = "none";
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const addModal = document.getElementById("addUniversityModal");
            const updateModal = document.getElementById("updateUniversityModal");
            if (event.target === addModal) {
                addModal.style.display = "none";
            }
            if (event.target === updateModal) {
                updateModal.style.display = "none";
            }
        }
    </script>
    <?php include "footer.php"; ?>   
    <script src="admin-js/dropdown.js"></script>
</body>
</html>

<?php 
mysqli_close($connect);
?>
