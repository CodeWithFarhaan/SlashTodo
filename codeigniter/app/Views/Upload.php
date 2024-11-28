<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css"
        integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body class="bg-gray-100 p-6 md:p-10">
    <!-- Main Container -->
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <button
            class="bg-blue-700 text-white py-1 px-4 rounded-lg hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-600"><a
                href="/dashboard"><i class="fa-solid fa-backward"></i> Back</a></button>
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Register CSV Users From Here...</h2>

        <!-- Upload Form -->
        <form action="/uploadUser/upload" method="post" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>

            <!-- File Upload Input -->
            <div class="flex flex-col">
                <label for="csv_file" class="text-lg font-medium text-gray-700 mb-2">Choose CSV File</label>
                <input type="file" id="csv_file" name="csv_file"
                    class="block w-full text-sm text-gray-500 file:border file:border-gray-300 file:rounded-md file:px-4 file:py-2 file:text-sm file:font-semibold file:bg-gray-50 hover:file:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 border rounded"
                    required>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit"
                    class="bg-blue-700 text-white py-1 px-4 rounded-lg hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-600"><a
                        href="/uploadUser"><i class="fa-solid fa-cloud-arrow-up"></i> Upload</a>
                </button>
            </div>
        </form>
    </div>
</body>

</html>