<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.html");
    exit;
}

// Include the database connection
require_once '../db_connect.php';

// Fetch available scholarships with requirements
$scholarships_query = "SELECT id, name, requirements FROM scholarships WHERE deadline >= CURDATE()";
$scholarships_result = $conn->query($scholarships_query);
$scholarships = [];
while ($row = $scholarships_result->fetch_assoc()) {
    $scholarships[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Application - EsyatekIskolar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <main class="p-6">
        <h2 class="text-2xl font-bold mb-4">Submit Scholarship Application</h2>
        <form id="applicationForm" action="submit_application.php" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow">
            <div class="mb-4">
                <label for="scholarship_id" class="block text-gray-700">Select Scholarship</label>
                <select name="scholarship_id" id="scholarship_id" class="w-full p-2 border rounded" required>
                    <option value="">Choose a scholarship</option>
                    <?php foreach ($scholarships as $sch): ?>
                        <option value="<?php echo $sch['id']; ?>" data-requirements="<?php echo htmlspecialchars($sch['requirements']); ?>">
                            <?php echo htmlspecialchars($sch['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="requirements_display" class="block text-gray-700">Requirements</label>
                <textarea id="requirements_display" class="w-full p-2 border rounded" rows="4" readonly></textarea>
            </div>
            <div class="mb-4">
                <label for="document" class="block text-gray-700">Upload Document (PDF, JPG, PNG) - Required</label>
                <input type="file" name="document" id="document" accept=".pdf,.jpg,.png" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Submit Application</button>
        </form>
    </main>
    <script>
        // Display requirements when a scholarship is selected
        document.getElementById('scholarship_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const requirements = selectedOption.getAttribute('data-requirements') || 'No requirements specified.';
            document.getElementById('requirements_display').value = requirements;
        });

        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('submit_application.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    window.location.href = 'student_dashboard.php';
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
        });
    </script>
</body>
</html>