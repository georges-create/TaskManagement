<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management System | TMS Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #fff0f6;
            margin: 0;
            color: #333;
        }

        a {
            text-decoration: none;
        }

        /* Navbar */
        .navbar-pink {
            background-color: #e91e63 !important;
        }

        .navbar-pink .navbar-brand,
        .navbar-pink .nav-link {
            color: #ffe4f0 !important;
            font-weight: 600;
        }

        .navbar-pink .nav-link:hover {
            opacity: 0.85;
        }

        /* Hero Section */
        .hero {
            padding: 100px 0;
            text-align: center;
            background: linear-gradient(to right, #fce4ec, #f8bbd0);
        }

        .hero h1 {
            font-weight: 700;
            font-size: 2.8rem;
            color: #e91e63;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 30px;
        }

        .btn-primary-custom {
            background: #e91e63;
            color: #fff;
            border: none;
            padding: 12px 28px;
            font-weight: 500;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-primary-custom:hover {
            background: #d81b60;
        }

        /* Feature Cards */
        .section-title {
            font-weight: 700;
            color: #e91e63;
            margin-bottom: 40px;
        }

        .feature-card {
            background: #fff;
            border-radius: 15px;
            padding: 35px 25px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #e91e63;
            margin-bottom: 15px;
        }

        /* CTA Section */
        .cta-section {
            background: #e91e63;
            color: #fff;
            padding: 70px 0;
            text-align: center;
        }

        .cta-section h4 {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .btn-light-custom {
            background: #fff;
            color: #e91e63;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-light-custom:hover {
            background: #f8bbd0;
            color: #e91e63;
        }

        /* Footer */
        footer {
            background: #e91e63;
            color: #ffe4f0;
            font-size: 14px;
            text-align: center;
            width: 100%;
            padding: 15px 0;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-pink shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Task Management System</a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="login.php" class="nav-link">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="nav-link">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container">
            <h1>Organize Classes & Schedules Efficiently</h1>
            <p>Manage timetables, lecture schedules, units, venues, and programs seamlessly. Download and generate timetables quickly for students and staff.</p>
            <a href="register.php" class="btn btn-primary-custom">Get Started</a>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="py-5">
        <div class="container">
            <h3 class="text-center mb-5 section-title">Key Features</h3>
            <div class="row g-4">

                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-calendar2-week"></i>
                        </div>
                        <h5>Timetable Management</h5>
                        <p>Create and organize lecture schedules, units, and programs. Manage daily, weekly, or semester timetables efficiently.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h5>Venue & Resource Planning</h5>
                        <p>Allocate classrooms, labs, and lecture halls seamlessly. Avoid conflicts and ensure all sessions have proper venues.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-download"></i>
                        </div>
                        <h5>Download & Share Timetables</h5>
                        <p>Generate downloadable timetables for students, lecturers, and staff. Share schedules instantly in PDF or Excel formats.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="cta-section">
        <div class="container">
            <h4>Simplify Task Management Today</h4>
            <p>Sign up now and start managing class schedules, venues, and programs effortlessly with our intuitive platform.</p>
            <a href="register.php" class="btn btn-light-custom mt-3">Create Your Account</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> TSM. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>