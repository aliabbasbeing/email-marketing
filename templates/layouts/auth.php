<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Email Marketing Tool' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .auth-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="auth-bg min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="glass rounded-xl shadow-2xl p-8">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <i class="fas fa-envelope-open-text text-4xl text-white mb-4"></i>
                <h1 class="text-3xl font-bold text-white">Email Marketing</h1>
                <p class="text-gray-200 mt-2">Professional Email Campaign Management</p>
            </div>
            
            <!-- Flash messages -->
            <?php if (isset($success) && $success): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error) && $error): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <!-- Page content -->
            <?= $content ?>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-8 text-white text-sm">
            <p>&copy; <?= date('Y') ?> Email Marketing Tool. All rights reserved.</p>
        </div>
    </div>
</body>
</html>