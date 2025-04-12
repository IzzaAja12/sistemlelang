<?php include_once("header.php")?>

<?php
/* (Uncomment this block to redirect people without selling privileges away from this page)
  // If user is not logged in or not a seller, they should not be able to
  // use this page.
  if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
  }
*/
?>

<div class="bg-gray-50 min-h-screen py-12">
  <div class="max-w-4xl mx-auto px-4">
    <!-- Header Section -->
    <div class="text-center mb-10">
      <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Create New Auction</h2>
      <p class="mt-3 text-lg text-gray-600">Fill in the details below to list your item for auction</p>
    </div>
    
    <!-- Main Form Card -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
      <div class="p-8">
        <form method="post" action="create_auction_result.php" enctype="multipart/form-data">
          
          <!-- Progress Steps -->
          <div class="mb-10">
            <div class="flex justify-between items-center">
              <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">1</div>
                <p class="mt-2 text-sm font-medium text-blue-600">Item Details</p>
              </div>
              <div class="flex-1 h-1 bg-blue-200 mx-2"></div>
              <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center font-bold text-gray-600">2</div>
                <p class="mt-2 text-sm font-medium text-gray-600">Pricing</p>
              </div>
              <div class="flex-1 h-1 bg-blue-200 mx-2"></div>
              <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center font-bold text-gray-600">3</div>
                <p class="mt-2 text-sm font-medium text-gray-600">Image</p>
              </div>
            </div>
          </div>
          
          <!-- Form Groups -->
          <div class="space-y-8">
          <?php
            if(isset($_GET['messageForm'])) :
          ?>
          <div class="rounded-lg bg-red-200 border p-2 text-red-600 mt-4 border-red-400 text-center"><?= $_GET['messageForm'] ?></div>
          <?php
          endif;
          ?>
            <!-- Item Information Section -->
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
              <h3 class="text-xl font-bold text-gray-800 mb-6">Item Information</h3>
              
              <!-- Auction Title -->
              <div class="mb-6">
                <label for="auctionTitle" class="block text-sm font-medium text-gray-700 mb-1">
                  Title of auction <span class="text-red-600">*</span>
                </label>
                <input type="text" id="auctionTitle" name="auctionTitle" placeholder="e.g. Black mountain bike" 
                      class="w-full py-3 px-4 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">A short description of the item you're selling, which will display in listings.</p>
              </div>
              
              <!-- Item Name -->
              <div class="mb-6">
                <label for="itemName" class="block text-sm font-medium text-gray-700 mb-1">
                  Name of Item <span class="text-red-600">*</span>
                </label>
                <input type="text" id="itemName" name="itemName" placeholder="e.g. insert item name" 
                      class="w-full py-3 px-4 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">A name you wish to give your item.</p>
              </div>
              
              <!-- Auction Details -->
              <div class="mb-6">
                <label for="auctionDetails" class="block text-sm font-medium text-gray-700 mb-1">
                  Details
                </label>
                <textarea id="auctionDetails" name="auctionDetails" rows="4" placeholder="Describe your item in detail..." 
                         class="w-full py-3 px-4 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                <p class="mt-1 text-sm text-gray-500">Optional. Provide additional details about your item.</p>
              </div>
              
              <!-- Two columns layout for shorter fields -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Auction Category -->
                <div>
                  <label for="auctionCategory" class="block text-sm font-medium text-gray-700 mb-1">
                    Category
                  </label>
                  <select id="auctionCategory" name="auctionCategory" 
                         class="w-full py-3 px-4 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Choose...</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Fashion">Fashion</option>
                    <option value="Home">Home</option>
                    <option value="Books">Books</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                
                <!-- Item Colour -->
                <div>
                  <label for="itemColour" class="block text-sm font-medium text-gray-700 mb-1">
                    Colour
                  </label>
                  <select id="itemColour" name="itemColour" 
                         class="w-full py-3 px-4 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Choose...</option>
                    <?php
                    $colors = ['Red', 'Orange', 'Yellow', 'Green', 'Blue', 'Purple', 'Pink', 'White', 'Grey', 'Black', 'Brown', 'Other'];
                    foreach ($colors as $color) {
                        echo "<option value=\"$color\">$color</option>";
                    }
                    ?>
                  </select>
                </div>
                
                <!-- Item Condition -->
                <div>
                  <label for="itemCondition" class="block text-sm font-medium text-gray-700 mb-1">
                    Condition <span class="text-red-600">*</span>
                  </label>
                  <select id="itemCondition" name="itemCondition" 
                         class="w-full py-3 px-4 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Choose...</option>
                    <option value="Great">Great</option>
                    <option value="Good">Good</option>
                    <option value="Okay">Okay</option>
                    <option value="Poor">Poor</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">State the condition of the item.</p>
                </div>
                
                <!-- End Date -->
                <div>
                  <label for="auctionEndDate" class="block text-sm font-medium text-gray-700 mb-1">
                    End date <span class="text-red-600">*</span>
                  </label>
                  <input type="datetime-local" id="auctionEndDate" name="auctionEndDate" 
                         class="w-full py-3 px-4 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <p class="mt-1 text-sm text-gray-500">Day for the auction to end.</p>
                </div>
              </div>
            </div>
            
            <!-- Pricing Section -->
            <div class="bg-green-50 p-6 rounded-lg border border-green-100">
              <h3 class="text-xl font-bold text-gray-800 mb-6">Pricing Information</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Starting Price -->
                <div>
                  <label for="auctionStartPrice" class="block text-sm font-medium text-gray-700 mb-1">
                    Starting price <span class="text-red-600">*</span>
                  </label>
                  <div class="mt-1 relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <span class="text-gray-500">£</span>
                    </div>
                    <input type="number" id="auctionStartPrice" name="auctionStartPrice" step="0.01" placeholder="0.00"
                           class="py-3 pl-10 pr-4 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                  </div>
                  <p class="mt-1 text-sm text-gray-500">Initial bid amount.</p>
                </div>
                
                <!-- Reserve Price -->
                <div>
                  <label for="auctionReservePrice" class="block text-sm font-medium text-gray-700 mb-1">
                    Reserve price
                  </label>
                  <div class="mt-1 relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <span class="text-gray-500">£</span>
                    </div>
                    <input type="number" id="auctionReservePrice" name="auctionReservePrice" step="0.01" placeholder="0.00"
                           class="py-3 pl-10 pr-4 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                  </div>
                  <p class="mt-1 text-sm text-gray-500">Optional. Auctions that end below this price will not go through.</p>
                </div>
              </div>
            </div>
            
            <!-- Image Upload Section -->
            <div class="bg-purple-50 p-6 rounded-lg border border-purple-100">
              <h3 class="text-xl font-bold text-gray-800 mb-6">Item Image</h3>
              
              <!-- Photo Upload with Preview -->
              <div>
                <label for="uploadImage" class="block text-sm font-medium text-gray-700 mb-1">
                  Upload Image <span class="text-red-600">*</span>
                </label>
                <div class="mt-1 flex flex-col items-center">
                  <!-- Image Preview Area -->
                  <div id="imagePreviewContainer" class="hidden mb-4 w-full max-w-md">
                    <div class="relative rounded-lg overflow-hidden border-2 border-purple-300">
                      <img id="imagePreview" class="w-full object-cover" src="#" alt="Image preview">
                      <button type="button" id="removeImage" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Upload Area -->
                  <div id="uploadArea" class="w-full flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                    <div class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label for="uploadImage" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                          <span>Upload a file</span>
                          <input id="uploadImage" name="image" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">
                        Allowed file types: jpg, png, jpeg
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        
          <!-- Submit Button -->
          <div class="mt-10">
            <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
              Create Auction
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Tips Card -->
    <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
      <div class="p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Tips for a successful auction</h3>
        <ul class="space-y-2 text-gray-600">
          <li class="flex items-start">
            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Use high-quality photos that clearly show the item
          </li>
          <li class="flex items-start">
            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Write a detailed description with all relevant information
          </li>
          <li class="flex items-start">
            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Set a competitive starting price to attract bidders
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
  // Image Preview Functionality
  document.addEventListener('DOMContentLoaded', function() {
    const uploadImage = document.getElementById('uploadImage');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const uploadArea = document.getElementById('uploadArea');
    const removeImage = document.getElementById('removeImage');
    
    // Function to handle file selection
    uploadImage.addEventListener('change', function() {
      const file = this.files[0];
      
      if (file) {
        // Only process image files
        if (!file.type.match('image.*')) {
          alert('Please select an image file (jpg, jpeg, or png)');
          return;
        }
        
        // Read and display the image
        const reader = new FileReader();
        
        reader.onload = function(e) {
          // Show preview
          imagePreview.src = e.target.result;
          imagePreviewContainer.classList.remove('hidden');
          uploadArea.classList.add('hidden');
        };
        
        reader.readAsDataURL(file);
      }
    });
    
    // Remove image functionality
    removeImage.addEventListener('click', function() {
      // Clear the file input
      uploadImage.value = '';
      
      // Hide preview, show upload area
      imagePreviewContainer.classList.add('hidden');
      uploadArea.classList.remove('hidden');
    });
    
    // Handle drag and drop functionality
    const dropArea = document.getElementById('uploadArea');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
      dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
      dropArea.classList.add('border-purple-500', 'bg-purple-100');
    }
    
    function unhighlight() {
      dropArea.classList.remove('border-purple-500', 'bg-purple-100');
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
      const dt = e.dataTransfer;
      const file = dt.files[0];
      
      if (file) {
        // Only process image files
        if (!file.type.match('image.*')) {
          alert('Please select an image file (jpg, jpeg, or png)');
          return;
        }
        
        // Programmatically add file to input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        uploadImage.files = dataTransfer.files;
        
        // Read and display the image
        const reader = new FileReader();
        
        reader.onload = function(e) {
          // Show preview
          imagePreview.src = e.target.result;
          imagePreviewContainer.classList.remove('hidden');
          uploadArea.classList.add('hidden');
        };
        
        reader.readAsDataURL(file);
      }
    }
  });
</script>

<?php include_once("footer.php")?>