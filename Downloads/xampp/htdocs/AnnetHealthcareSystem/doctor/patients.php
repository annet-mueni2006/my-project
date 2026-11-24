<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('doctor');
$user = currentUser();
$pageTitle = 'My Patients - Annet Healthcare';

$search = trim($_GET['search'] ?? '');
$sql = "SELECT DISTINCT u.* FROM users u WHERE u.role='patient'";
$params = [];
if ($search) {
    $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $params = ["%$search%", "%$search%"];
}
$sql .= " ORDER BY u.name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$patients = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">My Patients</h1>
            <p class="text-slate-500">Browse and manage patient files</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="<?= sanitize($search) ?>" placeholder="Search name or email..." class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none text-sm w-64">
            <button class="bg-medical-600 hover:bg-medical-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">Search</button>
        </form>
    </div>

    <?php if (empty($patients)): ?>
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-slate-100">
            <p class="text-slate-400">No patients found.</p>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($patients as $p): ?>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-full bg-medical-100 flex items-center justify-center text-medical-700 font-bold text-lg">
                            <?= strtoupper(substr($p['name'], 0, 1)) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800 truncate"><?= sanitize($p['name']) ?></p>
                            <p class="text-xs text-slate-500 truncate"><?= sanitize($p['email']) ?></p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 mb-4"><?= sanitize($p['phone'] ?: 'No phone') ?></p>
                    <a href="consultation.php?patient_id=<?= $p['id'] ?>" class="block text-center bg-health-600 hover:bg-health-700 text-white py-2 rounded-lg text-sm font-semibold transition">Open File</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>