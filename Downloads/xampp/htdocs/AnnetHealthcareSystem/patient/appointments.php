<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('patient');
$user = currentUser();
$pageTitle = 'My Appointments - Annet Healthcare';

// Handle new appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $doctor_id = (int)$_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = trim($_POST['reason']);
    if ($doctor_id && $date && $time) {
        $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, date, time, reason) VALUES (?,?,?,?,?)");
        $stmt->execute([$user['id'], $doctor_id, $date, $time, $reason]);
        flash('success', 'Appointment request sent to doctor.');
        redirect('appointments.php');
    }
}

$doctors = $pdo->query("SELECT id, name FROM users WHERE role='doctor'")->fetchAll();
$stmt = $pdo->prepare("SELECT a.*, u.name as doctor_name FROM appointments a JOIN users u ON a.doctor_id = u.id WHERE a.patient_id = ? ORDER BY a.date DESC, a.time DESC");
$stmt->execute([$user['id']]);
$appointments = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">My Appointments</h1>
            <p class="text-slate-500">Book and manage your visits</p>
        </div>
        <button onclick="document.getElementById('bookForm').classList.toggle('hidden')" class="bg-medical-600 hover:bg-medical-700 text-white px-5 py-2.5 rounded-lg font-semibold transition">+ Book Appointment</button>
    </div>

    <?php if ($msg = flash('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6"><?= sanitize($msg) ?></div>
    <?php endif; ?>

    <div id="bookForm" class="hidden bg-white p-6 rounded-xl shadow-sm border border-slate-100 mb-6">
        <h3 class="font-bold text-lg text-slate-800 mb-4">Book New Appointment</h3>
        <form method="POST" class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Doctor</label>
                <select name="doctor_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
                    <option value="">Select doctor</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= sanitize($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Date</label>
                <input type="date" name="date" required min="<?= date('Y-m-d') ?>" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Time</label>
                <input type="time" name="time" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Reason</label>
                <input type="text" name="reason" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none" placeholder="e.g., Checkup">
            </div>
            <div class="md:col-span-2">
                <button type="submit" name="book" class="bg-health-600 hover:bg-health-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">Send Request</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <?php if (empty($appointments)): ?>
            <p class="p-8 text-center text-slate-400">No appointments found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-left">
                        <tr>
                            <th class="px-6 py-3 font-medium">Doctor</th>
                            <th class="px-6 py-3 font-medium">Date & Time</th>
                            <th class="px-6 py-3 font-medium">Reason</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Doctor Response</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($appointments as $a): ?>
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-700"><?= sanitize($a['doctor_name']) ?></td>
                                <td class="px-6 py-4 text-slate-600"><?= date('M d, Y', strtotime($a['date'])) ?> @ <?= date('h:i A', strtotime($a['time'])) ?></td>
                                <td class="px-6 py-4 text-slate-600"><?= sanitize($a['reason']) ?></td>
                                <td class="px-6 py-4">
                                    <?php $colors = ['pending'=>'bg-yellow-100 text-yellow-700','accepted'=>'bg-green-100 text-green-700','rescheduled'=>'bg-blue-100 text-blue-700','completed'=>'bg-slate-100 text-slate-700','cancelled'=>'bg-red-100 text-red-700']; ?>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $colors[$a['status']] ?>"><?= ucfirst($a['status']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 text-sm"><?= sanitize($a['doctor_response'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>