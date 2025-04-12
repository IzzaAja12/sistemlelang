<?php 
include_once("header.php");
require("utilities.php");
require("database.php");

$has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
if (!$has_session) {
  echo '<div class="p-4 text-center text-red-600">Please login to view your listings.</div>';
  header("location: login.php");
  exit;
}
?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold text-gray-800 mb-6">My Listings</h1>
  
  <?php
  $user_id = $_SESSION['user_id'];
  $connection = db_connect();
  
  $auction_query = "SELECT auction.auction_title,
                    auction.start_time,
                    auction.end_time,
                    auction.reserve_price,
                    auction.starting_price,
                    auction.auction_id,
                    item.item_id,
                    item.name,
                    item.description,
                    item.photo
                    FROM auction
                    INNER JOIN item ON auction.item_id = item.item_id
                    WHERE auction.user_id = '$user_id'";
  $auction_result = db_query($connection, $auction_query);

  if (db_num_rows($auction_result) == 0) {
    echo '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center text-blue-800">
            You don\'t have any active listings yet. <a href="create_auction.php" class="text-indigo-600 hover:underline">Create your first auction</a>.
          </div>';
    db_disconnect($connection);
    exit;
  }
  ?>

  <div class="space-y-4">
    <?php
    while ($row = mysqli_fetch_assoc($auction_result)) {
      $item_id = $row['item_id'];
      $auction_id = $row['auction_id'];
      $item_name = $row['name'];
      $item_description = $row['description'];
      $item_photo = $row['photo'];
      $auction_title = $row['auction_title'];
      $auction_reserve_price = $row['reserve_price'];
      $auction_starting_price = $row['starting_price'];
      $auction_start_time = $row['start_time'];
      $auction_end_time = $row['end_time'];

      // Shorten description if needed
      $desc_shortened = (strlen($item_description) > 250 
                        ? substr($item_description, 0, 250) . '...' 
                        : $item_description);

      // Format dates
      $format = 'Y-m-d H:i:s';
      $auction_start_time = DateTime::createFromFormat($format, $auction_start_time);
      $auction_end_time = DateTime::createFromFormat($format, $auction_end_time);

      // Calculate time remaining
      $now = new DateTime();
      $is_ended = ($now > $auction_end_time);
      $time_remaining = $is_ended 
                        ? 'This auction has ended' 
                        : display_time_remaining(date_diff($now, $auction_end_time)) . ' remaining';
    ?>
    
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
      <div class="md:flex">
        <!-- Item Image -->
        <div class="md:flex-shrink-0 p-4">
          <img class="h-48 w-full object-contain md:w-48 " src="<?php echo htmlspecialchars($item_photo); ?>" alt="<?php echo htmlspecialchars($auction_title); ?>">
        </div>
        
        <!-- Item Details -->
        <div class="p-6 flex-1">
          <div class="flex flex-col md:flex-row md:justify-between">
            <div class="flex-1">
              <a href="listing.php?item_id=<?php echo $item_id; ?>" class="block mt-1 text-xl font-semibold text-indigo-600 hover:text-indigo-800">
                <?php echo htmlspecialchars($auction_title); ?>
              </a>
              <p class="mt-2 text-gray-600"><?php echo htmlspecialchars($desc_shortened); ?></p>
            </div>
            
            <!-- Price and Time Info -->
            <div class="mt-4 md:mt-0 md:ml-6 md:text-right">
              <div class="text-lg font-medium text-gray-900">
                <span class="text-sm font-normal text-gray-500">Starting Price</span><br>
                £<?php echo number_format($auction_starting_price, 2); ?>
              </div>
              <div class="mt-2 text-sm text-gray-500">
                Reserve: £<?php echo number_format($auction_reserve_price, 2); ?>
              </div>
              <div class="mt-2 <?php echo $is_ended ? 'text-red-600' : 'text-green-600'; ?>">
                <?php echo $time_remaining; ?>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="mt-4 flex space-x-3">
            <a href="listing.php?item_id=<?php echo $item_id; ?>" class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors">
              View Listing
            </a>
            <a href="edit_auction.php?auction_id=<?php echo $auction_id; ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
              Edit
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <?php
    }
    db_disconnect($connection);
    ?>
  </div>
</div>

<?php include_once("footer.php"); ?>