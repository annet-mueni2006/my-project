<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('patient');
$user = currentUser();
$pageTitle = 'Patient Dashboard - Annet Healthcare';

// Stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status IN ('pending','accepted','rescheduled')");
$stmt->execute([$user['id']]);
$upcoming = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM prescriptions WHERE patient_id = ?");
$stmt->execute([$user['id']]);
$prescCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM medical_records WHERE patient_id = ?");
$stmt->execute([$user['id']]);
$recordsCount = $stmt->fetchColumn();

// Latest vitals
$stmt = $pdo->prepare("SELECT * FROM vitals WHERE patient_id = ? ORDER BY recorded_at DESC LIMIT 1");
$stmt->execute([$user['id']]);
$latestVitals = $stmt->fetch();

// Recent appointments
$stmt = $pdo->prepare("SELECT a.*, u.name as doctor_name FROM appointments a JOIN users u ON a.doctor_id = u.id WHERE a.patient_id = ? ORDER BY a.created_at DESC LIMIT 5");
$stmt->execute([$user['id']]);
$appointments = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Welcome, <?= sanitize($user['name']) ?> 👋</h1>
        <p class="text-slate-500">Here's your health overview</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-medical-500">
            <p class="text-slate-500 text-sm">Upcoming Appointments</p>
            <p class="text-3xl font-bold text-medical-600"><?= $upcoming ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-health-500">
            <p class="text-slate-500 text-sm">Prescriptions</p>
            <p class="text-3xl font-bold text-health-600"><?= $prescCount ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500">
            <p class="text-slate-500 text-sm">Medical Records</p>
            <p class="text-3xl font-bold text-purple-600"><?= $recordsCount ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-orange-500">
            <p class="text-slate-500 text-sm">Latest Vitals</p>
            <p class="text-sm font-bold text-orange-600"><?= $latestVitals ? sanitize($latestVitals['blood_pressure'] ?? 'N/A') : 'No data' ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-bold text-lg text-slate-800">Recent Appointments</h2>
            <a href="appointments.php" class="text-sm text-medical-600 font-semibold hover:underline">View All →</a>
        </div>
        <?php if (empty($appointments)): ?>
            <div class="p-8 text-center text-slate-400">No appointments yet. <a href="appointments.php" class="text-medical-600 font-semibold">Book one now</a></div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-left">
                        <tr>
                            <th class="px-6 py-3 font-medium">Doctor</th>
                            <th class="px-6 py-3 font-medium">Date</th>
                            <th class="px-6 py-3 font-medium">Time</th>
                            <th class="px-6 py-3 font-medium">Reason</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($appointments as $a): ?>
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-700"><?= sanitize($a['doctor_name']) ?></td>
                                <td class="px-6 py-4 text-slate-600"><?= date('M d, Y', strtotime($a['date'])) ?></td>
                                <td class="px-6 py-4 text-slate-600"><?= date('h:i A', strtotime($a['time'])) ?></td>
                                <td class="px-6 py-4 text-slate-600"><?= sanitize($a['reason']) ?></td>
                                <td class="px-6 py-4">
                                    <?php
                                    $colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'accepted' => 'bg-green-100 text-green-700',
                                        'rescheduled' => 'bg-blue-100 text-blue-700',
                                        'completed' => 'bg-slate-100 text-slate-700',
                                        'cancelled' => 'bg-red-100 text-red-700'
                                    ];
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $colors[$a['status']] ?? '' ?>"><?= ucfirst($a['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>