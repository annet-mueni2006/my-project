<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('doctor');
$user = currentUser();
$pageTitle = 'Doctor Dashboard - Annet Healthcare';

// Handle appointment response (accept / reschedule)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respond'])) {
    $appt_id = (int)$_POST['appt_id'];
    $status = $_POST['status'];
    $response = trim($_POST['response'] ?? '');
    $new_date = $_POST['new_date'] ?? null;
    $new_time = $_POST['new_time'] ?? null;

    if ($status === 'rescheduled' && $new_date && $new_time) {
        $stmt = $pdo->prepare("UPDATE appointments SET status=?, doctor_response=?, date=?, time=? WHERE id=? AND doctor_id=?");
        $stmt->execute([$status, $response, $new_date, $new_time, $appt_id, $user['id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE appointments SET status=?, doctor_response=? WHERE id=? AND doctor_id=?");
        $stmt->execute([$status, $response, $appt_id, $user['id']]);
    }
    flash('success', 'Appointment response saved.');
    redirect('dashboard.php');
}

// Today's appointments
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT a.*, u.name as patient_name FROM appointments a JOIN users u ON a.patient_id = u.id WHERE a.doctor_id = ? AND a.date = ? AND a.status IN ('accepted','pending') ORDER BY a.time");
$stmt->execute([$user['id'], $today]);
$todayAppts = $stmt->fetchAll();

// Pending requests
$stmt = $pdo->prepare("SELECT a.*, u.name as patient_name FROM appointments a JOIN users u ON a.patient_id = u.id WHERE a.doctor_id = ? AND a.status = 'pending' ORDER BY a.created_at DESC");
$stmt->execute([$user['id']]);
$requests = $stmt->fetchAll();

// Readings to review (latest patient vitals)
$stmt = $pdo->prepare("SELECT v.*, u.name as patient_name FROM vitals v JOIN users u ON v.patient_id = u.id ORDER BY v.recorded_at DESC LIMIT 10");
$stmt->execute();
$readings = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Welcome, Dr. <?= sanitize(str_replace('Dr. ','',$user['name'])) ?></h1>
        <p class="text-slate-500"><?= date('l, F j, Y') ?></p>
    </div>

    <?php if ($msg = flash('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6"><?= sanitize($msg) ?></div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        <!-- Today's Schedule -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h2 class="font-bold text-lg text-slate-800">Today's Schedule</h2>
                <span class="text-xs bg-medical-100 text-medical-700 px-2.5 py-1 rounded-full font-semibold"><?= count($todayAppts) ?> appointments</span>
            </div>
            <?php if (empty($todayAppts)): ?>
                <p class="p-6 text-slate-400 text-center text-sm">No appointments today.</p>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($todayAppts as $a): ?>
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-slate-700"><?= sanitize($a['patient_name']) ?></p>
                                <p class="text-sm text-slate-500"><?= sanitize($a['reason']) ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-medical-600"><?= date('h:i A', strtotime($a['time'])) ?></p>
                                <span class="text-xs <?= $a['status']==='accepted' ? 'text-green-600' : 'text-yellow-600' ?>"><?= ucfirst($a['status']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Vitals to Review -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="font-bold text-lg text-slate-800">Readings to Review</h2>
            </div>
            <?php if (empty($readings)): ?>
                <p class="p-6 text-slate-400 text-center text-sm">No recent readings.</p>
            <?php else: ?>
                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    <?php foreach ($readings as $r): ?>
                        <div class="px-6 py-3 flex items-center justify-between text-sm">
                            <div>
                                <p class="font-medium text-slate-700"><?= sanitize($r['patient_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= date('M d, H:i', strtotime($r['recorded_at'])) ?></p>
                            </div>
                            <div class="text-right text-xs">
                                <p><span class="text-slate-500">BP:</span> <span class="font-semibold"><?= sanitize($r['blood_pressure']) ?></span></p>
                                <p><span class="text-slate-500">HR:</span> <span class="font-semibold"><?= $r['heart_rate'] ?></span></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-lg text-slate-800">Appointment Requests</h2>
        </div>
        <?php if (empty($requests)): ?>
            <p class="p-6 text-slate-400 text-center text-sm">No pending requests.</p>
        <?php else: ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($requests as $r): ?>
                    <div class="px-6 py-5">
                        <div class="flex justify-between items-start mb-3 flex-wrap gap-2">
                            <div>
                                <p class="font-semibold text-slate-800"><?= sanitize($r['patient_name']) ?></p>
                                <p class="text-sm text-slate-500">Reason: <?= sanitize($r['reason']) ?></p>
                                <p class="text-sm text-slate-500">Requested: <?= date('M d, Y', strtotime($r['date'])) ?> at <?= date('h:i A', strtotime($r['time'])) ?></p>
                            </div>
                        </div>
                        <form method="POST" class="grid md:grid-cols-4 gap-3 items-end bg-slate-50 p-4 rounded-lg">
                            <input type="hidden" name="appt_id" value="<?= $r['id'] ?>">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Action</label>
                                <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-500 outline-none" onchange="this.closest('form').querySelector('.resched').classList.toggle('hidden', this.value !== 'rescheduled')">
                                    <option value="accepted">Accept</option>
                                    <option value="rescheduled">Reschedule</option>
                                    <option value="cancelled">Cancel</option>
                                </select>
                            </div>
                            <div class="resched hidden">
                                <label class="block text-xs font-medium text-slate-600 mb-1">New Date</label>
                                <input type="date" name="new_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-500 outline-none">
                            </div>
                            <div class="resched hidden">
                                <label class="block text-xs font-medium text-slate-600 mb-1">New Time</label>
                                <input type="time" name="new_time" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Message to Patient</label>
                                <input type="text" name="response" placeholder="Optional note..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-500 outline-none">
                            </div>
                            <div class="md:col-span-4 flex justify-end">
                                <button type="submit" name="respond" class="bg-medical-600 hover:bg-medical-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">Send Response</button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>