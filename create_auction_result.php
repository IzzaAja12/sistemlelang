<?php include_once("header.php")?>
<?php include_once("database.php") ?>

<div class="container my-5">

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */

    // Create database connection
    $connection = db_connect();

/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //*******************************************************************************/
        // NOTE: Provide helpful feedback to the user!
        //******************************************************************************/

        if (empty($_POST['auctionTitle']) || empty($_POST['itemName']) || 
            empty($_POST['auctionEndDate']) || empty($_POST['itemCondition']) ||
            empty($_POST['auctionStartPrice']) || empty($_FILES["image"]["name"]) ) 
        
        {
            $errorMessage = "Error: Required fields are empty. Please fill in the following:";
            
            if (empty($_POST['auctionTitle'])) {
                $errorMessage .= "<br>- Auction Title";
            }
        
            if (empty($_POST['itemName'])) {
                $errorMessage .= "<br>- Item Name";
            }
        
            if (empty($_POST['auctionEndDate'])) {
                $errorMessage .= "<br>- Auction End Date";
            }
        
            if (empty($_POST['itemCondition'])) {
                $errorMessage .= "<br>- Item Condition";
            }

            if (empty($_POST['auctionStartPrice'])) {
                $errorMessage .= "<br>- Start Price";
            }

            if (empty($_FILES["image"]["name"])) {
                $errorMessage .= "<br>- image name";
            }
    
        
            echo '<div class="alert alert-danger mt-3" role="alert">' . $errorMessage . '</div>';
            db_disconnect($connection);
            exit();
        }

      // Cek apakah tanggal akhir sebelum hari ini
$today = date("Y-m-d H:i:s");
if ($_POST['auctionEndDate'] < $today) {
    db_disconnect($connection);
    header("location: create_auction.php?messageForm=Tanggal akhir tidak boleh sebelum hari ini");
    exit();
}

// ************************ cek foto ************************ 

$targetDirectory = "photos/";
$targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// Cek apakah file beneran gambar
$check = getimagesize($_FILES["image"]["tmp_name"]);
if ($check === false) {
    db_disconnect($connection);
    header("location: create_auction.php?messageForm=File yang diunggah bukan gambar");
    exit();
}

// Cek ukuran file (misalnya batas 500KB)
if ($_FILES["image"]["size"] > 1000000) {
    db_disconnect($connection);
    header("location: create_auction.php?messageForm=Ukuran file terlalu besar (maksimal 1MB)");
    exit();
}

// Format file yang diizinkan
$allowedExtensions = ['jpg', 'jpeg', 'png'];
if (!in_array($imageFileType, $allowedExtensions)) {
    db_disconnect($connection);
    header("location: create_auction.php?messageForm=Format file tidak valid. Hanya jpg, jpeg, dan png yang diperbolehkan");
    exit();
}

// Upload file
if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
    db_disconnect($connection);
    header("location: create_auction.php?messageForm=Gagal mengunggah file");
    exit();
}


        // **************************************************************

    }



/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */

    // Retrieve data from the form

    // Access the value of the data from the $_POST array
    $name = $_POST['itemName'];
    $description = !empty($_POST['auctionDetails']) ? $_POST['auctionDetails'] : NULL ; // Check if empty
    $category = !empty($_POST['auctionCategory']) ? $_POST['auctionCategory'] : "Other"; // Check if empty
    $colour = !empty($_POST['itemColour']) ? $_POST['itemColour'] : "Other"; // Check if empty
    $condition = $_POST['itemCondition'];
    $start_time = date("Y-m-d H:i:s"); // Get the current date and time;
    $end_time = $_POST['auctionEndDate'];
    $auction_title = $_POST['auctionTitle'];
    $reserve_price = !empty($_POST['auctionReservePrice']) ? $_POST['auctionReservePrice'] : 0; // Check if empty
    $starting_price = $_POST['auctionStartPrice'];

    $user_id = $_SESSION['user_id'];

    // *********************************************************
    // Note: Reserve price needs to be empty or else system will crash. 
    // *********************************************************

    // Now $X contains the value entered in the 'X' field
    echo '<div class="text-left mt-3">';
    echo '<h3>Entered Values:</h3>';
    echo "<p><strong>Name:</strong> " . $name . "</p>";
    echo "<p><strong>Description:</strong> " . $description . "</p>";
    echo "<p><strong>Category:</strong> " . $category . "</p>";
    echo "<p><strong>Colour:</strong> " . $colour . "</p>";
    echo "<p><strong>Condition:</strong> " . $condition . "</p>";
    echo "<p><strong>Start Time:</strong> " . $start_time . "</p>";
    echo "<p><strong>End Time:</strong> " . $end_time . "</p>";
    echo "<p><strong>Auction Title:</strong> " . $auction_title . "</p>";
    echo "<p><strong>Reserve Price:</strong> " . $reserve_price . "</p>";
    echo "<p><strong>Starting Price:</strong> " . $starting_price . "</p>";
    echo '</div>';

    // *****************************************************
    // Perform validation or additional processing if needed
    // *****************************************************

    $filePath =  "photos/" . basename($_FILES["image"]["name"]);
    #echo 'File path = ' . $filePath;

    // Insert data into the database
    // ********************** Add photo to auction_query  **********************
    $item_query = "INSERT INTO Item (name, description, category, colour, `condition`, photo)
        VALUES ('$name', '$description', '$category', '$colour', '$condition', '$filePath')";

    // Execute the item query
    $item_result = db_query($connection, $item_query);

    // Check item values
    if ($item_result) {
            // echo "Item added to DB <br>";
            # echo '<div class="alert alert-success mt-3" role="alert"> Item data inserted successfully! </div>';

            // Get the last inserted item_id
            $item_id = mysqli_insert_id($connection);
 
            // Insert into Auction table
            $auction_query = "INSERT INTO Auction (user_id, item_id, start_time, end_time, auction_title, reserve_price, starting_price)
                VALUES ('$user_id', '$item_id', '$start_time', '$end_time', '$auction_title', '$reserve_price', '$starting_price')";

            $auction_result = db_query($connection, $auction_query);
            if ($auction_result) {
                #echo '<div class="alert alert-success mt-3" role="alert"> Auction data inserted successfully! </div>';
            } 
            else { 
                #echo '<div class="alert alert-danger mt-3" role="alert"> Error: adding data to auction table </div>';
                db_disconnect($connection);    
                exit();
                // error_log("Auction Insert Error: " . mysqli_error($connection));
            }
        } 
    else {
            #echo '<div class="alert alert-danger mt-3" role="alert"> Error: adding data to item table </div>';
            db_disconnect($connection);
            exit();
        }

    // Close db connection
    db_disconnect($connection);
            

//*******************************************************************************/
// If all is successful, let user know.
//*******************************************************************************/
// Check the success of the queries
if ($item_result && $auction_result) {

    // Create the listing page link
    $listingLink = "listing.php?item_id=$item_id";

    // Display the success message with the link
    header("location: mylistings.php?message=Berhasil menambahkan item lelang");
    exit();

} else {
    header("location: create_auction.php?messageForm=Gagal menambahkan item lelang");
    exit();

}


?>

</div>


<?php include_once("footer.php")?>