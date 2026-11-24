<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('patient');
$user = currentUser();
$pageTitle = 'My Vitals - Annet Healthcare';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $bp = trim($_POST['blood_pressure']);
    $hr = (int)$_POST['heart_rate'];
    $temp = (float)$_POST['temperature'];
    $weight = (float)$_POST['weight'];
    $height = (float)$_POST['height'];
    $notes = trim($_POST['notes']);

    $stmt = $pdo->prepare("INSERT INTO vitals (patient_id, blood_pressure, heart_rate, temperature, weight, height, notes) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$user['id'], $bp, $hr, $temp, $weight, $height, $notes]);
    flash('success', 'Vitals recorded.');
    redirect('vitals.php');
}

$stmt = $pdo->prepare("SELECT * FROM vitals WHERE patient_id = ? ORDER BY recorded_at DESC");
$stmt->execute([$user['id']]);
$vitals = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">My Vitals</h1>
            <p class="text-slate-500">Track your health metrics</p>
        </div>
        <button onclick="document.getElementById('vitalsForm').classList.toggle('hidden')" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-semibold transition">+ Add Vitals</button>
    </div>

    <?php if ($msg = flash('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6"><?= sanitize($msg) ?></div>
    <?php endif; ?>

    <div id="vitalsForm" class="hidden bg-white p-6 rounded-xl shadow-sm border border-slate-100 mb-6">
        <h3 class="font-bold text-lg text-slate-800 mb-4">Record New Vitals</h3>
        <form method="POST" class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Blood Pressure</label>
                <input type="text" name="blood_pressure" placeholder="120/80" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Heart Rate (bpm)</label>
                <input type="number" name="heart_rate" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Temperature (°C)</label>
                <input type="number" step="0.1" name="temperature" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Weight (kg)</label>
                <input type="number" step="0.1" name="weight" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Height (cm)</label>
                <input type="number" step="0.1" name="height" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <input type="text" name="notes" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div class="md:col-span-3">
                <button type="submit" name="add" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg font-semibold transition">Save Vitals</button>
            </div>
        </form>
    </div>

    <?php if (empty($vitals)): ?>
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-slate-100">
            <p class="text-slate-400">No vitals recorded yet.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-left">
                        <tr>
                            <th class="px-6 py-3 font-medium">Date</th>
                            <th class="px-6 py-3 font-medium">BP</th>
                            <th class="px-6 py-3 font-medium">Heart Rate</th>
                            <th class="px-6 py-3 font-medium">Temp</th>
                            <th class="px-6 py-3 font-medium">Weight</th>
                            <th class="px-6 py-3 font-medium">Height</th>
                            <th class="px-6 py-3 font-medium">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($vitals as $v): ?>
                            <tr>
                                <td class="px-6 py-4 text-slate-600"><?= date('M d, Y H:i', strtotime($v['recorded_at'])) ?></td>
                                <td class="px-6 py-4 font-medium text-slate-700"><?= sanitize($v['blood_pressure']) ?></td>
                                <td class="px-6 py-4 text-slate-600"><?= $v['heart_rate'] ?> bpm</td>
                                <td class="px-6 py-4 text-slate-600"><?= $v['temperature'] ?>°C</td>
                                <td class="px-6 py-4 text-slate-600"><?= $v['weight'] ?> kg</td>
                                <td class="px-6 py-4 text-slate-600"><?= $v['height'] ?> cm</td>
                                <td class="px-6 py-4 text-slate-500"><?= sanitize($v['notes']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>