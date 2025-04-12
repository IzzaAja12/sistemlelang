<?php 
  require_once('admin_header.php');
  if(!isset($_SESSION['admin_id'])){
    header('location: admin_login.php');
  }
?>

<div class="min-h-screen bg-gray-50 py-8">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
      <div class="px-6 py-4 bg-indigo-600">
        <h2 class="text-xl font-bold text-white">Register New Account</h2>
      </div>
      
      <div class="p-6">
        <!-- Create auction form -->
        <form method="POST" action="process_registration.php" class="space-y-6">
          
          <!-- First Name -->
          <div class="space-y-2">
            <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="firstName" name="firstName" placeholder="First Name">
            <p class="text-sm text-gray-500"><span class="text-red-500">* Required</span></p>
          </div>
          
          <!-- Last Name -->
          <div class="space-y-2">
            <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="lastName" name="lastName" placeholder="Last Name">
            <p class="text-sm text-gray-500"><span class="text-red-500">* Required</span></p>
          </div>
          
          <!-- Account Type -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Registering as a:</label>
            <div class="flex space-x-4">
              <div class="flex items-center">
                <input type="radio" name="accountType" id="accountBuyer" value="buyer" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                <label for="accountBuyer" class="ml-2 block text-sm text-gray-700">Buyer</label>
              </div>
              <div class="flex items-center">
                <input type="radio" name="accountType" id="accountSeller" value="seller" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                <label for="accountSeller" class="ml-2 block text-sm text-gray-700">Petugas</label>
              </div>
            </div>
            <p class="text-sm text-gray-500"><span class="text-red-500">* Required</span></p>
          </div>
          
          <!-- Email -->
          <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="email" name="email" placeholder="Email">
            <p class="text-sm text-gray-500"><span class="text-red-500">* Required</span></p>
          </div>
          
          <!-- Password -->
          <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="password" name="password" placeholder="Password">
            <p class="text-sm text-gray-500"><span class="text-red-500">* Required</span></p>
          </div>
          
          <!-- Repeat Password -->
          <div class="space-y-2">
            <label for="passwordConfirmation" class="block text-sm font-medium text-gray-700">Repeat Password</label>
            <input type="password" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="passwordConfirmation" name="passwordConfirmation" placeholder="Enter password again">
            <p class="text-sm text-gray-500"><span class="text-red-500">* Required</span></p>
          </div>
          
          <!-- Submit Button -->
          <div>
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
              Register
            </button>
          </div>
        </form>
        
        <?php
          // Determine the base URL dynamically
          $login = 'login.php';
          // Display the registration link with the dynamic base URL
          echo '<div class="mt-6 text-center text-sm text-gray-600">Already have an account? <a href="' . $login . '" class="font-medium text-indigo-600 hover:text-indigo-500">Login</a></div>';
        ?>
      </div>
    </div>
  </div>
</div>

<?php include_once("footer.php")?>