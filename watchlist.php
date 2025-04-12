<?php include_once("header.php");
require("utilities.php");
require("database.php"); ?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Watchlist</h1>

    <?php
    $has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    $user_id = $_SESSION['user_id'];

    if (!$has_session) {
        echo '<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4 text-center">Please Login.</div>';
        header("location: login.php");
        exit;
    }

    // Establish database connection
    $connection = db_connect();

    // Query watchlist items
    $watchlist_query = "SELECT
        i.item_id,
        i.name,
        i.description,
        i.category,
        i.colour,
        i.condition,
        i.photo
    FROM
        watchlist w
    JOIN
        item i ON w.item_id = i.item_id
    WHERE
        w.user_id = $user_id;";
    
    $watchlist_result = db_query($connection, $watchlist_query);

    // Check if items exist
    if (db_num_rows($watchlist_result) == 0) {
        echo '<div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-6 text-center">
                <p class="text-lg mb-2">Your watchlist is empty</p>
                <p class="text-sm">Browse listings to add items to your watchlist</p>
                <a href="browse.php" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Browse Listings</a>
              </div>';
    } else {
    ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        // Get item details
        while ($row = mysqli_fetch_assoc($watchlist_result)) {
            $item_id = $row['item_id'];
            $item_name = $row['name'];
            $item_description = $row['description'];
            $item_category = $row['category'];
            $item_colour = $row['colour'];
            $item_condition = $row['condition'];
            $item_photo = $row['photo'];

            if (strlen($item_description) > 150) {
                $desc_shortened = substr($item_description, 0, 150) . '...';
            } else {
                $desc_shortened = $item_description;
            }
        ?>
            <div class="watchlist-item rounded-xl overflow-hidden shadow-lg border border-gray-200 transition-all hover:shadow-xl" data-itemid="<?php echo $item_id; ?>">
                <div class="h-48 overflow-hidden bg-gray-100">
                    <img class="w-full h-full object-cover" src="<?php echo $item_photo ?? "/photos/empty.png"; ?>" alt="<?php echo htmlspecialchars($item_name); ?>">
                </div>
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        <a href="listing.php?item_id=<?php echo $item_id; ?>" class="hover:text-indigo-600 transition"><?php echo htmlspecialchars($item_name); ?></a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($desc_shortened); ?></p>
                    
                    <div class="space-y-2 text-sm text-gray-700 mb-4">
                        <div class="flex items-center">
                            <span class="font-medium w-24">Category:</span> 
                            <span class="bg-gray-100 px-2 py-1 rounded-full"><?php echo htmlspecialchars($item_category); ?></span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-medium w-24">Color:</span> 
                            <span><?php echo htmlspecialchars($item_colour); ?></span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-medium w-24">Condition:</span> 
                            <span><?php echo htmlspecialchars($item_condition); ?></span>
                        </div>
                    </div>
                    
                    <div class="pt-3 border-t border-gray-200">
                        <button 
                            class="flex items-center text-red-600 hover:text-red-800 font-medium transition"
                            onclick="removeFromWatchlist(event)" 
                            data-userid="<?php echo $user_id; ?>" 
                            data-itemid="<?php echo $item_id; ?>"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Remove from Watchlist
                        </button>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <?php
    }
    db_disconnect($connection);
    ?>
</div>

<script>
    function removeFromWatchlist(event) {
        // This performs an asynchronous call to a PHP function using POST method.
        var userId = event.target.getAttribute('data-userid');
        var itemId = event.target.getAttribute('data-itemid');
        
        // If click is on SVG or path, get parent button's data
        if (!userId || !itemId) {
            var button = event.target.closest('button');
            userId = button.getAttribute('data-userid');
            itemId = button.getAttribute('data-itemid');
        }

        var itemCard = document.querySelector('.watchlist-item[data-itemid="' + itemId + '"]');
        
        fetch('watchlist_funcs.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                functionname: 'remove_from_watchlist',
                user_id: userId,
                item_id: itemId
            })
        })
        .then(response => response.text())
        .then(text => {
            var objT = text.trim();
            console.log(objT);

            if (objT == "success") {
                itemCard.style.opacity = '0';
                itemCard.style.transform = 'scale(0.95)';
                itemCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                setTimeout(() => {
                    itemCard.remove();
                    
                    // Check if there are no more items
                    if (document.querySelectorAll('.watchlist-item').length === 0) {
                        const container = document.querySelector('.grid');
                        container.innerHTML = `
                            <div class="col-span-full bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-6 text-center">
                                <p class="text-lg mb-2">Your watchlist is empty</p>
                                <p class="text-sm">Browse listings to add items to your watchlist</p>
                                <a href="browse.php" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Browse Listings</a>
                            </div>
                        `;
                    }
                }, 300);
            } else {
                console.log("Error:", objT);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    }
</script>

<?php include_once("footer.php") ?>