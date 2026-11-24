<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('doctor');
$user = currentUser();
$pageTitle = 'My Schedule - Annet Healthcare';

$weekStart = $_GET['week'] ?? date('Y-m-d', strtotime('monday this week'));
$weekStart = date('Y-m-d', strtotime($weekStart));
$weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));

$stmt = $pdo->prepare("SELECT a.*, u.name as patient_name FROM appointments a JOIN users u ON a.patient_id = u.id WHERE a.doctor_id = ? AND a.date BETWEEN ? AND ? ORDER BY a.date, a.time");
$stmt->execute([$user['id'], $weekStart, $weekEnd]);
$appts = $stmt->fetchAll();

$byDate = [];
foreach ($appts as $a) $byDate[$a['date']][] = $a;

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Weekly Schedule</h1>
            <p class="text-slate-500"><?= date('M d', strtotime($weekStart)) ?> - <?= date('M d, Y', strtotime($weekEnd)) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="?week=<?= date('Y-m-d', strtotime($weekStart . ' -7 days')) ?>" class="bg-white border border-slate-300 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium">← Prev</a>
            <a href="?week=<?= date('Y-m-d', strtotime('monday this week')) ?>" class="bg-medical-600 hover:bg-medical-700 text-white px-4 py-2 rounded-lg text-sm font-medium">This Week</a>
            <a href="?week=<?= date('Y-m-d', strtotime($weekStart . ' +7 days')) ?>" class="bg-white border border-slate-300 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium">Next →</a>
        </div>
    </div>

    <div class="grid md:grid-cols-7 gap-3">
        <?php for ($i = 0; $i < 7; $i++):
            $d = date('Y-m-d', strtotime($weekStart . " +$i days"));
            $isToday = $d === date('Y-m-d');
        ?>
            <div class="bg-white rounded-xl shadow-sm border <?= $isToday ? 'border-medical-400 ring-2 ring-medical-100' : 'border-slate-100' ?> overflow-hidden">
                <div class="px-3 py-2 <?= $isToday ? 'bg-medical-600 text-white' : 'bg-slate-50 text-slate-700' ?>">
                    <p class="text-xs font-medium"><?= date('D', strtotime($d)) ?></p>
                    <p class="text-lg font-bold"><?= date('j', strtotime($d)) ?></p>
                </div>
                <div class="p-2 space-y-1 min-h-[120px]">
                    <?php if (!empty($byDate[$d])): foreach ($byDate[$d] as $a): ?>
                        <div class="bg-medical-50 border-l-2 border-medical-500 p-2 rounded text-xs">
                            <p class="font-semibold text-slate-800"><?= date('h:i A', strtotime($a['time'])) ?></p>
                            <p class="text-slate-600 truncate"><?= sanitize($a['patient_name']) ?></p>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="text-xs text-slate-300 text-center pt-6">Free</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>