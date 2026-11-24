<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('doctor');
$user = currentUser();
$pageTitle = 'Consultation - Annet Healthcare';

$patientId = (int)($_GET['patient_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_record'])) {
        $pid = (int)$_POST['patient_id'];
        $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, notes, visit_date) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$pid, $user['id'], $_POST['diagnosis'], $_POST['treatment'], $_POST['notes'], $_POST['visit_date']]);
        flash('success', 'Medical record added.');
        redirect("consultation.php?patient_id=$pid");
    }
    if (isset($_POST['add_presc'])) {
        $pid = (int)$_POST['patient_id'];
        $stmt = $pdo->prepare("INSERT INTO prescriptions (patient_id, doctor_id, medication, dosage, frequency, duration, notes, issued_date) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$pid, $user['id'], $_POST['medication'], $_POST['dosage'], $_POST['frequency'], $_POST['duration'], $_POST['notes'], date('Y-m-d')]);
        flash('success', 'Prescription added.');
        redirect("consultation.php?patient_id=$pid");
    }
    if (isset($_POST['send_email'])) {
        $pid = (int)$_POST['patient_id'];
        $subject = $_POST['subject'];
        $body = $_POST['body'];
        // In a real app, use mail(). Here we log/save as message.
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, subject, body) VALUES (?,?,?,?)");
        $stmt->execute([$user['id'], $pid, $subject, $body]);
        flash('success', 'Message sent to patient (email simulated).');
        redirect("consultation.php?patient_id=$pid");
    }
}

// Determine patient list
$patients = $pdo->query("SELECT id, name FROM users WHERE role='patient' ORDER BY name")->fetchAll();
$patient = null;
$records = [];
$prescriptions = [];
$vitals = [];

if ($patientId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='patient'");
    $stmt->execute([$patientId]);
    $patient = $stmt->fetch();

    if ($patient) {
        $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE patient_id=? ORDER BY visit_date DESC");
        $stmt->execute([$patientId]);
        $records = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id=? ORDER BY issued_date DESC");
        $stmt->execute([$patientId]);
        $prescriptions = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM vitals WHERE patient_id=? ORDER BY recorded_at DESC LIMIT 5");
        $stmt->execute([$patientId]);
        $vitals = $stmt->fetchAll();
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Consultation</h1>
        <p class="text-slate-500">Manage patient files and consultations</p>
    </div>

    <?php if ($msg = flash('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6"><?= sanitize($msg) ?></div>
    <?php endif; ?>

    <div class="mb-6">
        <form method="GET" class="flex gap-2 items-center">
            <label class="text-sm font-medium text-slate-600">Select Patient:</label>
            <select name="patient_id" onchange="this.form.submit()" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none text-sm">
                <option value="">— Choose a patient —</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $p['id']==$patientId?'selected':'' ?>><?= sanitize($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($patient): ?>
        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            <!-- Add Medical Record -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg text-slate-800 mb-4">Add Medical Record</h2>
                <form method="POST" class="space-y-3">
                    <input type="hidden" name="patient_id" value="<?= $patientId ?>">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Diagnosis</label>
                        <input type="text" name="diagnosis" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Treatment</label>
                        <textarea name="treatment" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Visit Date</label>
                        <input type="date" name="visit_date" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                    </div>
                    <button type="submit" name="add_record" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg font-semibold transition text-sm">Save Record</button>
                </form>
            </div>

            <!-- Add Prescription -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg text-slate-800 mb-4">Add Prescription</h2>
                <form method="POST" class="space-y-3">
                    <input type="hidden" name="patient_id" value="<?= $patientId ?>">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Medication</label>
                        <input type="text" name="medication" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-health-500 outline-none text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Dosage</label>
                            <input type="text" name="dosage" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-health-500 outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Frequency</label>
                            <input type="text" name="frequency" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-health-500 outline-none text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Duration</label>
                        <input type="text" name="duration" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-health-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-health-500 outline-none text-sm"></textarea>
                    </div>
                    <button type="submit" name="add_presc" class="w-full bg-health-600 hover:bg-health-700 text-white py-2 rounded-lg font-semibold transition text-sm">Save Prescription</button>
                </form>
            </div>
        </div>

        <!-- Send Message / Email -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
            <h2 class="font-bold text-lg text-slate-800 mb-4">Send Message to Patient</h2>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="patient_id" value="<?= $patientId ?>">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Subject</label>
                    <input type="text" name="subject" required value="Next Appointment" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                    <textarea name="body" rows="3" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-medical-500 outline-none text-sm" placeholder="Your next appointment is..."></textarea>
                </div>
                <button type="submit" name="send_email" class="bg-medical-600 hover:bg-medical-700 text-white px-5 py-2 rounded-lg font-semibold transition text-sm">Send Email</button>
            </form>
        </div>

        <!-- Patient History -->
        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg text-slate-800 mb-3">Medical History</h2>
                <?php if (empty($records)): ?><p class="text-slate-400 text-sm">No records.</p><?php else: ?>
                    <div class="space-y-2 max-h-72 overflow-y-auto">
                        <?php foreach ($records as $r): ?>
                            <div class="p-3 bg-purple-50 rounded-lg">
                                <p class="font-semibold text-sm text-slate-800"><?= sanitize($r['diagnosis']) ?></p>
                                <p class="text-xs text-slate-500"><?= date('M d, Y', strtotime($r['visit_date'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg text-slate-800 mb-3">Recent Vitals</h2>
                <?php if (empty($vitals)): ?><p class="text-slate-400 text-sm">No vitals.</p><?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($vitals as $v): ?>
                            <div class="p-3 bg-orange-50 rounded-lg flex justify-between text-sm">
                                <span class="text-slate-600"><?= date('M d, H:i', strtotime($v['recorded_at'])) ?></span>
                                <span class="font-semibold text-slate-800">BP <?= sanitize($v['blood_pressure']) ?> | HR <?= $v['heart_rate'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-slate-100">
            <p class="text-slate-400">Select a patient to begin consultation.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>