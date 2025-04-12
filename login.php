<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <form class="bg-white shadow-lg rounded-lg px-8 pt-8 pb-8 mb-4" action="login_result.php" method="post">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Welcome Back</h2>
                <p class="text-gray-600 mt-2">Please sign in to your account</p>
                
                <!-- Error Message -->
                <?php if (isset($_GET['message'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($_GET['message']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="inputemail">
                    Email Address
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           id="inputemail" type="text" placeholder="your@email.com" required autofocus name="email">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="inputPassword">
                    Password
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-lock text-gray-400"></i>
                    </span>
                    <input class="shadow appearance-none border rounded w-full py-3 pl-10 pr-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           id="inputPassword" type="password" placeholder="••••••••" required name="password">
                </div>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-gray-700">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" value="remember-me">
                    <span class="ml-2 text-sm">Remember me</span>
                </label>
                <a class="inline-block align-baseline text-sm text-blue-500 hover:text-blue-800" href="#">
                    Forgot Password?
                </a>
            </div>

            <div class="flex flex-col space-y-4">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-200" 
                        type="submit">
                    Sign In
                </button>
                
            </div>
        </form>
        
        
    </div>
</body>

</html>