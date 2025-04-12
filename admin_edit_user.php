<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php")?>

<?php
// Check if user_id is set in GET parameter
if (isset($_GET['user_id'])) {
    $connection = db_connect();

    $user_id = $_GET['user_id'];

    // Query to fetch user details
    $query = "SELECT user_id, email, first_name, last_name FROM Users WHERE user_id = '$user_id'";
    $result = db_query($connection, $query);
    $user = db_fetch_single($result);

    if (!$user) {
        echo "User not found.";
        exit;
    }
} else {
    echo "No user ID provided.";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

  <?php include_once("admin_header.php"); ?>

  <div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white shadow-md rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
      </div>
      
      <form action="admin_user_result.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

        <div class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
              <input type="text" name="first_name" id="first_name" value="<?php echo $user['first_name']; ?>" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
              <input type="text" name="last_name" id="last_name" value="<?php echo $user['last_name']; ?>" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>
        </div>

        <div class="mt-8 flex space-x-3">
          <button type="submit" name="action" value="update" 
              class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
              Update User
          </button>
          <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this user?');"
              class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
              Delete User
          </button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>

<?php
  db_disconnect($connection);
?>