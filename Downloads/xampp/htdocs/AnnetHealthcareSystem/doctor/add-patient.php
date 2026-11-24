<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('doctor');
$user = currentUser();
$pageTitle = 'Add Patient - Annet Healthcare';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    if (!$name || !$email || !$password) {
        $error = 'Name, email and password are required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?,?,?,'patient',?)");
            $stmt->execute([$name, $email, $hash, $phone]);
            flash('success', 'Patient added successfully.');
            redirect('patients.php');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Add New Patient</h1>
        <p class="text-slate-500">Register a new patient into the system</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Temporary Password *</label>
                <input type="text" name="password" required minlength="6" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none" placeholder="At least 6 characters">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-health-600 hover:bg-health-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">Add Patient</button>
                <a href="patients.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>