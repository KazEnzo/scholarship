<?php
session_start();

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scholarship_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Fetch active applications count
$student_id = $_SESSION['student_id'];
$active_query = "SELECT COUNT(*) as active_count FROM applications WHERE student_id = ? AND status = 'Under Review'";
$active_stmt = $conn->prepare($active_query);
$active_stmt->bind_param("i", $student_id);
$active_stmt->execute();
$active_result = $active_stmt->get_result();
$active_count = $active_result->fetch_assoc()['active_count'];
$active_stmt->close();

// Fetch awarded scholarships count and total awarded amount
$awarded_query = "SELECT COUNT(*) as awarded_count, COALESCE(SUM(s.amount), 0) as total_awarded 
                 FROM applications a 
                 JOIN scholarships s ON a.scholarship_id = s.id 
                 WHERE a.student_id = ? AND a.status = 'Approved'";
$awarded_stmt = $conn->prepare($awarded_query);
$awarded_stmt->bind_param("i", $student_id);
$awarded_stmt->execute();
$awarded_result = $awarded_stmt->get_result();
$awarded_data = $awarded_result->fetch_assoc();
$awarded_count = $awarded_data['awarded_count'];
$total_awarded = $awarded_data['total_awarded'];
$awarded_stmt->close();

// Fetch new opportunities count (scholarships the student hasn't applied for)
$new_opportunities_query = "SELECT COUNT(*) as new_count 
                           FROM scholarships s 
                           WHERE s.deadline >= CURDATE() 
                           AND s.id NOT IN (SELECT scholarship_id FROM applications WHERE student_id = ?)";
$new_stmt = $conn->prepare($new_opportunities_query);
$new_stmt->bind_param("i", $student_id);
$new_stmt->execute();
$new_result = $new_stmt->get_result();
$new_opportunities_count = $new_result->fetch_assoc()['new_count'];
$new_stmt->close();

// Fetch student's applications
$apps_query = "SELECT a.id, s.name, a.date_applied, s.amount, a.status 
               FROM applications a 
               JOIN scholarships s ON a.scholarship_id = s.id 
               WHERE a.student_id = ? 
               ORDER BY a.date_applied DESC";
$apps_stmt = $conn->prepare($apps_query);
$apps_stmt->bind_param("i", $student_id);
$apps_stmt->execute();
$apps_result = $apps_stmt->get_result();
$applications = [];
while ($row = $apps_result->fetch_assoc()) {
    $applications[] = $row;
}
$apps_stmt->close();

// Fetch available scholarships for dropdown (scholarships the student hasn't applied for)
$avail_query = "SELECT s.id, s.name, s.amount, s.deadline, s.requirements 
                FROM scholarships s 
                WHERE s.deadline >= CURDATE() 
                AND s.id NOT IN (SELECT scholarship_id FROM applications WHERE student_id = ?) 
                ORDER BY s.deadline ASC";
$avail_stmt = $conn->prepare($avail_query);
$avail_stmt->bind_param("i", $student_id);
$avail_stmt->execute();
$avail_result = $avail_stmt->get_result();
$available_scholarships = [];
while ($row = $avail_result->fetch_assoc()) {
    $available_scholarships[] = $row;
}
$avail_stmt->close();

$conn->close();

$name = $_SESSION['name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Scholarship Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .status-approved { background-color: #2ECC71; color: white; }
        .status-under-review { background-color: #F1C40F; color: white; }
        .status-denied { background-color: #E74C3C; color: white; }
        .status-message { position: fixed; top: 10px; left: 50%; transform: translateX(-50%); padding: 10px 20px; border-radius: 5px; color: white; font-size: 14px; z-index: 1000; }
        .status-success { background-color: #2ECC71; }
        .status-error { background-color: #E74C3C; }
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
            <h2 class="text-2xl font-bold">Welcome to your SCHOLARSHIP DASHBOARD</h2>
            <p>Track your applications, discover new opportunities, and manage your scholarship journey all in one place.</p>
        </section>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Active Applications</h3>
                <p class="text-2xl font-bold text-green-600"><?php echo $active_count; ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Awarded Scholarships</h3>
                <p class="text-2xl font-bold text-green-600"><?php echo $awarded_count; ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">New Opportunities</h3>
                <p class="text-2xl font-bold text-green-600"><?php echo $new_opportunities_count; ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Total Awarded</h3>
                <p class="text-2xl font-bold text-green-600">₱<?php echo number_format($total_awarded, 2); ?></p>
            </div>
        </div>

        <!-- Apply for Scholarship -->
        <section class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Apply for a Scholarship</h2>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="mb-4">
                    <label for="scholarship_select" class="block text-gray-700 font-medium">Select Scholarship</label>
                    <select id="scholarship_select" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">-- Select a Scholarship --</option>
                        <?php foreach ($available_scholarships as $sch): ?>
                            <option value="<?php echo $sch['id']; ?>" 
                                    data-name="<?php echo htmlspecialchars($sch['name']); ?>" 
                                    data-amount="<?php echo number_format($sch['amount'], 2); ?>" 
                                    data-deadline="<?php echo date('d M Y', strtotime($sch['deadline'])); ?>" 
                                    data-requirements="<?php echo htmlspecialchars($sch['requirements']); ?>">
                                <?php echo htmlspecialchars($sch['name']); ?> (₱<?php echo number_format($sch['amount'], 2); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="scholarship_details" class="hidden bg-gray-100 p-4 rounded mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Scholarship Details</h3>
                    <p><strong>Name:</strong> <span id="detail_name"></span></p>
                    <p><strong>Amount:</strong> ₱<span id="detail_amount"></span></p>
                    <p><strong>Deadline:</strong> <span id="detail_deadline"></span></p>
                    <p><strong>Requirements:</strong> <span id="detail_requirements"></span></p>
                </div>
                <button id="apply_button" class="py-2 px-4 bg-green-600 text-white rounded hover:bg-green-700" disabled>Apply Now</button>
            </div>
        </section>

        <!-- Your Applications -->
        <section class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Your Applications</h2>
                <a href="#" class="text-green-600 hover:underline">View all</a>
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-2 px-4 text-left text-gray-700">Scholarship Name</th>
                            <th class="py-2 px-4 text-left text-gray-700">Date Applied</th>
                            <th class="py-2 px-4 text-left text-gray-700">Amount</th>
                            <th class="py-2 px-4 text-left text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="5" class="py-2 px-4 text-center text-gray-500">No applications yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($app['name']); ?></td>
                                    <td class="py-2 px-4"><?php echo date('d M Y', strtotime($app['date_applied'])); ?></td>
                                    <td class="py-2 px-4">₱<?php echo number_format($app['amount'], 2); ?></td>
                                    <td class="py-2 px-4">
                                        <span class="status-<?php echo strtolower(str_replace(' ', '-', $app['status'])); ?> px-2 py-1 rounded">
                                            <?php echo htmlspecialchars($app['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Apply Modal -->
    <div id="applyModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Apply for Scholarship</h2>
            <p class="mb-4"><strong>Scholarship:</strong> <span id="modal_scholarship_name"></span></p>
            <form id="applicationForm" action="../apply.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="scholarship_id" name="scholarship_id">
                <div class="mb-4">
                    <label for="document" class="block text-gray-700 font-medium">Upload Documents (PDF)</label>
                    <input type="file" id="document" name="document" accept=".pdf" required class="w-full p-2 border rounded">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeApplyModal()" class="py-2 px-4 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="py-2 px-4 bg-green-600 text-white rounded hover:bg-green-700">Submit Application</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white p-4 text-center text-gray-600 text-sm">
        © 2025 EsyatekIskolar. All rights reserved.
    </footer>

    <script>
        function logout() {
            window.location.href = '../logout.php';
        }

        function openApplyModal(scholarshipId, scholarshipName) {
            document.getElementById('scholarship_id').value = scholarshipId;
            document.getElementById('modal_scholarship_name').textContent = scholarshipName;
            document.getElementById('applyModal').classList.remove('hidden');
        }

        function closeApplyModal() {
            document.getElementById('applyModal').classList.add('hidden');
        }

        function viewDetails(applicationId) {
            alert('View details for application ID: ' + applicationId);
            // In a real application, this would redirect to a details page or open a modal
        }

        // Scholarship selection logic
        document.getElementById('scholarship_select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const detailsDiv = document.getElementById('scholarship_details');
            const applyButton = document.getElementById('apply_button');

            if (this.value) {
                document.getElementById('detail_name').textContent = selectedOption.getAttribute('data-name');
                document.getElementById('detail_amount').textContent = selectedOption.getAttribute('data-amount');
                document.getElementById('detail_deadline').textContent = selectedOption.getAttribute('data-deadline');
                document.getElementById('detail_requirements').textContent = selectedOption.getAttribute('data-requirements');
                detailsDiv.classList.remove('hidden');
                applyButton.disabled = false;
                applyButton.onclick = () => openApplyModal(this.value, selectedOption.getAttribute('data-name'));
            } else {
                detailsDiv.classList.add('hidden');
                applyButton.disabled = true;
                applyButton.onclick = null;
            }
        });

        // Display success/error messages
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const error = urlParams.get('error');
            if (success || error) {
                const statusDiv = document.createElement('div');
                statusDiv.className = 'status-message ' + (success ? 'status-success' : 'status-error');
                statusDiv.textContent = success === 'applicationreceived' ? 'Application submitted successfully!' :
                                       error === 'missingscholarship' ? 'No scholarship selected.' :
                                       error === 'nodocument' ? 'Please upload a document.' :
                                       error === 'invalidfiletype' ? 'Only PDF files are allowed.' :
                                       error === 'uploadfailed' ? 'Failed to upload document.' :
                                       error === 'applicationfailed' ? 'Failed to submit application.' :
                                       'An error occurred.';
                document.body.appendChild(statusDiv);
                setTimeout(() => statusDiv.remove(), 5000);
            }
        });
    </script>
</body>
</html>