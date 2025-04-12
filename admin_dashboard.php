<?php include_once("admin_header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<?php
// Assuming the admin's name is stored in the session (modify as needed)
$adminName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] ?? 'Admin';

$connection = db_connect();

// Query to count the number of users
$user_count_query = "SELECT COUNT(*) FROM Users";
$user_count_result = db_query($connection, $user_count_query);
$user_count = db_fetch_array($user_count_result)[0];

// Query to count the number of auctions
$auction_count_query = "SELECT COUNT(*) FROM Auction";
$auction_count_result = db_query($connection, $auction_count_query);
$auction_count = db_fetch_array($auction_count_result)[0];

// Query to get top categories (example of creative element)
$top_categories_query = "SELECT category, COUNT(*) as count FROM Item GROUP BY category ORDER BY count DESC LIMIT 3";
$top_categories_result = db_query($connection, $top_categories_query);
?>

<!doctype html>
<html lang="en">
<head>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="bg-white shadow rounded-lg mb-6 p-6">
      <div class="flex items-center">
        <div class="rounded-full bg-indigo-500 p-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="ml-4">
          <h1 class="text-2xl font-bold text-gray-800">Selamat datang, <?php echo $adminName; ?>!</h1>
          <p class="text-gray-600">Dashboard Admin - Ringkasan Sistem</p>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <!-- Users Card -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
          <div class="bg-blue-100 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <h2 class="text-gray-600 text-lg">Jumlah Pengguna</h2>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($user_count); ?></p>
          </div>
        </div>
        <div class="mt-4">
          <a href="manage_users.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Kelola pengguna →
          </a>
        </div>
      </div>

      <!-- Auctions Card -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
          <div class="bg-green-100 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <h2 class="text-gray-600 text-lg">Jumlah Lelang</h2>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($auction_count); ?></p>
          </div>
        </div>
        <div class="mt-4">
          <a href="manage_auctions.php" class="text-green-600 hover:text-green-800 text-sm font-medium">
            Kelola lelang →
          </a>
        </div>
      </div>
    </div>

    <!-- Top Categories Section -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-xl font-bold text-gray-800 mb-4">Kategori Lelang Teratas</h3>
      
      <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Kategori
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Jumlah Item
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php 
            $colors = ['bg-green-100 text-green-800', 'bg-blue-100 text-blue-800', 'bg-purple-100 text-purple-800'];
            $i = 0;
            while ($row = db_fetch_single($top_categories_result)) { 
              $color = $colors[$i % count($colors)];
              $i++;
            ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900"><?php echo $row['category']; ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900"><?php echo number_format($row['count']); ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                    Aktif
                  </span>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      
      <div class="mt-4">
        <a href="categories.php" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
          Lihat semua kategori →
        </a>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow rounded-lg p-6 mt-6">
      <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>
      
      <div class="border border-gray-200 rounded-md p-4 bg-gray-50">
        <p class="text-gray-500 text-center py-4">Fitur aktivitas terbaru akan segera hadir!</p>
      </div>
    </div>
  </div>

</body>
</html>

<?php
  db_disconnect($connection);
?>

<?php include_once("footer.php")?>