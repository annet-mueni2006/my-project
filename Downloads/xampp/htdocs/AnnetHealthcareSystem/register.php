<?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) redirect('/annet-healthcare/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'patient';
    $phone = trim($_POST['phone'] ?? '');

    if (!$name || !$email || !$password) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?,?,?,?,?)");
            $stmt->execute([$name, $email, $hash, $role, $phone]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            redirect('/annet-healthcare/dashboard.php');
        }
    }
}

$pageTitle = 'Register - Annet Healthcare';
include __DIR__ . '/includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-slate-100">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Create Account</h1>
            <p class="text-slate-500 text-sm">Join Annet Healthcare today</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 focus:border-medical-500 outline-none" placeholder="John Doe">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 focus:border-medical-500 outline-none" placeholder="you@example.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 focus:border-medical-500 outline-none" placeholder="+254...">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required minlength="6" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 focus:border-medical-500 outline-none" placeholder="Min 6 characters">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">I am a</label>
                <select name="role" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-medical-600 hover:bg-medical-700 text-white py-2.5 rounded-lg font-semibold transition">Create Account</button>
        </form>
        <p class="text-center text-sm text-slate-500 mt-6">Already have an account? <a href="login.php" class="text-medical-600 font-semibold hover:underline">Login</a></p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>