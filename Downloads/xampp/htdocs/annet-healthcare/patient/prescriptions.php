<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('patient');
$user = currentUser();
$pageTitle = 'My Prescriptions - Annet Healthcare';

$stmt = $pdo->prepare("SELECT p.*, u.name as doctor_name FROM prescriptions p JOIN users u ON p.doctor_id = u.id WHERE p.patient_id = ? ORDER BY p.issued_date DESC");
$stmt->execute([$user['id']]);
$prescriptions = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-800">My Prescriptions</h1>
        <p class="text-slate-500">Medications prescribed to you</p>
    </div>

    <?php if (empty($prescriptions)): ?>
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-slate-100">
            <p class="text-slate-400">No prescriptions yet.</p>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 gap-4">
            <?php foreach ($prescriptions as $p): ?>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-health-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-health-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <span class="text-xs text-slate-500"><?= date('M d, Y', strtotime($p['issued_date'])) ?></span>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1"><?= sanitize($p['medication']) ?></h3>
                    <p class="text-sm text-slate-500 mb-3">Prescribed by <?= sanitize($p['doctor_name']) ?></p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">Dosage:</span><span class="font-medium text-slate-700"><?= sanitize($p['dosage']) ?></span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Frequency:</span><span class="font-medium text-slate-700"><?= sanitize($p['frequency']) ?></span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Duration:</span><span class="font-medium text-slate-700"><?= sanitize($p['duration']) ?></span></div>
                    </div>
                    <?php if ($p['notes']): ?>
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <p class="text-xs text-slate-500"><?= sanitize($p['notes']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>