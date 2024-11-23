<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login-registration.php");
    exit();
}

// Retrieve pending approvals
$sqlPending = "SELECT listings.*, user.user_id, user.first_name AS advertiser_name, district.district_name, category.category_name, nearestuni.uni_name
        FROM listings 
        LEFT JOIN user ON listings.advertiser_id = user.user_id
        LEFT JOIN district ON listings.district_id = district.district_id
        LEFT JOIN category ON listings.category_id = category.category_id
        LEFT JOIN nearestuni ON listings.nearestuni_id = nearestuni.uni_id
        WHERE listings.approval_status = 'pending'";
$dataSetPending = mysqli_query($connect, $sqlPending);

// Approve listing
if (isset($_POST['approved'])) {
    $ad_id = $_POST['ad_id'];

    $stmt = mysqli_prepare($connect, "UPDATE listings SET approval_status = 'Approved', approval_date = CURRENT_DATE WHERE ad_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $ad_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: ./approve_post.php");
            exit();
        } else {
            echo "Error approving listing: " . mysqli_error($connect);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($connect);
    }
}

// Deny listing
if (isset($_POST['denied'])) {
    $ad_id = $_POST['ad_id'];

    $stmt = mysqli_prepare($connect, "UPDATE listings SET approval_status = 'Denied' WHERE ad_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $ad_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: ./approve_post.php");
            exit();
        } else {
            echo "Error denying listing: " . mysqli_error($connect);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($connect);
    }
}

// Fetch all posted ads
$sqlAllAds = "SELECT listings.*, user.user_id, user.first_name AS advertiser_name, district.district_name, category.category_name, nearestuni.uni_name
        FROM listings 
        LEFT JOIN user ON listings.advertiser_id = user.user_id
        LEFT JOIN district ON listings.district_id = district.district_id
        LEFT JOIN category ON listings.category_id = category.category_id
        LEFT JOIN nearestuni ON listings.nearestuni_id = nearestuni.uni_id";
$dataSetAllAds = mysqli_query($connect, $sqlAllAds);

// Remove ad
if (isset($_POST['remove_ad'])) {
    $ad_id = $_POST['ad_id'];

    // Start a transaction to safely delete with foreign key dependencies
    mysqli_begin_transaction($connect);

    try {
        // Delete from related tables first due to foreign key constraints
        $deleteImages = "DELETE FROM listing_images WHERE ad_id = ?";
        $stmtImages = mysqli_prepare($connect, $deleteImages);
        if ($stmtImages) {
            mysqli_stmt_bind_param($stmtImages, "s", $ad_id);
            mysqli_stmt_execute($stmtImages);
            mysqli_stmt_close($stmtImages);
        }

        $deleteComments = "DELETE FROM comment WHERE ad_id = ?";
        $stmtComments = mysqli_prepare($connect, $deleteComments);
        if ($stmtComments) {
            mysqli_stmt_bind_param($stmtComments, "s", $ad_id);
            mysqli_stmt_execute($stmtComments);
            mysqli_stmt_close($stmtComments);
        }

        $deleteFavorites = "DELETE FROM favorites WHERE ad_id = ?";
        $stmtFavorites = mysqli_prepare($connect, $deleteFavorites);
        if ($stmtFavorites) {
            mysqli_stmt_bind_param($stmtFavorites, "s", $ad_id);
            mysqli_stmt_execute($stmtFavorites);
            mysqli_stmt_close($stmtFavorites);
        }

        // Now delete the main ad entry
        $stmt = mysqli_prepare($connect, "DELETE FROM listings WHERE ad_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $ad_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Commit the transaction
        mysqli_commit($connect);
        header("Location: ./approve_post.php?message=Ad removed successfully!");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($connect);
        echo "Error removing ad: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals & All Ads</title>
    <link rel="stylesheet" href="admin-css/pending-approval.css">
    <link rel="stylesheet" href="admin-css/header.css">
    <link rel="stylesheet" href="admin-css/footer.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>

<?php include "header.php"; ?>
    
    <div class="container">
    <h2>Pending Approvals</h2>
    <table class="approval-table">
        <tr>
            <th>Title</th>
            <th>Advertiser ID</th>
            <th>Category</th>
            <th>District</th>
            <th>Nearest University</th>
            <th>Address</th>
            <th>City</th>
            <th>Price</th>
            <th>View Images</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($dataSetPending)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                <td><?php echo htmlspecialchars($row['uni_name']); ?></td>
                <td><?php echo htmlspecialchars($row['address_no'] . " " . $row['address_street']); ?></td>
                <td><?php echo htmlspecialchars($row['address_city']); ?></td>
                <td><?php echo number_format($row['price']); ?></td>
                <td>
                    <form action="view_images.php" method="get" style="display:inline;">
                        <input type="hidden" name="ad_id" value="<?php echo htmlspecialchars($row['ad_id']); ?>">
                        <button type="submit" class="view-images-button">View Images</button>
                    </form>
                </td>
                <td>
                    <div class="buttons">
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="ad_id" value="<?php echo htmlspecialchars($row['ad_id']); ?>">
                            <button type="submit" name="approved" class="approve-button">Approve</button>
                        </form>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="ad_id" value="<?php echo htmlspecialchars($row['ad_id']); ?>">
                            <button type="submit" name="denied" class="deny-button">Deny</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>All Posted Ads</h2>
    <table class="all-ads-table">
        <tr>
            <th>Title</th>
            <th>Advertiser ID</th>
            <th>Category</th>
            
            <th>Nearest University</th>
            
            <th>City</th>
            <th>Price</th>
            <th>View Images</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($dataSetAllAds)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                
                <td><?php echo htmlspecialchars($row['uni_name']); ?></td>
             
                <td><?php echo htmlspecialchars($row['address_city']); ?></td>
                <td><?php echo number_format($row['price']); ?></td>
                <td>
                    <form action="view_images.php" method="get" style="display:inline;">
                        <input type="hidden" name="ad_id" value="<?php echo htmlspecialchars($row['ad_id']); ?>">
                        <button type="submit" class="view-images-button">View Images</button>
                    </form>
                </td>
                <td>
                    <form action="" method="post" style="display:inline;">
                        <input type="hidden" name="ad_id" value="<?php echo htmlspecialchars($row['ad_id']); ?>">
                        <button type="submit" name="remove_ad" class="remove-button" onclick="return confirm('Are you sure you want to remove this ad?');">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    </div>

    <?php include "footer.php"; ?>   
    <script src="admin-js/dropdown.js"></script>

</body>
</html>

<?php 
mysqli_close($connect);
?>
