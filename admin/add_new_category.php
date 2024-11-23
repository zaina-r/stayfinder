<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login-registration.php");
    exit();
}

// Fetch current categories
$sql = "SELECT * FROM category";
$data_set = mysqli_query($connect, $sql);

// Update category
if (isset($_POST['update_category'])) {
    $category_id = (int) $_POST['category_id'];
    $new_category_name = trim($_POST['new_category_name']);

    if ($category_id > 0 && !empty($new_category_name)) {
        $stmt = mysqli_prepare($connect, "UPDATE category SET category_name = ? WHERE category_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $new_category_name, $category_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Category updated successfully');</script>";
                header("Location: ./add_new_category.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($connect);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($connect);
        }
    } else {
        echo "<script>alert('Invalid category ID or category name');</script>";
    }
}

// Add new category
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);

    if (!empty($category_name)) {
        $stmt = mysqli_prepare($connect, "INSERT INTO category (category_name) VALUES (?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $category_name);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Category added successfully');</script>";
                header("Location: ./add_new_category.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($connect);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($connect);
        }
    } else {
        echo "<script>alert('Category name cannot be empty');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="admin-css/category-style.css">
    <link rel="stylesheet" href="admin-css/header.css">
    <link rel="stylesheet" href="admin-css/footer.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>

<?php include "header.php"; ?>
    <div class="container">
        <div class="container-head">
            <h1>Manage Categories</h1>
            <button class="add-button" onclick="openAddModal()">Add New Category</button>
        </div>
        <!-- Display Existing Categories -->
        <div class="table-div">
            <table class="category-table">
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($data_set)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td>
                            <!-- Update Category Button -->
                            <button class="update-button" onclick="openUpdateModal('<?php echo $row['category_id']; ?>', '<?php echo htmlspecialchars($row['category_name']); ?>')">Update</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- Modal for Adding Category -->
        <div id="addCategoryModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h2>Add New Category</h2>
                <form action="" method="post">
                    <label for="category_name">Category Name:</label>
                    <input type="text" name="category_name" id="category_name" required>
                    <button type="submit" name="add_category" class="remove-button">Add Category</button>
                </form>
            </div>
        </div>

        <!-- Modal for Updating Category -->
        <div id="updateCategoryModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeUpdateModal()">&times;</span>
                <h2>Update Category</h2>
                <form action="" method="post">
                    <input type="hidden" name="category_id" id="update_category_id">
                    <label for="new_category_name">New Category Name:</label>
                    <input type="text" name="new_category_name" id="new_category_name" required>
                    <button type="submit" name="update_category" class="submit-button" class="remove-button">Update Category</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to handle modals
        function openAddModal() {
            document.getElementById("addCategoryModal").style.display = "block";
        }

        function closeAddModal() {
            document.getElementById("addCategoryModal").style.display = "none";
        }

        function openUpdateModal(categoryId, categoryName) {
            document.getElementById("updateCategoryModal").style.display = "block";
            document.getElementById("update_category_id").value = categoryId;
            document.getElementById("new_category_name").value = categoryName;
        }

        function closeUpdateModal() {
            document.getElementById("updateCategoryModal").style.display = "none";
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const addModal = document.getElementById("addCategoryModal");
            const updateModal = document.getElementById("updateCategoryModal");
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
