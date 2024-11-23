<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../dbconnect.php';
session_start();

// Handle "Add to Favorites" action
if (isset($_SESSION['user_id']) && isset($_GET['favorite'])) {
    $adId = $_GET['favorite'];
    $userId = (int)$_SESSION['user_id'];

    // Check if the ad is already in favorites
    $checkFavoriteQuery = "SELECT * FROM favorites WHERE user_id = ? AND ad_id = ?";
    if ($stmt = mysqli_prepare($connect, $checkFavoriteQuery)) {
        mysqli_stmt_bind_param($stmt, "is", $userId, $adId);
        mysqli_stmt_execute($stmt);
        $resultCheck = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultCheck) == 0) {
            // Add to favorites if not already added
            $insertFavoriteQuery = "INSERT INTO favorites (user_id, ad_id) VALUES (?, ?)";
            if ($stmtInsert = mysqli_prepare($connect, $insertFavoriteQuery)) {
                mysqli_stmt_bind_param($stmtInsert, "is", $userId, $adId);
                if (mysqli_stmt_execute($stmtInsert)) {
                    $_SESSION['message'] = "Ad added to favorites.";
                    $_SESSION['message_type'] = "success";
                    header("Location: viewads.php");
                    exit;
                } else {
                    $_SESSION['message'] = "An error occurred. Please try again.";
                    $_SESSION['message_type'] = "error";
                    header("Location: viewads.php");
                    exit;
                }
            }
        } else {
            $_SESSION['message'] = "Ad is already in your favorites.";
            $_SESSION['message_type'] = "info";
            header("Location: viewads.php");
            exit;
        }
    }
}

// Fetch data for filters
$fetchdistrict = "SELECT district_id, district_name FROM district";
$locationOptions = mysqli_query($connect,$fetchdistrict );

$fetchcategory = "SELECT category_id, category_name FROM category";
$categoryOptions = mysqli_query($connect, $fetchcategory);

$fetchuni = "SELECT uni_id, uni_name FROM nearestuni";
$universityOptions = mysqli_query($connect, $fetchuni);

// Initialize filters
$whereClauses = ["listings.approval_status = 'approved'"];
if (!empty($_GET['district'])) $whereClauses[] = "listings.district_id = " . (int)$_GET['district'];
if (!empty($_GET['category'])) $whereClauses[] = "listings.category_id = " . (int)$_GET['category'];
if (!empty($_GET['university'])) $whereClauses[] = "listings.nearestuni_id = " . (int)$_GET['university'];
if (!empty($_GET['min_price'])) $whereClauses[] = "listings.price >= " . (int)$_GET['min_price'];
if (!empty($_GET['max_price'])) $whereClauses[] = "listings.price <= " . (int)$_GET['max_price'];

// Handle search query
if (!empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($connect, $_GET['search']);
    $whereClauses[] = "(title LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%' OR address_city LIKE '%$searchTerm%' OR category.category_name LIKE '%$searchTerm%')";
}

$whereSQL = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Sorting
$sortOrder = '';
if (!empty($_GET['sort_price'])) {
    $sortOrder = $_GET['sort_price'] == 'asc' ? 'ASC' : 'DESC';
}

// Set default values for ads per page
$defaultLimit = 5;
$limit = isset($_GET['ads_per_page']) ? (int)$_GET['ads_per_page'] : $defaultLimit;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Main SQL query to fetch listings
$sqlListings = "SELECT listings.*, district.district_name, category.category_name, nearestuni.uni_name, 
                (SELECT image_name FROM listing_images WHERE listing_images.ad_id = listings.ad_id ORDER BY image_id LIMIT 1) AS image_name
                FROM listings
                LEFT JOIN district ON listings.district_id = district.district_id
                LEFT JOIN category ON listings.category_id = category.category_id
                LEFT JOIN nearestuni ON listings.nearestuni_id = nearestuni.uni_id
                $whereSQL";
if ($sortOrder) {
    $sqlListings .= " ORDER BY listings.price $sortOrder";
}
$sqlListings .= " LIMIT $limit OFFSET $offset";

$resultListings = mysqli_query($connect, $sqlListings);

if ($resultListings === false) {
    echo "Error fetching listings: " . mysqli_error($connect);
    exit;
}

// Total count for pagination with correct joins
$totalCountQuery = "SELECT COUNT(*) as total FROM listings
    LEFT JOIN district ON listings.district_id = district.district_id
    LEFT JOIN category ON listings.category_id = category.category_id
    LEFT JOIN nearestuni ON listings.nearestuni_id = nearestuni.uni_id
    $whereSQL";

$totalCountResult = mysqli_query($connect, $totalCountQuery);
if ($totalCountResult === false) {
    echo "Error fetching total count: " . mysqli_error($connect);
    exit;
}
$totalCount = mysqli_fetch_assoc($totalCountResult)['total'];
$totalPages = ceil($totalCount / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Ads</title>
    <link rel="stylesheet" href="seeker-styles/viewads.css">
    <link rel="stylesheet" href="seeker-styles/header.css">
    <link rel="stylesheet" href="seeker-styles/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
    <?php include "header.php"; ?>
    
    <?php
    // Display feedback messages and clear them after displaying
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message_type'] ?? 'info'; // Default to 'info' if not set
        echo "<p style='color: " . ($messageType === 'success' ? 'green' : ($messageType === 'error' ? 'red' : 'orange')) . "; text-align: center;'>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <main>
        <div class="side-filter-bar">
            <form method="GET" action="viewads.php">
                <div class="filter-section">
                    <label>District:</label>
                    <select name="district">
                        <option value="">Select</option>
                        <?php while ($locationRow = mysqli_fetch_assoc($locationOptions)): ?>
                            <option value="<?php echo $locationRow['district_id']; ?>" <?php echo isset($_GET['district']) && $_GET['district'] == $locationRow['district_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($locationRow['district_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-section">
                    <label>Nearest University:</label>
                    <select name="university">
                        <option value="">Select</option>
                        <?php while ($universityRow = mysqli_fetch_assoc($universityOptions)): ?>
                            <option value="<?php echo $universityRow['uni_id']; ?>" <?php echo isset($_GET['university']) && $_GET['university'] == $universityRow['uni_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($universityRow['uni_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-section">
                    <label>Category:</label>
                    <select name="category">
                        <option value="">Select</option>
                        <?php while ($categoryRow = mysqli_fetch_assoc($categoryOptions)): ?>
                            <option value="<?php echo $categoryRow['category_id']; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $categoryRow['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoryRow['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-section">
                    <label>Price:</label>
                    <input type="number" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">
                    <input type="number" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
                </div>
                <div class="filter-section">
                    <button type="submit">Apply Filters</button>
                </div>
            </form>
        </div>

        <div class="top-filter-listings-pagination">
            <div class="top-filter-bar">
                <form method="GET" action="viewads.php">
                    <div class="filter-section-top-left">
                        <label for="ads_per_page">Ads per page:</label>
                        <select name="ads_per_page" onchange="this.form.submit()">
                            <option value="5" <?php echo ($limit == 5) ? 'selected' : ''; ?>>5</option>
                            <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>10</option>
                            <option value="15" <?php echo ($limit == 15) ? 'selected' : ''; ?>>15</option>
                            <option value="20" <?php echo ($limit == 20) ? 'selected' : ''; ?>>20</option>
                        </select>
                    </div>
                    <div class="filter-section-top-right">
                        <label for="sort_price">Sort by Price:</label>
                        <select name="sort_price" onchange="this.form.submit()">
                            <option value="">Select</option>
                            <option value="asc" <?php echo (isset($_GET['sort_price']) && $_GET['sort_price'] == 'asc') ? 'selected' : ''; ?>>Low to High</option>
                            <option value="desc" <?php echo (isset($_GET['sort_price']) && $_GET['sort_price'] == 'desc') ? 'selected' : ''; ?>>High to Low</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="listings">
                <?php if (mysqli_num_rows($resultListings) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($resultListings)): ?>
                        <div class="listing">
                            <div class="image-section">
                                <a href="moredetails.php?ad_id=<?php echo $row['ad_id']; ?>">
                                    <img src="../uploads/listings_images/<?php echo htmlspecialchars($row['image_name'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($row['image_name']); ?>">
                                </a>
                            </div>
                            <div class="info-section">
                                <a href="moredetails.php?ad_id=<?php echo $row['ad_id']; ?>">
                                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                </a>
                                <p>Features: <?php echo htmlspecialchars($row['description']); ?></p>
                                <p>Nearest University: <?php echo htmlspecialchars($row['uni_name']); ?></p>
                                <p>Price: Rs <?php echo number_format($row['price']); ?> / Monthly</p>
                                <button onclick="window.location.href='moredetails.php?ad_id=<?php echo $row['ad_id']; ?>'">More Info</button>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button onclick="window.location.href='viewads.php?favorite=<?php echo $row['ad_id']; ?>'">Add to Favorites</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No listings found.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination Section -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="viewads.php?page=<?php echo $page - 1; ?>&ads_per_page=<?php echo $limit; ?>">&#60; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="viewads.php?page=<?php echo $i; ?>&ads_per_page=<?php echo $limit; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="viewads.php?page=<?php echo $page + 1; ?>&ads_per_page=<?php echo $limit; ?>">Next &#62;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <!-- Modal for Popup -->
    <div id="loginModal" class="modal-popup" style="display: none;">
        <div class="modal-content">
            <div class="message"><p>You have to log in as an Advertiser to post an ad.</p></div>
            <button class="cancel-btn" onclick="closeLoginPopup()">Cancel</button>
            <button class="login-btn" onclick="redirectToLogin()">Log In</button>    
        </div>
    </div>
    <?php include "footer.php"; ?>   
    <script src="seeker-js/dropdown.js"></script>
    <script src="seeker-js/popup.js"></script>
</body>
</html>
