<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php")?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $connection = db_connect();
    $user_id = $_POST['user_id'];

    // Determine the action: update or delete
    $action = $_POST['action'];

    if ($action == 'update') {
        // Sanitize and prepare data for updating
        $email = $_POST['email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];

        // Update query
        $query = "UPDATE Users SET email = '$email', first_name = '$first_name', last_name = '$last_name' WHERE user_id = $user_id";

        if (db_query($connection, $query)) {
            header("Location: admin_users.php?message=berhasil merubah data user");

        } else {
            header("Location: admin_users.php?message=gagal merubah data user");

        }
    } elseif ($action == 'delete') {
        // Delete query
        $query = "DELETE FROM Users WHERE user_id = $user_id";

        if (db_query($connection, $query)) {
            header("Location: admin_users.php?message=berhasil menghapus data user");

        } else {
            header("Location: admin_users.php?message=gagal menghapus data user");

        }
    }

    db_disconnect($connection);
} else {
    header("Location: admin_users.php?message=berhasil merubah data user");
}
?>
