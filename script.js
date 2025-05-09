document.addEventListener('DOMContentLoaded', () => {
    const applicationForm = document.getElementById('applicationForm');
    const applicationsDiv = document.getElementById('applications');

    if (applicationForm) {
        loadScholarships();
        applicationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(applicationForm);
            fetch('apply.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    applicationForm.reset();
                    loadStatus();
                }
            });
        });
        loadStatus();
    }

    if (applicationsDiv) {
        loadApplications();
    }
});

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('sidebar-hidden');
}

function loadScholarships() {
    fetch('get_scholarships.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('scholarship_id');
            select.innerHTML = data.scholarships.length ? 
                data.scholarships.map(s => `<option value="${s.id}">${s.name} (Deadline: ${s.deadline})</option>`).join('') : 
                '<option value="">No open scholarships</option>';
        });
}

function loadStatus() {
    fetch('get_status.php')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('status');
            statusDiv.innerHTML = data.applications.length ? 
                data.applications.map(app => `
                    <div class="p-4 border-b">
                        <p class="text-gray-700"><strong>Scholarship:</strong> ${app.scholarship_name}</p>
                        <p class="text-gray-700"><strong>Application Date:</strong> ${new Date(app.application_date).toLocaleDateString()}</p>
                        <p class="text-gray-700"><strong>Status:</strong> <span class="${app.status === 'approved' ? 'text-green-600' : app.status === 'rejected' ? 'text-red-600' : 'text-yellow-600'}">${app.status.charAt(0).toUpperCase() + app.status.slice(1)}</span></p>
                    </div>
                `).join('') : 
                '<p class="text-gray-600">No applications submitted.</p>';
        });
}

function loadApplications() {
    fetch('get_applications.php')
        .then(response => response.json())
        .then(data => {
            const applicationsDiv = document.getElementById('applications');
            applicationsDiv.innerHTML = data.applications.map(app => `
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <p class="text-gray-700"><strong>Student:</strong> ${app.student_name}</p>
                    <p class="text-gray-700"><strong>Scholarship:</strong> ${app.scholarship_name}</p>
                    <p class="text-gray-700"><strong>Application Date:</strong> ${new Date(app.application_date).toLocaleDateString()}</p>
                    <p class="text-gray-700"><a href="Uploads/${app.documents}" target="_blank" class="text-blue-600 hover:underline">View Documents</a></p>
                    <div class="mt-4 flex gap-2">
                        <button onclick="updateStatus(${app.id}, 'approved')" class="py-2 px-4 bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                        <button onclick="updateStatus(${app.id}, 'rejected')" class="py-2 px-4 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                    </div>
                </div>
            `).join('');
        });
}

function updateStatus(applicationId, status) {
    fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: applicationId, status })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadApplications();
    });
}

function logout() {
    fetch('logout.php')
        .then(() => {
            window.location.href = 'index.html';
        });
}