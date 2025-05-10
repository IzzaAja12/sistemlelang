<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <h2 class="text-3xl font-bold text-gray-800 mb-6">Lelang yang Saya Menangkan</h2>

  <?php
    $has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    $user_id = $_SESSION['user_id'];

    $connection = db_connect();
    $won_auctions = "SELECT item.item_id,
                            auction.auction_title,
                            item.description,
                            bids.price AS your_bid,
                            item.photo,
                            users.user_id
                     FROM auction
                     JOIN bids ON auction.auction_id = bids.auction_id
                     JOIN item ON auction.item_id = item.item_id
                     JOIN users ON auction.user_id = users.user_id
                     WHERE bids.user_id = '$user_id'
                     AND bids.price = (
                      SELECT MAX(price) AS 'your_bid'
                      FROM bids
                      WHERE auction_id = auction.auction_id
                     )
                     AND auction.end_time < NOW();";
    $won_results = db_query($connection, $won_auctions);
    confirm_result_set($won_results);

    $num_results = db_num_rows($won_results); 
    $results_per_page = 10;
    $max_page = ceil($num_results / $results_per_page);

    function checkRated($raterUserId, $ratedUserId, $soldItem) {
      $connection = db_connect();
      
      $stmt = "SELECT COUNT(*) AS num_ratings
               FROM ratings 
               WHERE rater_user_id = '$raterUserId'
               AND rated_user_id = '$ratedUserId'
               AND item_id = '$soldItem'";

      $rating_call = db_query($connection, $stmt);
      confirm_result_set($rating_call);

      $rating_count = db_fetch_single($rating_call)["num_ratings"];
      db_free_result($rating_call);

      return $rating_count;
    }
    
    function rateSeller($raterUserId, $ratedUserId, $ratingValue, $soldItem) {
      $connection = db_connect();
      $existingRatingCount = checkRated($raterUserId, $ratedUserId, $soldItem);

      if ($existingRatingCount == 0) {
          $stmt = "INSERT INTO ratings (rater_user_id, rated_user_id, rating_value, item_id)
                   VALUES ('$raterUserId', '$ratedUserId', '$ratingValue', '$soldItem')";
          $rate_call = db_query($connection, $stmt);
          confirm_result_set($rate_call);
          db_free_result($rate_call);

          $stmt = "UPDATE users
                   SET total_ratings = total_ratings + 1,
                   average_rating = ((average_rating * total_ratings) + '$ratingValue') / total_ratings
                   WHERE user_id = '$ratedUserId'";
          $rated_call = db_query($connection, $stmt);
          confirm_result_set($rated_call);
          db_free_result($rated_call);
      }
      db_disconnect($connection);
    } 

    function getUserRating($raterUserId, $ratedUserId, $soldItem) {
        $connection = db_connect();
        $stmt = "SELECT rating_value
                 FROM ratings
                 WHERE rater_user_id = '$raterUserId'
                 AND rated_user_id = '$ratedUserId'
                 AND item_id = '$soldItem'";
        $rating_call = db_query($connection, $stmt);
        confirm_result_set($rating_call);

        $rating = db_fetch_single($rating_call);

        $userRating = $rating["rating_value"];

        db_free_result($rating_call);
        db_disconnect($connection);
        
        return $userRating;
    }
  ?>

  <?php if ($num_results == 0): ?>
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-lg text-center">
      <p class="text-lg">Anda belum memenangkan lelang apa pun</p>
    </div>
  <?php else: ?>
    <ul class="space-y-4">
      <?php
        while($row = db_fetch_single($won_results)) {
          $item_photo = $row['photo'];
          $item_id = $row["item_id"];
          $title = $row["auction_title"];
          $description = $row["description"];
          $your_bid = $row["your_bid"];
          $seller_id = $row["user_id"];

          if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
              if (isset($_POST['rate'])) {
                  rateSeller($user_id, $seller_id, $_POST['rate'], $item_id);
              }
          }

          if (strlen($description) > 250) {
              $desc_shortened = substr($description, 0, 250) . '...';
          } else {
              $desc_shortened = $description;
          }
      ?>
        <li class="bg-white rounded-lg shadow-md p-4 border border-gray-100">
          <div class="flex flex-col md:flex-row">
            <!-- Image Section -->
            <div class="md:w-32 flex-shrink-0 mb-4 md:mb-0">
              <img src="<?php echo $item_photo; ?>" alt="<?php echo $title; ?>" 
                   class="w-full h-32 object-cover rounded-lg">
            </div>
            
            <!-- Content Section -->
            <div class="md:ml-6 flex-grow">
              <h3 class="text-xl font-semibold text-gray-800 mb-2">
                <a href="listing.php?item_id=<?php echo $item_id; ?>" class="hover:text-indigo-600 transition">
                  <?php echo $title; ?>
                </a>
              </h3>
              <p class="text-gray-600 mb-4"><?php echo $desc_shortened; ?></p>
              
              <!-- Bottom Info -->
              <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <!-- Bid Info -->
                <div class="mb-4 md:mb-0">
                  <p class="text-sm text-gray-500">Kamu Pemenang Lelang:</p>
                  <p class="text-2xl font-bold text-green-600">Rp <?php echo number_format($your_bid, 2); ?></p>
                </div>
                
                <!-- Rating Section -->
                <div class="w-full md:w-auto">
                  <?php if (checkRated($user_id, $seller_id, $item_id) == 0): ?>
                    <p class="text-sm font-medium text-gray-700 mb-2">Rate this Seller:</p>
                    <form method="post" action="" class="flex items-center space-x-2">
                      <select name="rate" id="rate" 
                              class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5" selected>5</option>
                      </select>
                      <button type="submit" name="submit" 
                              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Submit
                      </button>
                    </form>
                  <?php else: ?>
                    <div class="text-center">
                      <p class="text-sm font-medium text-gray-700">Ratting Penjual:</p>
                      <div class="flex items-center justify-center mt-1">
                        <?php 
                          $seller_rating = getUserRating($user_id, $seller_id, $item_id);
                          for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $seller_rating) {
                              echo '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                            } else {
                              echo '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                            }
                          }
                        ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </li>
      <?php } ?>
    </ul>
  <?php endif; ?>

  <?php
    db_disconnect($connection);
  ?>
</div>