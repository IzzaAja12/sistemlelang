<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("header.php")?>

<?php
// Check if auction_id is set in GET parameter
if (isset($_GET['auction_id'])) {
    $connection = db_connect();

    $auction_id = $_GET['auction_id'];

    // Query to fetch auction details
    $query = "SELECT Auction.auction_id, Auction.auction_title, Auction.end_time, Item.name AS item_name 
              FROM Auction
              INNER JOIN Item ON Auction.item_id = Item.item_id
              WHERE Auction.auction_id = '$auction_id'";

    $result = db_query($connection, $query);
    $auction = db_fetch_single($result);

    if (!$auction) {
        echo "Auction not found.";
        exit;
    }

    // Convert end_time to a format suitable for datetime-local input
    $auction['end_time'] = str_replace(' ', 'T', $auction['end_time']);
} else {
    echo "No auction ID provided.";
    exit;
}
?>

<div class="max-w-4xl mx-auto px-4 py-8">
  <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit lelang</h1>
    
    <form action="admin_auction_result.php" method="post" class="space-y-6">
      <input type="hidden" name="auction_id" value="<?php echo $auction['auction_id']; ?>">

      <div class="space-y-2">
        <label for="auction_title" class="block text-sm font-medium text-gray-700">Judul lelang</label>
        <input type="text" 
               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" 
               name="auction_title" 
               id="auction_title" 
               value="<?php echo $auction['auction_title']; ?>" 
               required>
      </div>

      <div class="space-y-2">
        <label for="item_name" class="block text-sm font-medium text-gray-700">Nama barang</label>
        <input type="text" 
               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" 
               name="item_name" 
               id="item_name" 
               value="<?php echo $auction['item_name']; ?>" 
               required>
      </div>

      <div class="space-y-2">
        <label for="end_time" class="block text-sm font-medium text-gray-700">Waktu berakhir</label>
        <input type="datetime-local" 
               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" 
               name="end_time" 
               id="end_time" 
               value="<?php echo $auction['end_time']; ?>" 
               required>
      </div>

      <div class="flex space-x-4 pt-4">
        <button type="submit" 
                name="action" 
                value="update" 
                class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
          Update lelang
        </button>
        <button type="submit" 
                name="action" 
                value="delete" 
                class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150"
                onclick="return confirm('Are you sure you want to delete this auction?');">
          Hapus lelang
        </button>
      </div>
    </form>
  </div>
</div>

<?php
  db_disconnect($connection);
?>