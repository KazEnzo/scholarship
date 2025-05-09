<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EsyatekIskolar - Scholarship Management System</title>
    <style>
        a:link {
            color: white;
            background-color: transparent;
            text-decoration: none;
        }
        a:visited {
            color: white;
            background-color: transparent;
            text-decoration: none;
        }
        :root {
            --primary: #00674f;
            --primary-dark: #002c22;
            --secondary: #2ecc71;
            --dark: #002c22;
            --light: #ecf0f1;
            --danger: #e74c3c;
            --warning: #f39c12;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 80%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: var(--primary);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        
        nav a:hover {
            opacity: 0.8;
        }
        
        .auth-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--secondary);
            color: white;
        }
        
        .btn-outline {
            background-color: #2ecc71;
            border: 1px solid white;
            color: #ffffff;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .main-content {
            margin: 2rem 0;
        }
        
        .hero {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .hero p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .search-bar {
            display: flex;
            margin: 2rem auto;
            max-width: 600px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 1rem;
        }
        
        .search-bar button {
            padding: 12px 20px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 2rem;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: var(--primary);
            color: white;
            padding: 15px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .card h3 {
            margin-bottom: 10px;
        }
        
        .card p {
            color: #666;
            margin-bottom: 15px;
        }
        
        .card-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .amount {
            font-weight: bold;
            color: var(--secondary);
        }
        
        .deadline {
            color: #666;
            font-size: 0.9rem;
        }
        
        .apply-btn {
            background-color: var(--primary);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .apply-btn:hover {
            background-color: var(--primary-dark);
        }
        
        form {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        textarea {
            height: 150px;
            resize: vertical;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }
        
        .sidebar {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }
        
        .sidebar h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar li {
            margin-bottom: 10px;
        }
        
        .sidebar a {
            display: block;
            padding: 10px;
            color: var(--dark);
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background-color: #f5f7fa;
            color: var(--primary);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .stat-card p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .data-table th {
            background-color: #f5f7fa;
            font-weight: 600;
        }
        
        .data-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #ffe0b2;
            color: #e65100;
        }
        
        .status-approved {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        
        .status-rejected {
            background-color: #ffcdd2;
            color: #c62828;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        
        .btn-view {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-edit {
            background-color: var(--warning);
            color: white;
        }
        
        .btn-delete {
            background-color: var(--danger);
            color: white;
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }
        
        .footer-column h3 {
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column li {
            margin-bottom: 10px;
        }
        
        .footer-column a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column a:hover {
            color: white;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid #44546280;
            color: #bdc3c7;
        }
        
        /* Login/Register */
        .auth-container {
            max-width: 500px;
            margin: 2rem auto;
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 20px;
        }
        
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            background-color: #f5f7fa;
            cursor: pointer;
        }
        
        .auth-tab.active {
            background-color: white;
            border-top: 3px solid var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            max-width: 500px;
            width: 90%;
            position: relative;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-content h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .modal-content p {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .modal-content ul {
            list-style: none;
        }

        .modal-content li {
            margin-bottom: 10px;
            color: #666;
            font-size: 1rem;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            background: none;
            border: none;
        }

        .close-btn:hover {
            color: var(--dark);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            header .container {
                flex-direction: column;
                gap: 15px;
            }
            
            nav ul {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">EsyatekIskolar</div>
            <nav>
                <ul>
                    <li><a href="#" class="active">Home</a></li>
                    <li><a href="scholarships.html">Scholarships</a></li>
                    <li><a href="#" id="aboutUsLink">About Us</a></li>
                    <li><a href="#" id="contactUsLink">Contact Us</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <button class="btn btn-outline"><a href="index.html">Login</a></button>
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="container">
            <div class="hero">
                <h1>Find the Perfect Scholarship</h1>
                <p>Discover a wide range of scholarships and take control of your educational future</p>
            </div>

            <h2>Featured Scholarships</h2>
            <div class="cards">
                <div class="card">
                    <div class="card-header">
                        <h3>Academicare Scholarship</h3>
                    </div>
                    <div class="card-body">
                        <p>In line with our commitment to providing educational opportunities during challenging times and our dedication to your future.</p>
                        <p><strong>Requirements:</strong> Incoming Freshmen</p>
                    </div>
                    <div class="card-footer">
                        <div>
                            <div class="amount">Php. 11,500</div>
                            <div class="deadline">Deadline: May 30, 2025</div>
                        </div>
                        <button class="apply-btn"><a href="index.html">Apply Now</a></button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Future Leaders Grant</h3>
                    </div>
                    <div class="card-body">
                        <p>Supporting students who have demonstrated exceptional leadership qualities and community involvement.</p>
                        <p><strong>Requirements:</strong> Leadership experience</p>
                    </div>
                    <div class="card-footer">
                        <div>
                            <div class="amount">Php. 3,500</div>
                            <div class="deadline">Deadline: June 15, 2025</div>
                        </div>
                        <button class="apply-btn"><a href="index.html">Apply Now</a></button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Creative Arts Scholarship</h3>
                    </div>
                    <div class="card-body">
                        <p>For talented students in visual arts, music, theater, or creative writing looking to pursue their passion.</p>
                        <p><strong>Requirements:</strong> Portfolio submission</p>
                    </div>
                    <div class="card-footer">
                        <div>
                            <div class="amount">Php. 2,500</div>
                            <div class="deadline">Deadline: June 30, 2025</div>
                        </div>
                        <button class="apply-btn"><a href="index.html">Apply Now</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Us Modal -->
    <div id="aboutUsModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('aboutUsModal')">×</button>
            <h2>About Us</h2>
            <p>
                EsyatekIskolar is dedicated to helping students achieve their educational dreams by connecting them with scholarships that match their needs and qualifications. Our platform simplifies the scholarship application process, providing a seamless experience for students in the Philippines and beyond. Join us to discover opportunities and take control of your future!
            </p>
        </div>
    </div>

    <!-- Contact Us Modal -->
    <div id="contactUsModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('contactUsModal')">×</button>
            <h2>Contact Us</h2>
            <ul>
                <li>Email: info@esyatekiskolar.com</li>
                <li>Phone: (555) 123-4567</li>
                <li>Address: 1506, Entrance of Golden City, Brgy. Dila, Santa Rosa, Philippines</li>
            </ul>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>EsyatekIskolar</h3>
                    <p>Connecting students with scholarships to help them achieve their educational goals.</p>
                </div>
                <div class="footer-column">
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="scholarships.html">Scholarships</a></li>
                        <li><a href="#" id="aboutUsLinkFooter">About Us</a></li>
                        <li><a href="#" id="contactUsLinkFooter">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul>
                        <li>Email: info@esyatekiskolar.com</li>
                        <li>Phone: (555) 123-4567</li>
                        <li>Address: 1506, Entrance of Golden City, Brgy. Dila , Santa Rosa, Philippines</li>
                    </ul>
                </div>
                <div class="footer-column">
                </div>
            </div>
            <div class="copyright">
                © 2025 EsyatekIskolar. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Modal Functions (Moved to global scope)
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Show/Hide forms based on navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Auth tabs functionality
            const tabs = document.querySelectorAll('.auth-tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    tabContents[index].classList.add('active');
                });
            });

            // Event Listeners for Modal Links (Header)
            document.getElementById('aboutUsLink').addEventListener('click', function(e) {
                e.preventDefault();
                openModal('aboutUsModal');
            });

            document.getElementById('contactUsLink').addEventListener('click', function(e) {
                e.preventDefault();
                openModal('contactUsModal');
            });

            // Event Listeners for Modal Links (Footer)
            document.getElementById('aboutUsLinkFooter').addEventListener('click', function(e) {
                e.preventDefault();
                openModal('aboutUsModal');
            });

            document.getElementById('contactUsLinkFooter').addEventListener('click', function(e) {
                e.preventDefault();
                openModal('contactUsModal');
            });

            // Close Modal on Outside Click
            window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    closeModal(e.target.id);
                }
            });
            
            console.log('EsyatekIskolar System Initialized');
        });
    </script>
</body>
</html>