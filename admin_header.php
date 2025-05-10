<?php
  session_start();
  if(!isset($_SESSION['admin'])){
    header("location: admin_login.php");
  }
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind & FontAwesome -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a2d9d6d52f.js" crossorigin="anonymous"></script>
  <style>
    @keyframes line {
      from { width: 100%; }
      to { width: 0; }
    }
    .notification-line {
      animation: line 5s linear forwards;
    }
  </style>

  <title>Admin Panel</title>
</head>

<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="bg-white shadow z-50 fixed top-0 w-full">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <!-- Left: Navigation -->
        <div class="flex space-x-4">
          <a href="admin_users.php" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
            User
          </a>
          <a href="admin_auctions.php" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
            Lelang
          </a>
        </div>

        <!-- Right: Logout -->
        <div>
          <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) { ?>
            <a href="logout.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
              Logout
            </a>
          <?php } ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- Spacer -->
  <div class="h-16"></div>

  <!-- Notification -->
  <?php if(isset($_GET['message'])): ?>
    <div id="notification" class="fixed bottom-4 right-4 w-80 bg-green-50 rounded-lg shadow-lg border border-green-200 overflow-hidden transition-opacity duration-300 z-50">
      <div class="p-4">
        <div class="flex justify-between items-center">
          <p class="text-green-700 text-sm">
            <?= htmlspecialchars($_GET['message']) ?>
          </p>
          <button onclick="dismissNotification()" 
                  class="text-green-700 hover:text-green-900 focus:outline-none border-green-400 border px-2 rounded-md">
            Tutup
          </button>
        </div>
      </div>
      <div class="h-1 bg-green-200">
        <div class="notification-line h-full bg-green-500"></div>
      </div>
    </div>

    <script>
      function dismissNotification() {
        const notification = document.getElementById('notification');
        notification.classList.add('opacity-0');
        setTimeout(() => {
          notification.remove();
        }, 300);
      }

      setTimeout(() => {
        dismissNotification();
      }, 5000);
    </script>
  <?php endif; ?>

</body>
</html>
