<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <title>Login</title>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <!-- Login Form Container -->
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md mx-4 sm:mx-0">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Login</h1>

        <!-- Form Start -->
        <form action="<?= base_url('/login') ?>" method="post">

            <!-- Email Field -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email:</label>
                <input type="email" id="email" name="email"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Enter your email" required>
            </div>

            <!-- Password Field -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password:</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Enter your password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Login
            </button>

            <!-- Signup Link -->
            <p class="text-center text-sm text-gray-600 mt-4">
                Don't have an account? <a href="<?= base_url('/signup') ?>"
                    class="text-indigo-600 hover:text-indigo-700">Sign Up here</a>
            </p>
        </form>
        <!-- Form End -->
    </div>
    <!-- Login Form Container End -->

</body>

</html>