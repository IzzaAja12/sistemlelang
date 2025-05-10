<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php require("database.php") ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="mb-8">
    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Telusuri Daftar</h1>
    <p class="mt-2 text-sm text-gray-500">Temukan item yang sempurna dari koleksi lengkap kami</p>
  </div>

  <!-- Search Form -->
  <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
    <form method="get" action="browse.php" class="space-y-4 md:space-y-0 md:grid md:grid-cols-12 md:gap-4">
      <!-- Keyword Search -->
      <div class="md:col-span-5">
        <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
        <div class="relative rounded-md shadow-sm">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
          </div>
          <input type="text" name="keyword" id="keyword" 
                 class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" 
                 placeholder="Cari apa saja" value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
        </div>
      </div>

      <!-- Category Filter -->
      <div class="md:col-span-3">
        <label for="cat" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
        <select class="block w-full py-3 px-4 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                name="cat" id="cat">
          <option value="all" <?php echo (!isset($_GET['cat']) || $_GET['cat'] === 'all' ? 'selected' : '') ?>>Semua kategori</option>
          <?php
            $connection = db_connect();
            $categories = "SELECT DISTINCT category FROM item";
            $result_categories = db_query($connection, $categories);
            confirm_result_set($result_categories);
            while ($row = mysqli_fetch_array($result_categories)) {
              $selected = isset($_GET['cat']) && $_GET['cat'] === $row[0] ? 'selected' : '';
              echo '<option value="' . htmlspecialchars($row[0]) . '" ' . $selected . '>' . htmlspecialchars($row[0]) . '</option>';
            }
            db_free_result($result_categories);
            db_disconnect($connection);
          ?>
        </select>
      </div>

      <!-- Sort Options -->
      <div class="md:col-span-3">
        <label for="order_by" class="block text-sm font-medium text-gray-700 mb-1">Urutkan menurut</label>
        <select class="block w-full py-3 px-4 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                name="order_by" id="order_by">
          <option value="pricelow" <?php echo (!isset($_GET['order_by']) || $_GET['order_by'] === 'pricelow') ? 'selected' : ''; ?>>Harga (rendah ke tinggi)</option>
          <option value="pricehigh" <?php echo isset($_GET['order_by']) && $_GET['order_by'] === 'pricehigh' ? 'selected' : ''; ?>>Harga (tinggi ke rendah)</option>
          <option value="date" <?php echo isset($_GET['order_by']) && $_GET['order_by'] === 'date' ? 'selected' : ''; ?>>Batas paling cepat</option>
        </select>
      </div>

      <!-- Submit Button -->
      <div class="md:col-span-1 flex items-end">
        <button type="submit" class="w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
          <i class="fas fa-search mr-1"></i> Cari
        </button>
      </div>
    </form>
  </div>

  <?php
  // Retrieve these from the URL
  $keyword = $_GET['keyword'] ?? '';
  $category = $_GET['cat'] ?? 'all';
  $ordering = $_GET['order_by'] ?? 'pricelow';
  $curr_page = $_GET['page'] ?? 1;

  $connection = db_connect();
  $search = "SELECT DISTINCT item.item_id,
                           auction.auction_title,
                           item.description,
                           COALESCE(MAX(bids.price), auction.starting_price) AS highest_bid,
                           COUNT(bids.auction_id) AS num_bids,
                           auction.end_time,
                           item.category
           FROM item
           INNER JOIN auction ON item.item_id = auction.item_id
           LEFT JOIN bids ON auction.auction_id = bids.auction_id
           WHERE (item.name LIKE '%$keyword%'
           OR item.description LIKE '%$keyword%'
           OR auction.auction_title LIKE '%$keyword%')
           AND auction.end_time > NOW()";

  if($category != 'all') {
    $search .= " AND item.category = '$category'";
  }

  $search .= " GROUP BY item.item_id, auction.auction_title, auction.end_time, auction.starting_price";

  if($ordering == "pricelow") {
    $search .= " ORDER BY highest_bid ASC";
  } elseif($ordering == "pricehigh") {
    $search .= " ORDER BY highest_bid DESC";
  } elseif($ordering == "date") {
    $search .= " ORDER BY auction.end_time ASC";
  }

  // Add pagination
  $results_per_page = 10;
  $offset = ($curr_page - 1) * $results_per_page;
  $search .= " LIMIT $offset, $results_per_page";

  $search_results = db_query($connection, $search);
  confirm_result_set($search_results);

  // Get total count for pagination
  $count_query = "SELECT COUNT(DISTINCT item.item_id) as total 
                  FROM item 
                  INNER JOIN auction ON item.item_id = auction.item_id
                  WHERE (item.name LIKE '%$keyword%'
                  OR item.description LIKE '%$keyword%'
                  OR auction.auction_title LIKE '%$keyword%')
                  AND auction.end_time > NOW()";
  
  if($category != 'all') {
    $count_query .= " AND item.category = '$category'";
  }

  $count_result = db_query($connection, $count_query);
  $total_items = db_fetch_single($count_result)['total'];
  $max_page = ceil($total_items / $results_per_page);
  ?>

  <!-- Results Section -->
  <div>
    <?php if (db_num_rows($search_results) == 0): ?>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
        <i class="fas fa-search text-blue-500 text-4xl mb-3"></i>
        <h3 class="text-lg font-medium text-blue-800 mb-1">Tidak ditemukan hasil yang cocok</h3>
        <p class="text-blue-600">Coba sesuaikan kriteria pencarian Anda atau telusuri semua kategori</p>
      </div>
    <?php else: ?>
      <!-- Results Count -->
      <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-gray-500">
          Tampilkan <?php echo min(($curr_page - 1) * $results_per_page + 1, $total_items); ?> - 
          <?php echo min($curr_page * $results_per_page, $total_items); ?> of 
          <?php echo $total_items; ?> Hasil
        </p>
      </div>
      
      <!-- Custom Listing Display -->
      <div class="space-y-4">
        <?php while($row = db_fetch_single($search_results)): ?>
          <?php
          $item_id = $row["item_id"];
          $title = $row["auction_title"];
          $description = $row["description"];
          $current_price = $row["highest_bid"];
          $num_bids = $row["num_bids"];
          $end_date = new DateTime($row["end_time"]);
          
          // Custom listing display with border and better styling
          ?>
          <div class="border border-gray-200 rounded-lg shadow-sm bg-white hover:shadow-md transition-shadow duration-150 overflow-hidden">
            <a href="listing.php?item_id=<?php echo $item_id; ?>" class="block">
              <div class="p-5">
                <div class="flex justify-between items-start">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($title); ?></h3>
                  <div class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium">
                    <?php echo htmlspecialchars($row["category"]); ?>
                  </div>
                </div>
                
                <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($description); ?></p>
                
                <div class="flex flex-wrap gap-6 text-sm">
                  <div>
                    <span class="font-medium text-gray-500">Tawaran Saat Ini:</span>
                    <span class="text-lg font-bold text-gray-900 ml-1">Rp <?php echo number_format($current_price, 2); ?></span>
                  </div>
                  
                  <div>
                    <span class="font-medium text-gray-500">Bids:</span>
                    <span class="text-gray-900 ml-1"><?php echo $num_bids; ?></span>
                  </div>
                  
                  <div class="ml-auto">
                    <span class="font-medium text-gray-500">Ends:</span>
                    <span class="text-gray-900 ml-1">
                      <?php 
                      $now = new DateTime();
                      if ($now->diff($end_date)->days < 1) {
                        echo '<span class="text-red-600 font-medium">' . $end_date->format('H:i') . ' today</span>';
                      } else {
                        echo $end_date->format('M j, Y, H:i');
                      }
                      ?>
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-500">Item #<?php echo $item_id; ?></span>
                  <span class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    View details <i class="fas fa-chevron-right ml-2"></i>
                  </span>
                </div>
              </div>
            </a>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <?php if ($max_page > 1): ?>
    <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0 mt-8 pt-6">
      <div class="flex w-0 flex-1">
        <?php if ($curr_page != 1): ?>
          <a href="browse.php?<?php echo http_build_query(array_merge($_GET, ['page' => $curr_page - 1])); ?>" 
             class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-300">
            <i class="fas fa-chevron-left mr-2 text-gray-500"></i>
            Previous
          </a>
        <?php else: ?>
          <span class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 border border-gray-200 cursor-not-allowed">
            <i class="fas fa-chevron-left mr-2 text-gray-400"></i>
            Previous
          </span>
        <?php endif; ?>
      </div>
      
      <div class="hidden md:flex">
        <?php
        $high_page_boost = max(3 - $curr_page, 0);
        $low_page_boost = max(2 - ($max_page - $curr_page), 0);
        $low_page = max(1, $curr_page - 2 - $low_page_boost);
        $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
        
        if ($low_page > 1): ?>
          <a href="browse.php?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" 
             class="inline-flex items-center px-4 py-2 mx-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            1
          </a>
          <?php if ($low_page > 2): ?>
            <span class="px-2 py-2 text-gray-500">...</span>
          <?php endif; ?>
        <?php endif; ?>
        
        <?php for ($i = $low_page; $i <= $high_page; $i++):
          $query = array_merge($_GET, ['page' => $i]);
          if ($i == $curr_page): ?>
            <span class="inline-flex items-center px-4 py-2 mx-1 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 rounded-md">
              <?php echo $i; ?>
            </span>
          <?php else: ?>
            <a href="browse.php?<?php echo http_build_query($query); ?>" 
               class="inline-flex items-center px-4 py-2 mx-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
              <?php echo $i; ?>
            </a>
          <?php endif;
        endfor; ?>
        
        <?php if ($high_page < $max_page): ?>
          <?php if ($high_page < $max_page - 1): ?>
            <span class="px-2 py-2 text-gray-500">...</span>
          <?php endif; ?>
          <a href="browse.php?<?php echo http_build_query(array_merge($_GET, ['page' => $max_page])); ?>" 
             class="inline-flex items-center px-4 py-2 mx-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            <?php echo $max_page; ?>
          </a>
        <?php endif; ?>
      </div>
      
      <div class="flex w-0 flex-1 justify-end">
        <?php if ($curr_page != $max_page): ?>
          <a href="browse.php?<?php echo http_build_query(array_merge($_GET, ['page' => $curr_page + 1])); ?>" 
             class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-300">
            Next
            <i class="fas fa-chevron-right ml-2 text-gray-500"></i>
          </a>
        <?php else: ?>
          <span class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 border border-gray-200 cursor-not-allowed">
            Next
            <i class="fas fa-chevron-right ml-2 text-gray-400"></i>
          </span>
        <?php endif; ?>
      </div>
    </nav>
  <?php endif; ?>

  <?php
  db_free_result($search_results);
  db_free_result($count_result);
  db_disconnect($connection);
  ?>
</div>

<?php include_once("footer.php") ?>