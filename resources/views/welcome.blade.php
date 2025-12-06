<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM-Pro - Multi-Tenant HR Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            padding: 40px;
        }

        .logo {
            font-size: 3rem;
            color: #667eea;
        }
    </style>
</head>

<body>
    <div class="welcome-card text-center">
        <div class="logo mb-4">
            <i class="bi bi-building-fill"></i>
        </div>
        <h1 class="mb-3">HRM-Pro</h1>
        <p class="text-muted mb-4">Multi-Tenant HR Management System</p>

        <div class="d-grid gap-3">
            <a href="/login" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </a>
            <a href="/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
            </a>
        </div>

        <hr class="my-4">

        <div class="text-start small">
            <h6>Features:</h6>
            <ul class="list-unstyled">
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Employee Management</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Attendance Tracking</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Payroll Processing</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Leave Management</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Recruitment (ATS)</li>
            </ul>
        </div>
    </div>
</body>

</html>