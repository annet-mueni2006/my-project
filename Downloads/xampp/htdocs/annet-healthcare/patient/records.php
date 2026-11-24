<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('patient');
$user = currentUser();
$pageTitle = 'Medical Records - Annet Healthcare';

$stmt = $pdo->prepare("SELECT r.*, u.name as doctor_name FROM medical_records r LEFT JOIN users u ON r.doctor_id = u.id WHERE r.patient_id = ? ORDER BY r.visit_date DESC");
$stmt->execute([$user['id']]);
$records = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Medical Records</h1>
        <p class="text-slate-500">Your complete health history</p>
    </div>

    <?php if (empty($records)): ?>
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-slate-100">
            <p class="text-slate-400">No medical records yet.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($records as $r): ?>
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-purple-500 p-6">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-lg text-slate-800"><?= sanitize($r['diagnosis']) ?></h3>
                            <p class="text-sm text-slate-500">By <?= sanitize($r['doctor_name'] ?? 'N/A') ?> • <?= date('M d, Y', strtotime($r['visit_date'])) ?></p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <p class="text-xs font-semibold text-purple-700 mb-1">TREATMENT</p>
                            <p class="text-sm text-slate-700"><?= nl2br(sanitize($r['treatment'])) ?></p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg">
                            <p class="text-xs font-semibold text-slate-700 mb-1">NOTES</p>
                            <p class="text-sm text-slate-700"><?= nl2br(sanitize($r['notes'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>