<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php require("database.php") ?>
<?php
$has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_id = $_SESSION['user_id'] ?? null;
$account_type = $_SESSION['account_type'] ?? null;
echo($account_type);
// Get info from the URL:
$item_id = $_GET['item_id'] ?? null;

if (!$item_id) {
  echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-4 mx-auto max-w-2xl'>Error: Item ID is missing.</div>";
  exit;
}

// Establish database connection
$connection = db_connect();

// Fetch item details
$item_query = "SELECT name, description, photo FROM Item WHERE item_id = '$item_id'";
$item_result = db_query($connection, $item_query);

if (db_num_rows($item_result) == 0) {
  echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-4 mx-auto max-w-2xl'>Error: Item not found.</div>";
  db_disconnect($connection);
  exit;
}

$item = db_fetch_single($item_result);
$name = $item['name'];
$description = $item['description'];
$item_photo = $item['photo'];

// Check if the item is part of an auction (CORRECTED QUERY)
$auction_query = "SELECT auction_id, start_time, end_time, auction_title
                  FROM Auction WHERE item_id = '$item_id'";
$auction_result = db_query($connection, $auction_query);
$auction_exists = true;

if ($auction_exists) {
  $auction_data = db_fetch_single($auction_result);
  $auction_id = $auction_data['auction_id'];
  $title = $auction_data['auction_title'];
  $start_time = new DateTime($auction_data['start_time']);
  $end_time = new DateTime($auction_data['end_time']);

  $bid_query = "SELECT b.price AS current_price, u.user_id, u.first_name, u.last_name, COUNT(*) AS num_bids
                FROM Bids b
                INNER JOIN Users u ON b.user_id = u.user_id
                WHERE b.auction_id = '$auction_id'
                AND b.price = (SELECT MAX(price) FROM Bids WHERE auction_id = '$auction_id')
                GROUP BY b.price, u.user_id, u.first_name, u.last_name";

  $bid_result = db_query($connection, $bid_query);
  $bid_data = db_fetch_single($bid_result);
  
  if ($bid_data) {
    $current_price = $bid_data['current_price'] ?: '0.00';
    $current_winner = $bid_data['first_name'] . ' ' . $bid_data['last_name'];
    $num_bids = $bid_data['num_bids'];
  } else {
    $current_price = '0.00';
    $current_winner = 'None';
    $num_bids = 0;
  }

  db_free_result($bid_result);

  $now = new DateTime();
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
  }
} else {
  $title = $name;
}

// Watchlist check
$watching = false;
if ($has_session) {
    $watchlist_query = "SELECT 1
                        FROM Watchlist
                        WHERE user_id = '$user_id' AND
                              item_id = $item_id";
    $watchlist_result = db_query($connection, $watchlist_query);
    $watching = db_num_rows($watchlist_result) > 0;
}

// Clean up
db_free_result($item_result);
if (isset($watchlist_result)) {
  db_free_result($watchlist_result);
}
?>

<!-- Rest of your HTML remains exactly the same -->

<div class="max-w-6xl mx-auto px-4 py-8">
  <!-- Top section with title and watch button -->
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0"><?php echo htmlspecialchars($title); ?></h1>
    
    <?php if ($auction_exists && $account_type == '1'): ?>
      <!-- Watchlist functionality -->
      <?php if ($now < $end_time) : ?>
        <div id="watch_nowatch" class="<?php if ($has_session && $watching) echo 'hidden'; ?>">
          <button type="button" onclick="addToWatchlist()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Tambahkan ke daftar pantauan
          </button>
        </div>
        <div id="watch_watching" class="<?php if (!$has_session || !$watching) echo 'hidden'; ?>">
          <div class="flex space-x-2">
            <button type="button" disabled class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600">
              <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
              Tampilkan
            </button>
            <button type="button" onclick="removeFromWatchlist()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
              <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
              Hapus 
            </button>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <!-- Main content area -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Left column with image and description -->
    <div class="md:col-span-2 space-y-6">
      <?php if (!empty($item['photo'])) : ?>
        <div class="overflow-hidden rounded-lg shadow-lg">
          <img src="<?php echo htmlspecialchars($item['photo']); ?>" alt="Item Image" class="w-full h-auto">
        </div>
      <?php endif; ?>
      
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Deskripsi</h3>
          <div class="prose max-w-none text-gray-600">
            <?php echo htmlspecialchars($description); ?>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Right column with auction details -->
    <?php if ($auction_exists): ?>
      <div class="md:col-span-1">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
          <?php if ($now > $end_time) : ?>
    <!-- Tampilan saat lelang selesai -->
    <h3 class="text-lg font-medium text-gray-900 mb-4">Auction Ended</h3>
    <p class="text-sm text-gray-500 mb-2">
        <?php echo htmlspecialchars($end_time->format('j M H:i')); ?>
    </p>
    <p class="text-xl font-semibold mb-2">
        Winning bid: £<?php echo number_format($current_price, 2); ?>
    </p>
    <p class="text-gray-600">
        Winner: <?php echo htmlspecialchars($current_winner); ?>
    </p>
<?php else: ?>
    <!-- Tampilan saat lelang masih berjalan (untuk semua tipe akun) -->
    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail lelang</h3>
    <p class="text-sm text-gray-500 mb-2">
        Berakhir: <?php echo date_format($end_time, 'j M H:i') . $time_remaining ?>
    </p>
    <p class="text-sm text-gray-500 mb-4">
        Total bids: <?php echo $num_bids ?>
    </p>
    <p class="text-2xl font-bold text-indigo-600 mb-6">
        Harga saat ini: Rp <?php echo number_format($current_price, 2) ?>
    </p>
    
    <!-- Daftar penawar (untuk semua tipe akun) -->
    <div class="mb-6">
        <h4 class="text-md font-medium text-gray-900 mb-2">Penawar Tertinggi:</h4>
        <?php
        $bidders_query = "SELECT u.first_name, u.last_name, b.price, b.time_of_bid 
                         FROM Bids b
                         JOIN Users u ON b.user_id = u.user_id
                         WHERE b.auction_id = '$auction_id'
                         ORDER BY b.price DESC
                         LIMIT 5";
        $bidders_result = db_query($connection, $bidders_query);
        
        if (db_num_rows($bidders_result) > 0) {
            echo '<ul class="divide-y divide-gray-200">';
            while ($bidder = db_fetch_single($bidders_result)) {
                echo '<li class="py-2">';
                echo '<div class="flex justify-between">';
                echo '<span>' .htmlspecialchars($bidder['first_name']).' '.htmlspecialchars($bidder['last_name']). '</span>';
                echo '<span class="font-medium">Rp '.number_format($bidder['price'], 2).'</span>';
                echo '</div>';
                echo '<div class="text-xs text-gray-500">'.date('j M H:i', strtotime($bidder['time_of_bid'])).'</div>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="text-gray-500 text-sm">Belum ada tawaran</p>';
        }
        ?>
    </div>
    
    <!-- Form bid hanya untuk account_type 1 -->
    <?php if ($account_type == '1'): ?>
        <form method="POST" action="place_bid.php" class="space-y-4">
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">Rp </span>
                </div>
                <input type="number" name="bid" id="bid" step="0.01" min="<?php echo $current_price + 0.01; ?>" 
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                     placeholder="  Masukkan tawaran Anda" required>
            </div>
            <input type="hidden" name="auction_id" value="<?php echo $auction_id ?>">
            <input type="hidden" name="current_price" value="<?php echo $current_price ?>">
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Place bid
            </button>
        </form>
    <?php endif; ?>
<?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include_once("footer.php") ?>

<script>
  // JavaScript functions: addToWatchlist and removeFromWatchlist.
  function addToWatchlist(button) {
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {
        functionname: 'add_to_watchlist',
        arguments: [<?php echo ($item_id); ?>]
      },

      success: function(obj, textstatus) {
        // Callback function for when call is successful and returns obj
        var objT = obj.trim();

        console.log(objT);

        if (objT == "success") {
          $("#watch_nowatch").hide();
          $("#watch_watching").show();
        } else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.innerHTML += '<div class="text-red-500 mt-2">Add to watch failed. Try again later.</div>';
        }
      },

      error: function(obj, textstatus) {
        console.log("Error");
      }
    }); // End of AJAX call

  } // End of addToWatchlist func

  function removeFromWatchlist(button) {
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {
        functionname: 'remove_from_watchlist',
        arguments: [<?php echo ($item_id); ?>]
      },

      success: function(obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();

      
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
      },

      error: function(obj, textstatus) {
        console.log("Error");
      }
    }); // End of AJAX call

  } // End of removeFromWatchlist func
</script>