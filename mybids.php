<?php
include_once("header.php");
require("utilities.php");
require("database.php");

$has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
if (!$has_session) {
  echo ('<div class="text-center p-8 text-red-600 font-medium">Please Login.</div>');
  header("location: login.php");
  exit;
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <h2 class="text-3xl font-bold text-gray-900 mb-6">My Bids</h2>

  <?php
  $user_id = $_SESSION['user_id'];
  // Establish database connection
  $connection = db_connect();

  // Query to get user's bids
  $auction_query = "SELECT 
                    U.user_id,
                    B.price,
                    B.time_of_bid,
                    I.item_id,
                    I.name AS item_name,
                    I.description AS item_description,
                    I.colour AS item_colour,
                    I.condition AS item_condition,
                    I.category As item_category,
                    I.photo AS item_photo,
                    A.auction_id,
                    A.start_time AS auction_start_time,
                    A.end_time AS auction_end_time,
                    A.auction_title,
                    A.reserve_price,
                    A.starting_price,
                    (SELECT MAX(B2.price) FROM Bids B2 WHERE B2.auction_id = A.auction_id) AS highest_bid
                    FROM Users U
                    JOIN Bids B ON U.user_id = B.user_id
                    JOIN Auction A ON B.auction_id = A.auction_id
                    JOIN Item I ON A.item_id = I.item_id
                    WHERE U.user_id = '$user_id'
                    ORDER BY B.time_of_bid DESC";
  $auction_result = db_query($connection, $auction_query);

  // Check if bids exist
  if (db_num_rows($auction_result) == 0) {
    echo '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center text-yellow-700">
            <p class="text-lg font-medium">You have not placed any bids yet.</p>
            <a href="browse.php" class="mt-4 inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-150">Browse Auctions</a>
          </div>';
    db_disconnect($connection);
    exit;
  }
  ?>

  <div class="space-y-6">
    <?php
    while ($row = mysqli_fetch_assoc($auction_result)) {
      $item_id = $row['item_id'];
      $item_name = $row['item_name'];
      $item_description = $row['item_description'];
      $item_photo = $row['item_photo'];
      $auction_id = $row['auction_id'];
      $auction_reserve_price = $row['reserve_price'];
      $auction_starting_price = $row['starting_price'];
      $auction_title = $row['auction_title'];
      $auction_start_time = $row['auction_start_time'];
      $auction_end_time = $row['auction_end_time'];
      $bid_price = $row['price'];
      $bid_time = $row['time_of_bid'];
      $highest_bid = $row['highest_bid'];

      if (strlen($item_description) > 250) {
        $desc_shortened = substr($item_description, 0, 250) . '...';
      } else {
        $desc_shortened = $item_description;
      }

      // Convert times to DateTime objects
      $format = 'Y-m-d H:i:s';
      $auction_start_time = DateTime::createFromFormat($format, $auction_start_time);
      $auction_end_time = DateTime::createFromFormat($format, $auction_end_time);
      $now = new DateTime();

      if ($now > $auction_end_time) {
        $time_remaining = 'This auction has ended';
        $ended = true;
      } else {
        $time_to_end = date_diff($now, $auction_end_time);
        $time_remaining = display_time_remaining($time_to_end) . ' remaining';
        $ended = false;
      }

      $is_highest_bidder = ($bid_price == $highest_bid);
    ?>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
      <div class="md:flex">
        <div class="md:flex-shrink-0 p-4 flex items-center justify-center bg-gray-50">
          <img src="<?php echo $item_photo; ?>" alt="<?php echo htmlspecialchars($auction_title); ?>" 
               class="h-32 w-32 object-contain">
        </div>
        
        <div class="p-4 md:p-6 md:flex-1">
          <div class="md:flex md:justify-between">
            <div class="md:flex-1">
              <a href="listing.php?item_id=<?php echo urlencode($item_id); ?>" 
                 class="block text-xl font-semibold text-indigo-600 hover:text-indigo-800 transition duration-150 mb-2">
                <?php echo htmlspecialchars($auction_title); ?>
              </a>
              <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($desc_shortened); ?></p>
            </div>
            
            <div class="mt-4 md:mt-0 md:ml-6 md:w-72 flex-shrink-0">
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <div class="mb-2">
                  <span class="block text-sm text-gray-500">Your Bid</span>
                  <span class="text-2xl font-bold text-gray-900">£<?php echo number_format($bid_price, 2); ?></span>
                </div>
                
                <div class="text-sm text-gray-500 mb-2">
                  <span>Placed on <?php echo date("F j, Y, g:i a", strtotime($bid_time)); ?></span>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                  <div>
                    <span class="text-gray-500">Starting Price</span>
                    <div class="font-medium">£<?php echo number_format($auction_starting_price, 2); ?></div>
                  </div>
                  <div>
                    <span class="text-gray-500">Reserve Price</span>
                    <div class="font-medium">£<?php echo number_format($auction_reserve_price, 2); ?></div>
                  </div>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="text-sm font-medium <?php echo $ended ? 'text-red-600' : 'text-blue-600'; ?>">
                    <?php echo $time_remaining; ?>
                  </span>
                  
                  <?php if ($is_highest_bidder): ?>
                    <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                      Highest bid
                    </span>
                  <?php else: ?>
                    <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                      Not highest bid
                    </span>
                  <?php endif; ?>
                </div>
                
                <?php if (!$is_highest_bidder && !$ended): ?>
                  <div class="mt-3">
                    <form action="listing.php?item_id=<?php echo urlencode($item_id); ?>" method="post">
                      <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition duration-150">
                        Place a New Bid
                      </button>
                    </form>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
    }
    db_free_result($auction_result);
    db_disconnect($connection);
    ?>
  </div>
</div>

<?php include_once("footer.php") ?>