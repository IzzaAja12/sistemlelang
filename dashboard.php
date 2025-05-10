<?php
include_once("header.php");
require("utilities.php");
require("database.php");

$has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_id = $_SESSION['user_id'];

if (!$has_session) {
    echo ('<div class="text-center">Please Login.</div>');
    header("location: login.php");
    exit;
}

$connection = db_connect();
$query = "SELECT * FROM users WHERE user_id='$user_id'";
$result = db_query($connection, $query);
confirm_result_set($result);
$user = db_fetch_single($result);
db_free_result($result);
db_disconnect($connection);

function updateUserField($field, $new_value, $user_id) {
    $connection = db_connect();
    $update_query = "UPDATE users SET $field = '$new_value' WHERE user_id = '$user_id'";
    $stmt = db_query($connection, $update_query);
    confirm_result_set($stmt);
    db_free_result($stmt);
    db_disconnect($connection);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $user_id = $_POST['user_id'];

    if (isset($_POST['new_first_name']) && $_POST['new_first_name'] != '') {
        updateUserField('first_name', $_POST['new_first_name'], $user_id);
    } 

    if (isset($_POST['new_last_name']) && $_POST['new_last_name'] != '') {
        updateUserField('last_name', $_POST['new_last_name'], $user_id);
    } 

    if (isset($_POST['new_email']) && $_POST['new_email'] != '') {
        updateUserField('email', $_POST['new_email'], $user_id);
    } 

    if (isset($_POST['new_password']) && $_POST['new_password'] != '') {
        updateUserField('password', $_POST['new_password'], $user_id);
    }
}
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat datang, <?php echo $user['first_name']; ?>! 👋</h1>
        <p class="text-gray-600">Kelola akun dan aktivitas Anda</p>
    </div>

    <!-- Activities Section -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-indigo-600">Aktivitas Anda</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if ($user['role'] == 0) : ?>
                <?php
                    $connection = db_connect();
                    $stmt = "SELECT total_ratings, average_rating FROM users WHERE user_id = '$user_id'";
                    $rating_call = db_query($connection, $stmt);
                    confirm_result_set($rating_call);
                    $rating = db_fetch_single($rating_call);
                    $totalRatings = $rating["total_ratings"];
                    $userRating = $rating["average_rating"];
                    db_free_result($rating_call);
                    db_disconnect($connection);
                ?>
                
                <!-- Seller Activities -->
                <a href="browse.php" class="p-4 border rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="font-medium">Telusuri daftar</div>
                    <p class="text-sm text-gray-500">Jelajahi lelang terkini</p>
                </a>
                <a href="mylistings.php" class="p-4 border rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="font-medium">Daftar saya</div>
                    <p class="text-sm text-gray-500">Kelola lelang aktif Anda</p>
                </a>
                <a href="create_auction.php" class="p-4 border rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="font-medium">Buat Lelang Baru</div>
                    <p class="text-sm text-gray-500">Mulailah menjual barang Anda</p>
                </a>
                
                <!-- Rating Section -->
                <div class="p-4 border rounded-lg bg-indigo-50 border-indigo-200">
                    <div class="font-medium">Peringkat Penjual</div>
                    <?php if ($totalRatings == 0) : ?>
                        <p class="text-sm text-gray-500">Belum ada peringkat</p>
                    <?php else : ?>
                        <div class="flex items-center mt-2">
                            <div class="text-2xl font-bold text-indigo-600"><?php echo number_format($userRating, 1); ?></div>
                            <div class="ml-2 text-yellow-400">
                                <?php for ($i = 0; $i < floor($userRating); $i++) : ?>
                                    ★
                                <?php endfor; ?>
                            </div>
                            <span class="text-sm text-gray-500 ml-2">(<?php echo $totalRatings; ?> ratings)</span>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($user['role'] == 1) : ?>
                <!-- Buyer Activities -->
                <a href="browse.php" class="p-4 border rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="font-medium">Telusuri daftar</div>
                    <p class="text-sm text-gray-500">Temukan item untuk ditawar</p>
                </a>
                <a href="mybids.php" class="p-4 border rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="font-medium">Tawaran Saya</div>
                    <p class="text-sm text-gray-500">Lacak tawaran aktif Anda</p>
                </a>
                <a href="watchlist.php" class="p-4 border rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="font-medium">Daftar pantauan</div>
                    <p class="text-sm text-gray-500">Lihat item tersimpan</p>
                </a>
                <a href="recommendations.php" class="p-4 border rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="font-medium">Rekomendasi</div>
                    <p class="text-sm text-gray-500">Saran yang dipersonalisasi</p>
                </a>
                <a href="won_auctions.php" class="p-4 border rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="font-medium">Lelang yang dimenangkan</div>
                    <p class="text-sm text-gray-500">Kelola pembelian Anda</p>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Profile Update Section -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-semibold mb-6 text-indigo-600">Pengaturan Profil</h2>
        <form method="post" action="" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Nama depan</label>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><?php echo $user['first_name']; ?></span>
                        <input type="text" name="new_first_name" 
                               class="flex-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                      focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Nama depan baru">
                    </div>
                </div>

                <!-- Last Name -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Nama belakang</label>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><?php echo $user['last_name']; ?></span>
                        <input type="text" name="new_last_name" 
                               class="flex-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                      focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Nama belakang baru">
                    </div>
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><?php echo $user['email']; ?></span>
                        <input type="email" name="new_email" 
                               class="flex-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                      focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Email baru">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">••••••••</span>
                        <input type="password" name="new_password" 
                               class="flex-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                      focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Password baru">
                    </div>
                </div>
            </div>

            <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
            <button type="submit" name="submit" 
                    class="w-full md:w-auto px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white 
                           rounded-lg transition-colors font-medium">
                Update Profil
            </button>
        </form>
    </div>
</div>

<?php include_once("footer.php"); ?>