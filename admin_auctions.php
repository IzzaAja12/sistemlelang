<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php")?>

<?php
$connection = db_connect();

// Fetch all auctions
$query = "SELECT Auction.auction_id, Auction.auction_title, Item.name, Auction.end_time
          FROM Auction
          INNER JOIN Item ON Auction.item_id = Item.item_id";
$result = db_query($connection, $query);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Manajemen Lelang</h1>
      <div class="mt-4 sm:mt-0">
     
      </div>
    </div>
    
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
      <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-gray-900">ID</th>
            <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-gray-900">Judul</th>
            <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-gray-900">Nama Barang</th>
            <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-gray-900">Batas Waktu</th>
            <th scope="col" class="py-3.5 px-4 text-right text-sm font-semibold text-gray-900">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          <?php while ($row = db_fetch_single($result)) { ?>
            <tr class="hover:bg-gray-50">
              <td class="whitespace-nowrap py-4 px-4 text-sm text-gray-500"><?php echo $row['auction_id']; ?></td>
              <td class="whitespace-nowrap py-4 px-4 text-sm font-medium text-gray-900"><?php echo $row['auction_title']; ?></td>
              <td class="whitespace-nowrap py-4 px-4 text-sm text-gray-500"><?php echo $row['name']; ?></td>
              <td class="whitespace-nowrap py-4 px-4 text-sm text-gray-500"><?php echo $row['end_time']; ?></td>
              <td class="whitespace-nowrap py-4 px-4 text-sm text-right">
  <form action="admin_auction_result.php" method="post" onsubmit="return confirm('Yakin ingin menghapus lelang ini?');" style="display:inline;">
    <input type="hidden" name="auction_id" value="<?php echo $row['auction_id']; ?>">
    <input type="hidden" name="action" value="delete">
    <button type="submit" class="ml-2 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
      Hapus
    </button>
  </form>
</td>

            </tr>
          <?php } ?>
          
          <?php if (db_num_rows($result) == 0) { ?>
            <tr>
              <td colspan="5" class="py-8 text-center text-sm text-gray-500">
                No auctions found
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>

<?php
  db_disconnect($connection);
?>