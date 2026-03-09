<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - WatAtlas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    .navbar-custom {
      background-color: #003366; /* your custom navbar color */
    }
    body {
      background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e') no-repeat center center fixed;
      background-size: cover;
    }
    .login-card {
      background: rgba(255, 255, 255, 0.9); /* semi-transparent for readability */
      border-radius: 8px;
    }
  </style>
</head>
<body>
  <!-- Login Form -->
  <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm p-4 login-card" style="max-width: 400px; width: 100%;">
      <h3 class="text-center mb-4">Login</h3>
        <form method="post" action="login_process.php">
        <!-- Username -->
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-user"></i></span>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
        </div>
        <!-- Organization -->
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-building"></i></span>
            <input type="text" class="form-control" id="organization" name="organization" placeholder="Organization" required>
        </div>
        <!-- Password -->
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
