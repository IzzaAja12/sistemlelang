<?php
require_once('database.php');

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.
session_start();

// Create database connection
$connection = db_connect();

// Extract $_POST variables
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare SQL query
$query = "SELECT * FROM Admins WHERE email = '$email' AND password = '$password'";
$result = db_query($connection, $query);
confirm_result_set($result);
$user = db_fetch_single($result);


if (db_num_rows($result) === 1) {

    // Login successful
    $_SESSION['logged_in'] = true;
    $_SESSION['email'] = $email;
    $_SESSION['admin'] = true;

    // TODO: Set 'account_type' or other session variables as needed
    $_SESSION['admin_id'] = $user['admin_id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];
   

    echo ('
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes draw {
            to { stroke-dashoffset: 0; }
        }
        @keyframes progress {
            from { width: 0; }
            to { width: 100%; }
        }
        .animate-progress {
            animation: progress 2s ease-in-out forwards;
        }
        .checkmark-path {
            stroke-dasharray: 1;
            stroke-dashoffset: 1;
            animation: draw 0.6s ease-out forwards 0.3s;
        }
        .checkmark-circle {
            stroke-dasharray: 1;
            stroke-dashoffset: 1;
            animation: draw 0.8s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Success Redirect Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-xl shadow-2xl max-w-md w-full mx-4 text-center transform transition-all duration-300">
            <div class="flex flex-col items-center justify-center gap-5">
                <!-- Animated Checkmark -->
                <div class="relative">
                    <svg class="w-16 h-16 text-green-500" viewBox="0 0 24 24" fill="none">
                        <!-- Background Circle -->
                        <circle cx="12" cy="12" r="10" stroke="#E5E7EB" stroke-width="2"/>
                        <!-- Checkmark Path -->
                        <path 
                            class="stroke-current checkmark-path" 
                            stroke-width="2" 
                            stroke-linecap="round" 
                            stroke-linejoin="round"
                            d="M7 13l3 3 7-7"
                            pathLength="1"
                        />
                        <!-- Animated Circle -->
                        <circle 
                            cx="12" cy="12" r="10" 
                            class="stroke-current checkmark-circle" 
                            stroke-width="2" 
                            stroke-linecap="round"
                            fill="transparent"
                            pathLength="1"
                        />
                    </svg>
                </div>
                
                <!-- Success Message -->
                <div class="space-y-1">
                    <h3 class="text-2xl font-bold text-gray-800">Login Berhasil!</h3>
                    <p class="text-gray-600">Kamu akan diarahkan sebentar lagi...</p>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-1.5 rounded-full animate-progress"></div>
                </div>
                
                <!-- Loading Text -->
                <p class="text-xs text-gray-500 mt-2">Harap tunggu...</p>
            </div>
        </div>
    </div>

   
</body>
</html>
    ');
    header("refresh:2;url=admin_dashboard.php");
} else {
    // Login failed
    header('location: admin_login.php?message=Invalid email or password.');
}

// Free result set and close database connection
db_free_result($result);
db_disconnect($connection);
