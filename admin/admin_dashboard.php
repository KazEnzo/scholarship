<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

// Include the database connection
require_once '../db_connect.php';

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Fetch pending applications count
$pending_query = "SELECT COUNT(*) as pending_count FROM applications WHERE status = 'Under Review'";
$pending_result = $conn->query($pending_query);
$pending_count = $pending_result->fetch_assoc()['pending_count'];

// Fetch approved scholarships count and total awarded amount
$approved_query = "SELECT COUNT(*) as approved_count, COALESCE(SUM(s.amount), 0) as total_awarded 
                  FROM applications a 
                  JOIN scholarships s ON a.scholarship_id = s.id 
                  WHERE a.status = 'Approved'";
$approved_result = $conn->query($approved_query);
$approved_data = $approved_result->fetch_assoc();
$approved_count = $approved_data['approved_count'];
$total_awarded = $approved_data['total_awarded'];

// Fetch denied applications count
$denied_query = "SELECT COUNT(*) as denied_count FROM applications WHERE status = 'Denied'";
$denied_result = $conn->query($denied_query);
$denied_count = $denied_result->fetch_assoc()['denied_count'];

// Fetch total scholarships count
$total_scholarships_query = "SELECT COUNT(*) as total_count FROM scholarships";
$total_scholarships_result = $conn->query($total_scholarships_query);
$total_scholarships_count = $total_scholarships_result->fetch_assoc()['total_count'];

// Fetch all applications for search, filter, and pagination
$apps_query = "SELECT a.id, CONCAT(st.first_name, ' ', st.last_name) as student_name, s.name as scholarship_name, a.date_applied, a.status, a.document_path 
               FROM applications a 
               JOIN students st ON a.student_id = st.id 
               JOIN scholarships s ON a.scholarship_id = s.id 
               ORDER BY a.date_applied DESC";
$apps_result = $conn->query($apps_query);
$all_apps = [];
while ($row = $apps_result->fetch_assoc()) {
    error_log("Application ID {$row['id']}: document_path = " . ($row['document_path'] ?? 'NULL'));
    $all_apps[] = $row;
}

// Fetch available scholarships with application counts and requirements
$scholarships_query = "SELECT s.id, s.name, s.deadline, s.amount, s.requirements, 
                      (SELECT COUNT(*) FROM applications a WHERE a.scholarship_id = s.id) as app_count 
                      FROM scholarships s 
                      ORDER BY s.deadline ASC";
$scholarships_result = $conn->query($scholarships_query);
$scholarships = [];
while ($row = $scholarships_result->fetch_assoc()) {
    $scholarships[] = $row;
}

// Fetch approved applications for modal
$approved_apps_query = "SELECT CONCAT(st.first_name, ' ', st.last_name) as student_name, s.name as scholarship_name 
                       FROM applications a 
                       JOIN students st ON a.student_id = st.id 
                       JOIN scholarships s ON a.scholarship_id = s.id 
                       WHERE a.status = 'Approved'";
$approved_apps_result = $conn->query($approved_apps_query);
$approved_apps = [];
while ($row = $approved_apps_result->fetch_assoc()) {
    $approved_apps[] = $row;
}

// Fetch denied applications for modal
$denied_apps_query = "SELECT CONCAT(st.first_name, ' ', st.last_name) as student_name, s.name as scholarship_name 
                     FROM applications a 
                     JOIN students st ON a.student_id = st.id 
                     JOIN scholarships s ON a.scholarship_id = s.id 
                     WHERE a.status = 'Denied'";
$denied_apps_result = $conn->query($denied_apps_query);
$denied_apps = [];
while ($row = $denied_apps_result->fetch_assoc()) {
    $denied_apps[] = $row;
}

// Fetch awarded amounts by scholarship for modal
$awarded_by_scholarship_query = "SELECT s.name, COALESCE(SUM(s.amount), 0) as awarded_amount 
                                FROM applications a 
                                JOIN scholarships s ON a.scholarship_id = s.id 
                                WHERE a.status = 'Approved' 
                                GROUP BY s.id, s.name";
$awarded_by_scholarship_result = $conn->query($awarded_by_scholarship_query);
$awarded_by_scholarship = [];
while ($row = $awarded_by_scholarship_result->fetch_assoc()) {
    $awarded_by_scholarship[] = $row;
}

$conn->close();

$name = $_SESSION['name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Scholarship Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .status-approved { background-color: #2ECC71; color: white; }
        .status-under-review { background-color: #F1C40F; color: white; }
        .status-denied { background-color: #E74C3C; color: white; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background-color: white; padding: 20px; border-radius: 8px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto; position: relative; }
        .close-btn { position: absolute; top: 10px; right: 10px; font-size: 1.5rem; color: #666; cursor: pointer; background: none; border: none; }
        .close-btn:hover { color: #000; }
        .pagination-container { display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 10px; padding: 10px; background-color: #f5f5f5; border-radius: 8px; }
        .pagination-container button { background-color: #d1d5db; color: #374151; padding: 8px 16px; border-radius: 4px; transition: background-color 0.3s; }
        .pagination-container button:hover:not(:disabled) { background-color: #9ca3af; }
        .pagination-container button:disabled { background-color: #e5e7eb; color: #9ca3af; cursor: not-allowed; }
        .pagination-container span { font-size: 1rem; color: #374151; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-green-600 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">EsyatekIskolar</h1>
        <div class="flex items-center space-x-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span><?php echo htmlspecialchars($name); ?></span>
            <button onclick="logout()" class="bg-green-700 hover:bg-red-800 px-4 py-2 rounded">Logout</button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6">
        <!-- Welcome Section -->
        <section class="bg-green-500 text-white p-6 rounded-lg mb-6">
            <h2 class="text-2xl font-bold">Welcome to your ADMIN DASHBOARD</h2>
            <p>Manage student applications, review submissions, and oversee scholarship awards.</p>
        </section>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Pending Applications</h3>
                    <p class="text-2xl font-bold text-green-600"><?php echo $pending_count; ?></p>
                </div>
                <div class="flex justify-end mt-2">
                    <button onclick="openModal('pendingModal')" class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Show Details</button>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Approved Scholarships</h3>
                    <p class="text-2xl font-bold text-green-600"><?php echo $approved_count; ?></p>
                </div>
                <div class="flex justify-end mt-2">
                    <button onclick="openModal('approvedModal')" class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Show Details</button>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Denied Applications</h3>
                    <p class="text-2xl font-bold text-green-600"><?php echo $denied_count; ?></p>
                </div>
                <div class="flex justify-end mt-2">
                    <button onclick="openModal('deniedModal')" class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Show Details</button>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Total Scholarships</h3>
                    <p class="text-2xl font-bold text-green-600"><?php echo $total_scholarships_count; ?></p>
                </div>
                <div class="flex justify-end mt-2">
                    <button onclick="openModal('scholarshipsModal')" class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Show Details</button>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Total Awarded</h3>
                    <p class="text-2xl font-bold text-green-600">₱<?php echo number_format($total_awarded, 2); ?></p>
                </div>
                <div class="flex justify-end mt-2">
                    <button onclick="openModal('awardedModal')" class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Show Details</button>
                </div>
            </div>
        </div>

        <!-- Pending Applications with Search, Filter, and Pagination -->
        <section class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Applications</h2>
                <a href="#" class="text-green-600 hover:underline">View all</a>
            </div>
            <div class="mb-4 flex space-x-4">
                <input type="text" id="searchStudent" placeholder="Search by student name..." class="p-2 border rounded w-1/2">
                <select id="filterStatus" class="p-2 border rounded">
                    <option value="Under Review" selected>Under Review</option>
                    <option value="Approved">Approved</option>
                    <option value="Denied">Denied</option>
                    <option value="all">All Statuses</option>
                </select>
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full" id="applicationsTable">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-2 px-4 text-left text-gray-700">Student Name</th>
                            <th class="py-2 px-4 text-left text-gray-700">Scholarship Name</th>
                            <th class="py-2 px-4 text-left text-gray-700">Date Applied</th>
                            <th class="py-2 px-4 text-left text-gray-700">Status</th>
                            <th class="py-2 px-4 text-left text-gray-700">File</th>
                            <th class="py-2 px-4 text-left text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody id="applicationsBody">
                        <?php if (empty($all_apps)): ?>
                            <tr>
                                <td colspan="6" class="py-2 px-4 text-center text-gray-500">No applications found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($all_apps as $app): ?>
                                <tr>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($app['student_name']); ?></td>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($app['scholarship_name']); ?></td>
                                    <td class="py-2 px-4"><?php echo date('d M Y', strtotime($app['date_applied'])); ?></td>
                                    <td class="py-2 px-4">
                                        <span class="status-<?php echo strtolower(str_replace(' ', '-', $app['status'])); ?> px-2 py-1 rounded">
                                            <?php echo htmlspecialchars($app['status']); ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <?php
                                        $adjusted_path = '../' . $app['document_path'];
                                        if (!empty($app['document_path']) && file_exists($adjusted_path)): ?>
                                            <button onclick="viewFile('<?php echo htmlspecialchars($app['document_path']); ?>')" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">View File</button>
                                        <?php else: ?>
                                            <span class="text-gray-500">No file</span>
                                            <?php if (!empty($app['document_path'])): ?>
                                                <span class="text-red-500"> (File not found: <?php echo htmlspecialchars($app['document_path']); ?>)</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2 px-4">
                                        <button onclick="approveApplication(<?php echo $app['id']; ?>)" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 mr-2">Approve</button>
                                        <button onclick="denyApplication(<?php echo $app['id']; ?>)" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Deny</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="pagination-container">
                    <button id="prevPage" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Previous</button>
                    <span id="pageInfo"></span>
                    <button id="nextPage" class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400">Next</button>
                </div>
            </div>
        </section>

        <!-- Add Scholarship Form -->
        <section class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Add New Scholarship</h2>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <form id="addScholarshipForm" class="space-y-4">
                    <div>
                        <label for="scholarship_name" class="block text-gray-700">Scholarship Name</label>
                        <input type="text" name="scholarship_name" id="scholarship_name" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label for="deadline" class="block text-gray-700">Deadline</label>
                        <input type="date" name="deadline" id="deadline" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label for="amount" class="block text-gray-700">Amount (₱)</label>
                        <input type="number" name="amount" id="amount" class="w-full p-2 border rounded" step="0.01" required>
                    </div>
                    <div>
                        <label for="requirements" class="block text-gray-700">Requirements</label>
                        <textarea name="requirements" id="requirements" class="w-full p-2 border rounded" rows="4" required placeholder="E.g., Must have a GPA of 3.0 or higher, submit a 500-word essay, etc."></textarea>
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Scholarship</button>
                </form>
            </div>
        </section>

        <!-- Available Scholarships -->
        <section>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Available Scholarships</h2>
                <a href="#" class="text-green-600 hover:underline">View all</a>
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-2 px-4 text-left text-gray-700">Scholarship Name</th>
                            <th class="py-2 px-4 text-left text-gray-700">Deadline</th>
                            <th class="py-2 px-4 text-left text-gray-700">Amount</th>
                            <th class="py-2 px-4 text-left text-gray-700">Requirements</th>
                            <th class="py-2 px-4 text-left text-gray-700">Applications</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($scholarships)): ?>
                            <tr>
                                <td colspan="6" class="py-2 px-4 text-center text-gray-500">No scholarships available.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($scholarships as $sch): ?>
                                <tr>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($sch['name']); ?></td>
                                    <td class="py-2 px-4"><?php echo date('d M Y', strtotime($sch['deadline'])); ?></td>
                                    <td class="py-2 px-4">₱<?php echo number_format($sch['amount'], 2); ?></td>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($sch['requirements']); ?></td>
                                    <td class="py-2 px-4"><?php echo $sch['app_count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white p-4 text-center text-gray-600 text-sm">
        © 2025 EsyatekIskolar. All rights reserved.
    </footer>

    <!-- Modals -->
    <div id="pendingModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('pendingModal')">×</button>
            <h2 class="text-xl font-semibold">Pending Applications</h2>
            <ul class="list-disc pl-5">
                <?php foreach ($all_apps as $app): if ($app['status'] === 'Under Review'): ?>
                    <li><?php echo htmlspecialchars($app['student_name']) . ' - ' . htmlspecialchars($app['scholarship_name']); ?></li>
                <?php endif; endforeach; ?>
            </ul>
        </div>
    </div>
    <div id="approvedModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('approvedModal')">×</button>
            <h2 class="text-xl font-semibold">Approved Scholarships</h2>
            <ul class="list-disc pl-5">
                <?php foreach ($approved_apps as $app): ?>
                    <li><?php echo htmlspecialchars($app['student_name']) . ' - ' . htmlspecialchars($app['scholarship_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div id="deniedModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('deniedModal')">×</button>
            <h2 class="text-xl font-semibold">Denied Applications</h2>
            <ul class="list-disc pl-5">
                <?php foreach ($denied_apps as $app): ?>
                    <li><?php echo htmlspecialchars($app['student_name']) . ' - ' . htmlspecialchars($app['scholarship_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div id="scholarshipsModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('scholarshipsModal')">×</button>
            <h2 class="text-xl font-semibold">Total Scholarships</h2>
            <ul class="list-disc pl-5">
                <?php foreach ($scholarships as $sch): ?>
                    <li><?php echo htmlspecialchars($sch['name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div id="awardedModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('awardedModal')">×</button>
            <h2 class="text-xl font-semibold">Total Awarded</h2>
            <ul class="list-disc pl-5">
                <?php foreach ($awarded_by_scholarship as $item): ?>
                    <li><?php echo htmlspecialchars($item['name']) . ': ₱' . number_format($item['awarded_amount'], 2); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        function logout() {
            window.location.href = '../logout.php';
        }

        function approveApplication(applicationId) {
            if (confirm('Are you sure you want to approve this application?')) {
                fetch('update_application.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'application_id=' + applicationId + '&status=Approved'
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          location.reload();
                      } else {
                          alert('Failed to approve application.');
                      }
                  });
            }
        }

        function denyApplication(applicationId) {
            if (confirm('Are you sure you want to deny this application?')) {
                fetch('update_application.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'application_id=' + applicationId + '&status=Denied'
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          location.reload();
                      } else {
                          alert('Failed to deny application.');
                      }
                  });
            }
        }

        function viewScholarship(scholarshipId) {
            alert('View details for scholarship ID: ' + scholarshipId);
            // In a real application, this would redirect to a details page or open a modal
        }

        function viewFile(documentPath) {
            if (documentPath) {
                const serveFileUrl = '../serve_file.php?file=' + encodeURIComponent(documentPath);
                fetch(serveFileUrl, { method: 'HEAD' })
                    .then(response => {
                        if (response.ok) {
                            window.open(serveFileUrl, '_blank');
                        } else {
                            console.warn('serve_file.php not found, falling back to direct access');
                            window.open('../' + documentPath, '_blank');
                        }
                    })
                    .catch(error => {
                        console.error('Error accessing serve_file.php:', error);
                        window.open('../' + documentPath, '_blank');
                    });
            } else {
                alert('No file available to view.');
            }
        }

        // Handle Add Scholarship Form Submission
        document.getElementById('addScholarshipForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('add_scholarship.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });

        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Pagination and Filter Functionality
        let currentPage = 1;
        const rowsPerPage = 10;

        function updateTable() {
            const searchValue = document.getElementById('searchStudent').value.toLowerCase();
            const filterValue = document.getElementById('filterStatus').value;
            const tableBody = document.getElementById('applicationsBody');
            const rows = tableBody.getElementsByTagName('tr');
            let filteredRows = [];

            for (let row of rows) {
                const studentName = row.getElementsByTagName('td')[0].textContent.toLowerCase();
                const statusCell = row.getElementsByTagName('td')[3];
                const status = statusCell.querySelector('span').textContent.trim();
                const matchSearch = studentName.includes(searchValue);
                const matchStatus = filterValue === 'all' || status === filterValue;

                if (matchSearch && matchStatus) {
                    filteredRows.push(row);
                }
            }

            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;

            for (let row of rows) {
                row.style.display = 'none';
            }

            for (let i = startIndex; i < endIndex && i < filteredRows.length; i++) {
                filteredRows[i].style.display = '';
            }

            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('prevPage').disabled = currentPage === 1;
            document.getElementById('nextPage').disabled = currentPage === totalPages || totalPages === 0;
        }

        document.getElementById('searchStudent').addEventListener('input', () => {
            currentPage = 1;
            updateTable();
        });

        document.getElementById('filterStatus').addEventListener('change', () => {
            currentPage = 1;
            updateTable();
        });

        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            const filterValue = document.getElementById('filterStatus').value;
            const searchValue = document.getElementById('searchStudent').value.toLowerCase();
            const tableBody = document.getElementById('applicationsBody');
            const rows = tableBody.getElementsByTagName('tr');
            let filteredRows = [];

            for (let row of rows) {
                const studentName = row.getElementsByTagName('td')[0].textContent.toLowerCase();
                const statusCell = row.getElementsByTagName('td')[3];
                const status = statusCell.querySelector('span').textContent.trim();
                const matchSearch = studentName.includes(searchValue);
                const matchStatus = filterValue === 'all' || status === filterValue;

                if (matchSearch && matchStatus) {
                    filteredRows.push(row);
                }
            }

            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();
            }
        });

        // Initialize table on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateTable();
        });
    </script>
</body>
</html>