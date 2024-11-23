<?php
error_reporting(E_ALL);
ini_set('display_error', 1);

include '../dbconnect.php';
session_start();

if (!isset($_GET['ad_id'])) {
    echo "Invalid request.";
    exit;
}

$adId = $_GET['ad_id'];

// Fetch ad details
$sqlAdDetails = "SELECT listings.*, 
                    district.district_name, 
                    category.category_name, 
                    nearestuni.uni_name, 
                    user.first_name AS advertiser_name, 
                    user.email AS advertiser_email, 
                    user.contact_no AS advertiser_contact
                 FROM listings
                 LEFT JOIN district ON listings.district_id = district.district_id
                 LEFT JOIN category ON listings.category_id = category.category_id
                 LEFT JOIN nearestuni ON listings.nearestuni_id = nearestuni.uni_id
                 LEFT JOIN user ON listings.advertiser_id = user.user_id
                 WHERE listings.ad_id = ?";
$stmt = mysqli_prepare($connect, $sqlAdDetails);
mysqli_stmt_bind_param($stmt, 's', $adId);
mysqli_stmt_execute($stmt);
$resultAdDetails = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultAdDetails) == 0) {
    echo "No ad found.";
    exit;
}

$ad = mysqli_fetch_assoc($resultAdDetails);

// Fetch images for the ad
$sqlImages = "SELECT image_name FROM listing_images WHERE ad_id = ?";
$stmtImages = mysqli_prepare($connect, $sqlImages);
mysqli_stmt_bind_param($stmtImages, 's', $adId);
mysqli_stmt_execute($stmtImages);
$resultImages = mysqli_stmt_get_result($stmtImages);
$images = [];
while ($row = mysqli_fetch_assoc($resultImages)) {
    $images[] = $row['image_name'];
}

// Fetch comments and ratings for the ad
$sqlComments = "SELECT comment.*, user.first_name, user.last_name FROM comment 
                LEFT JOIN user ON comment.user_id = user.user_id 
                WHERE comment.ad_id = ? ORDER BY comment.comment_date DESC";
$stmtComments = mysqli_prepare($connect, $sqlComments);
mysqli_stmt_bind_param($stmtComments, 's', $adId);
mysqli_stmt_execute($stmtComments);
$resultComments = mysqli_stmt_get_result($stmtComments);

// Calculate average rating
$sqlAvgRating = "SELECT AVG(CAST(rating AS UNSIGNED)) AS avg_rating FROM comment WHERE ad_id = ?";
$stmtAvgRating = mysqli_prepare($connect, $sqlAvgRating);
mysqli_stmt_bind_param($stmtAvgRating, 's', $adId);
mysqli_stmt_execute($stmtAvgRating);
$resultAvgRating = mysqli_stmt_get_result($stmtAvgRating);
$avgRatingRow = mysqli_fetch_assoc($resultAvgRating);
$avgRating = round($avgRatingRow['avg_rating'], 1);

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $comment = trim($_POST['comment']);
    $rating = isset($_POST['rating']) ? (string)$_POST['rating'] : null;

    if (!empty($comment) && in_array($rating, ['1', '2', '3', '4', '5'])) {
        $sqlInsertComment = "INSERT INTO comment (comment, rating, comment_date, user_id, ad_id) VALUES (?, ?, NOW(), ?, ?)";
        $stmtInsertComment = mysqli_prepare($connect, $sqlInsertComment);
        mysqli_stmt_bind_param($stmtInsertComment, 'ssis', $comment, $rating, $userId, $adId);
        mysqli_stmt_execute($stmtInsertComment);
        header("Location: moredetails.php?ad_id=$adId");
        exit();
    } else {
        $errorMsg = "Please provide a valid comment and rating (1-5).";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($ad['title']); ?> - Details</title>
    <link rel="stylesheet" href="seeker-styles/moredetails.css">
    <link rel="stylesheet" href="seeker-styles/header.css">
    <link rel="stylesheet" href="seeker-styles/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
    <?php include "header.php"; ?>
    
    <main>
        <div class="ad-details">
            <div class="image-section">
                <div class="slideshow-container">
                    <?php foreach ($images as $index => $imageName): ?>
                        <div class="mySlides fade">
                            <div class="numbertext"><?php echo $index + 1; ?> / <?php echo count($images); ?></div>
                            <img src="../uploads/listings_images/<?php echo htmlspecialchars($imageName); ?>" style="width:100%">
                        </div>
                    <?php endforeach; ?>
                    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                    <a class="next" onclick="plusSlides(1)">&#10095;</a>
                </div>
                <br>
                <div style="text-align:center">
                    <?php for ($i = 1; $i <= count($images); $i++): ?>
                        <span class="dot" onclick="currentSlide(<?php echo $i; ?>)"></span>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="info-section">
                <h2><?php echo htmlspecialchars($ad['title']); ?></h2>
                <p>Average Rating: <?php echo str_repeat('⭐', (int)$avgRating); ?></p>
                <div class="ad-info">
                    <?php 
                        $availability = strtolower(str_replace(' ', '-', trim($ad['availability'])));
                    ?>
                    <p>
                        <strong>Availability :</strong> 
                        <span class="availability <?php echo ($availability === 'available') ? 'available' : 'not-available'; ?>">
                            <?php echo htmlspecialchars($ad['availability']); ?>
                        </span>
                    </p>
                    <p><strong>Description:</strong><br> <?php echo htmlspecialchars($ad['description']); ?></p>
                    <p><strong>Price:</strong><br> Rs <?php echo number_format($ad['price']); ?> / Monthly</p>
                    <p><strong>Location:</strong><br> <?php echo htmlspecialchars($ad['address_no'] . ', ' . $ad['address_street'] . ', ' . $ad['address_city']); ?></p>
                    <p><strong>Category:</strong><br> <?php echo htmlspecialchars($ad['category_name']); ?></p>
                    <p><strong>District:</strong><br> <?php echo htmlspecialchars($ad['district_name']); ?></p>
                    <p><strong>Nearest University:</strong><br> <?php echo htmlspecialchars($ad['uni_name']); ?></p>
                    <button id="showContactBtn" onclick="showContactInfo()">Show Owner Contact Info &nbsp  <i class='fas fa-angle-double-down'></i></button>
                </div>
                
                <div id="contactInfo" style="display: none;">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="owner-info">
                            <p><strong>Name:</strong><br> <?php echo htmlspecialchars($ad['advertiser_name']); ?></p>
                            <p><strong>Email:</strong><br> <?php echo htmlspecialchars($ad['advertiser_email']); ?></p>
                            <p><strong>Contact Number:</strong><br> <?php echo htmlspecialchars($ad['advertiser_contact']); ?></p>
                            <button onclick="window.location.href='ownerprofile.php?user_id=<?php echo $ad['advertiser_id']; ?>'">View Owner Profile</button>
                        </div>
                    <?php else: ?>
                        <div id="loginModal-2" class="modal-popup-2">
                            <div class="modal-content-2">
                                <div class="message"><p>You must be logged in to view contact information.</p></div>
                                <button class="cancel-btn" onclick="closeLoginPopup2()">Cancel</button>
                                <button class="login-btn" onclick="redirectToLogin2()">Log In</button>    
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <h3>Comments</h3>
            <hr>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" class="comment-form">
                    <label for="rating">Rating (1-5):</label>
                    <select name="rating" id="rating" >
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <label for="comment">Comment:</label>
                    <textarea name="comment" id="comment" rows="3" required></textarea>
                    <button type="submit">Submit</button>
                    <?php if (isset($errorMsg)): ?>
                        <p class="error-message"><?php echo htmlspecialchars($errorMsg); ?></p>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <p>Please <a href="../login-registration.php">log in</a> to add a comment.</p>
            <?php endif; ?>
            <hr>
            <div class="existing-comments">
                <?php while ($comment = mysqli_fetch_assoc($resultComments)): ?>
                    <div class="comment">
                        <p><strong><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></strong> (<?php echo str_repeat('⭐', $comment['rating']); ?>)</p>
                        <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                        <small>Posted on <?php echo htmlspecialchars($comment['comment_date']); ?></small>
                    </div>
                <?php endwhile; ?>
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
    <script src="seeker-js/popup.js"></script>
    <script src="seeker-js/moredetails.js"></script>
    <script src="seeker-js/dropdown.js"></script>
    
    <?php include "footer.php"; ?>
</body>
</html>
