<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css"
        integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Dashboard</title>
</head>

<body class="bg-gray-100 flex justify-center items-center h-screen">
    <!-- Dashboard Table Container -->
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl mx-4 sm:mx-6 md:mx-8 lg:mx-12 xl:mx-auto">
        <!-- Header Section with Space Between -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
            <h1 class="text-3xl font-semibold text-center text-gray-800 mb-4 sm:mb-0 sm:text-left">Fetched User</h1>
            <!-- Search Bar for filtering -->
            <div class="flex justify-center">
                <div class="relative w-full max-w-lg">
                    <input type="text" id="searchInput" onkeyup="filterUsers()" placeholder="Search users by name"
                        class="w-full p-3 pl-10 pr-4 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-300" />
                    <!-- Icon inside the input field -->
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 4a7 7 0 107 7 7 7 0 00-7-7zm0 0V2M2 11h2M22 11h-2" />
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Download Button with Data -->
            <div class="flex space-x-4">
                <button
                    class="bg-blue-700 text-white py-1 px-4 rounded-lg hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-600"><a
                        href="/uploadUser"><i class="fa-solid fa-cloud-arrow-up"></i> Upload</a>
                </button>
                <button
                    class="bg-blue-700 text-white py-1 px-4 rounded-lg hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-600"
                    onclick="downloadData()">
                    <i class="fa-solid fa-download"></i> Download
                </button>
                <!-- Logout Link -->
                <button>
                    <a href="/logout"
                        class="text-white px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Logout</a>
                </button>
            </div>
        </div>

        <!-- Table Start -->
        <table class="min-w-full table-auto border-collapse" id="userTable">
            <thead>
                <tr class="bg-indigo-600 text-white">
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">UUID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row) { ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo $row->id; ?></td>
                        <td class="px-4 py-2"><?php echo $row->uuid; ?></td>
                        <td class="px-4 py-2"><?php echo $row->name; ?></td>
                        <td class="px-4 py-2"><?php echo $row->email; ?></td>
                        <td class="px-4 py-2 text-center">
                            <!-- Edit Button with Data -->
                            <button
                                class="bg-green-400 text-white py-1 px-4 rounded hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 mr-2"
                                onclick="openEditModal(<?php echo $row->id; ?>, '<?php echo $row->name; ?>', '<?php echo $row->email; ?>','<?php echo $row->uuid; ?>')">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>

                            <!-- Delete Button with Data -->
                            <!-- <button
                                class="bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
                                onclick="confirmDelete(<?php echo $row->id; ?>)">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button> -->
                            <!-- Delete Button with Data -->
                            <button
                                class="bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
                                onclick="confirmDelete('<?php echo $row->uuid; ?>')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <div id="pagination" class="flex justify-between mt-6">
            <button onclick="changePage(-1)"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 focus:outline-none">Previous</button>
            <button onclick="changePage(1)"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 focus:outline-none">Next</button>
        </div>
    </div>
    <!-- Table End -->

    <!-- Edit User Modal -->
    <div id="editModal" class="absolute inset-0 flex justify-center items-center bg-gray-500 bg-opacity-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm mx-4 sm:mx-8">
            <h2 class="text-2xl font-semibold text-center text-gray-800 mb-4">Edit User</h2>
            <form id="editForm" action="/update-user" method="POST">
                <div class="mb-4">
                    <label for="editId" class="block text-gray-700">Id</label>
                    <input type="text" name="id" id="editId" class="w-full p-2 border border-gray-300 rounded mt-2"
                        required>
                </div>
                <div class="mb-4">
                    <label for="editName" class="block text-gray-700">Name</label>
                    <input type="text" name="name" id="editName" class="w-full p-2 border border-gray-300 rounded mt-2"
                        required>
                </div>
                <div class="mb-4">
                    <label for="editEmail" class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="editEmail"
                        class="w-full p-2 border border-gray-300 rounded mt-2" required>
                </div>
                <div class="flex flex-col sm:flex-row justify-between">
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-400 text-white px-4 py-2 rounded mb-2 sm:mb-0">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pagination state variables
        let currentPage = 1;
        const rowsPerPage = 5;

        // Function to show the correct users for the current page
        function paginateUsers() {
            const rows = document.querySelectorAll('#userTable tbody tr');
            const totalRows = rows.length;
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;

            // Hide all rows first
            rows.forEach(row => row.style.display = 'none');

            // Show the rows for the current page
            for (let i = startIndex; i < endIndex && i < totalRows; i++) {
                rows[i].style.display = '';
            }
        }

        // Change page based on direction (-1 for previous, 1 for next)
        function changePage(direction) {
            const rows = document.querySelectorAll('#userTable tbody tr');
            const totalRows = rows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);

            currentPage += direction;

            // Prevent going out of bounds
            if (currentPage < 1) {
                currentPage = 1;
            } else if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            paginateUsers();
        }

        // Initialize pagination on page load
        window.onload = function () {
            paginateUsers();
        };

        // Filter users based on search input
        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#userTable tbody tr');

            rows.forEach(row => {
                const nameCell = row.cells[2];
                const name = nameCell ? nameCell.textContent.toLowerCase() : '';

                if (name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Download data in CSV format
        function downloadData() {
            const table = document.getElementById('userTable');
            const rows = table.getElementsByTagName('tr');

            let csvContent = "ID,UUID,Name,Email\n";
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');

                if (cells.length > 0) {
                    const id = cells[0].textContent.trim();
                    const uuid = cells[1].textContent.trim();
                    const name = cells[2].textContent.trim();
                    const email = cells[3].textContent.trim();
                    csvContent += `${id},${uuid},${name},${email}\n`;
                }
            }

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'users_data.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Open the edit modal and pre-fill the form
        function openEditModal(id, name, email) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editModal').classList.remove('hidden');
        }

        // Close the edit modal
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Confirm deletion and send delete request
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Send DELETE request to backend
                window.location.href = '/delete-user/' + id;
            }
        }
    </script>

</body>

</html>