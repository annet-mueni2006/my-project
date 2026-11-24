 <?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) redirect('/annet-healthcare/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        redirect('/annet-healthcare/dashboard.php');
    } else {
        $error = 'Invalid email or password.';
    }
}

$pageTitle = 'Login - Annet Healthcare';
include __DIR__ . '/includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-slate-100">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Welcome Back</h1>
            <p class="text-slate-500 text-sm">Login to your Annet account</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
            </div>
            <button type="submit" class="w-full bg-medical-600 hover:bg-medical-700 text-white py-2.5 rounded-lg font-semibold transition">Login</button>
        </form>

        <div class="mt-6 p-4 bg-medical-50 rounded-lg text-xs text-medical-800">
            <p class="font-semibold mb-1">Demo Credentials:</p>
            <p>Doctor: doctor@annet.com / password123</p>
            <p>Patient: patient@annet.com / password123</p>
        </div>

        <p class="text-center text-sm text-slate-500 mt-6">No account? <a href="register.php" class="text-medical-600 font-semibold hover:underline">Register</a></p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>