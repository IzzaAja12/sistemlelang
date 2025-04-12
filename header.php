<?php
session_start();
// FIXME: Replace with proper database validation in actual implementation
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind and FontAwesome -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a2d9d6d52f.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style>
    @keyframes line {
      from { width: 100%; }
      to { width: 0; }
    }
    .notification-line {
      animation: line 5s linear forwards;
    }
  </style>
  <title>webbnya izza - lelang</title>
</head>

<body class="bg-gray-50">
  <!-- Navbar -->
  <nav class="bg-white z-50 shadow fixed w-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Brand -->
          
          <!-- Desktop Navigation -->
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <a href="browse.php" class="border-transparent text-gray-500 hover:border-indigo-500 hover:text-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
              Browse
            </a>
            <?php if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == '1') { ?>
              <!-- Buyer Links -->
              <a href="mybids.php" class="border-transparent text-gray-500 hover:border-indigo-500 hover:text-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                My Bids
              </a>
              <a href="watchlist.php" class="border-transparent text-gray-500 hover:border-indigo-500 hover:text-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Watchlist
              </a>
              <a href="recommendations.php" class="border-transparent text-gray-500 hover:border-indigo-500 hover:text-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Recommended
              </a>
              <a href="won_auctions.php" class="border-transparent text-gray-500 hover:border-indigo-500 hover:text-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Won Auctions
              </a>
            <?php } ?>

            <?php if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == '0') { ?>
              <!-- Seller Links -->
              <a href="mylistings.php" class="border-transparent text-gray-500 hover:border-indigo-500 hover:text-indigo-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                My Listings
              </a>
              <a href="create_auction.php" class="ml-3 px-3 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                + Create Auction
              </a>
            <?php } ?>
          </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center">
          <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
            <a href="logout.php" class="ml-4 px-3 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
              Logout
            </a>
          <?php } else { ?>
            <a href="login.php" class="ml-4 px-3 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
              Login
            </a>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <a href="browse.php" class="bg-gray-50 border-indigo-500 text-indigo-600 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
          Browse
        </a>
        <?php if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == '1') { ?>
          <!-- Buyer Mobile Links -->
          <a href="mybids.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-indigo-500 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
            My Bids
          </a>
          <a href="watchlist.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-indigo-500 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
            Watchlist
          </a>
          <a href="recommendations.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-indigo-500 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
            Recommended
          </a>
          <a href="won_auctions.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-indigo-500 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
            Won Auctions
          </a>
        <?php } ?>

        <?php if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == '0') { ?>
          <!-- Seller Mobile Links -->
          <a href="mylistings.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-indigo-500 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
            My Listings
          </a>
          <a href="create_auction.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-indigo-500 hover:text-indigo-600 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
            + Create Auction
          </a>
        <?php } ?>
      </div>
    </div>
  </nav>

  <!-- Spacer for fixed navbar -->
  <div class="h-16"></div>

  <?php if(isset($_GET['message'])): ?>
    <!-- Notification -->
    <div id="notification" class="fixed bottom-4 right-4 w-80 bg-green-50 rounded-lg shadow-lg border border-green-200 overflow-hidden transition-opacity duration-300">
      <div class="p-4">
        <div class="flex justify-between items-center">
          <p class="text-green-700 text-sm">
            <?= htmlspecialchars($_GET['message']) ?>
          </p>
          <button onclick="dismissNotification()" 
                  class="text-green-700 hover:text-green-900 focus:outline-none border-green-400 border px-2 rouded-md">
            Tutup
          </button>
        </div>
      </div>
      <div class="h-1 bg-green-200">
        <div class="notification-line h-full bg-green-500"></div>
      </div>
    </div>

    <script>
      // Function to dismiss notification
      function dismissNotification() {
        const notification = document.getElementById('notification');
        notification.classList.add('opacity-0');
        setTimeout(() => {
          notification.remove();
        }, 300);
      }

      // Auto dismiss after 5 seconds (5000ms)
      setTimeout(() => {
        dismissNotification();
      }, 5000);
    </script>
  <?php endif; ?>
</body>
</html>