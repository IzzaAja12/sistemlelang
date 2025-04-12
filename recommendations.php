<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Recommendations for You</h2>
    <div class="hidden sm:block">
      <i class="fas fa-lightbulb text-yellow-500 text-xl mr-2"></i>
      <span class="text-gray-600 text-sm">Based on your interests</span>
    </div>
  </div>

  <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg p-4 mb-8 shadow-sm border border-indigo-100">
    <div class="flex items-center">
      <i class="fas fa-info-circle text-indigo-500 mr-3 text-lg"></i>
      <p class="text-gray-700">Items recommended based on what other users with similar interests are watching</p>
    </div>
  </div>

  <?php
    // This page is for showing a buyer recommended items based on their bid 
    // history. It will be pretty similar to browse.php, except there is no 
    // search bar. This can be started after browse.php is working with a database.
    // Feel free to extract out useful functions from browse.php and put them in
    // the shared "utilities.php" where they can be shared by multiple files.
    
    // Check user's credentials (cookie/session).
    $has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    $user_id = $_SESSION['user_id'];

    // Perform a query to pull up auctions they might be interested in.
    $connection = db_connect();
    # the query pulls items from buyers' watchlists who are watching the same items as the current user
    $recommendations = "SELECT DISTINCT w3.item_id, 
                                        auction.auction_title, 
                                        item.description,
                                        MAX(bids.price) AS highest_bid,
                                        COUNT(bids.auction_id) AS num_bids,
                                        auction.end_time, 
                                        item.category                
                        FROM watchlist w1
                        INNER JOIN watchlist w2
                        ON w1.item_id = w2.item_id
                        INNER JOIN watchlist w3
                        ON w2.user_id = w3.user_id
                        RIGHT JOIN auction
                        ON w3.item_id = auction.item_id
                        RIGHT JOIN item 
                        ON w3.item_id = item.item_id
                        RIGHT JOIN bids
                        ON auction.auction_id = bids.auction_id
                        WHERE w1.user_id = '$user_id'
                        AND w2.user_id != '$user_id'
                        AND w1.item_id != w3.item_id
                        GROUP BY w3.item_id,
                                auction.auction_title,
                                auction.end_time";
    $recommended_results = db_query($connection, $recommendations);
    confirm_result_set($recommended_results);

    $num_results = db_num_rows($recommended_results); 
    $results_per_page = 10;
    $max_page = ceil($num_results / $results_per_page);
  ?>

  <div class="space-y-6">
    <?php
    if ($num_results == 0) {
    ?>
      <div class="bg-white rounded-lg shadow-md p-8 text-center border border-gray-200">
        <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Recommendations Available</h3>
        <p class="text-gray-600 mb-4">We don't have any recommendations for you yet. This could be because:</p>
        <ul class="text-left text-gray-600 max-w-md mx-auto space-y-2 mb-6">
          <li class="flex items-start">
            <i class="fas fa-circle text-xs text-gray-400 mt-1.5 mr-2"></i>
            <span>You haven't added items to your watchlist</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-circle text-xs text-gray-400 mt-1.5 mr-2"></i>
            <span>Not enough users with similar interests</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-circle text-xs text-gray-400 mt-1.5 mr-2"></i>
            <span>You might want to explore more categories</span>
          </li>
        </ul>
        <a href="browse.php" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-150">
          <i class="fas fa-search mr-2"></i>
          Browse Items
        </a>
      </div>
    <?php
    } else {
      // Loop through results and print them out as list items.
      while($row = db_fetch_single($recommended_results)) {
        $item_id = $row["item_id"];
        $title = $row["auction_title"];
        $description = $row["description"];
        $current_price = $row["highest_bid"];
        $num_bids = $row["num_bids"];
        $end_date = new DateTime($row["end_time"]);
        print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
      }
    }
    
    db_free_result($recommended_results);
    db_disconnect($connection);
    ?>
  </div>

  <!-- No results found illustration -->
  <?php if ($num_results == 0): ?>
    <div class="flex justify-center mt-8">
      no recommendations found
    </div>
  <?php endif; ?>
</div>