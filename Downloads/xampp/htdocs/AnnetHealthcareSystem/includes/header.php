<?php
require_once __DIR__ . '/functions.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Annet Healthcare' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        medical: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd',
                            300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9',
                            600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e'
                        },
                        health: {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0',
                            300: '#86efac', 400: '#4ade80', 500: '#22c55e',
                            600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d'
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

<?php if (isLoggedIn()): ?>
<nav class="bg-white shadow-sm border-b border-medical-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-3">
                <a href="/annet-healthcare/dashboard.php" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-medical-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-medical-700">Annet <span class="text-health-600">Healthcare</span></span>
                </a>
            </div>

            <div class="hidden md:flex items-center gap-6">
                <?php if ($user['role'] === 'patient'): ?>
                    <a href="/annet-healthcare/patient/dashboard.php" class="text-slate-600 hover:text-medical-600 font-medium">Dashboard</a>
                    <a href="/annet-healthcare/patient/appointments.php" class="text-slate-600 hover:text-medical-600 font-medium">Appointments</a>
                    <a href="/annet-healthcare/patient/records.php" class="text-slate-600 hover:text-medical-600 font-medium">Records</a>
                    <a href="/annet-healthcare/patient/prescriptions.php" class="text-slate-600 hover:text-medical-600 font-medium">Prescriptions</a>
                    <a href="/annet-healthcare/patient/vitals.php" class="text-slate-600 hover:text-medical-600 font-medium">Vitals</a>
                <?php else: ?>
                    <a href="/annet-healthcare/doctor/dashboard.php" class="text-slate-600 hover:text-medical-600 font-medium">Today</a>
                    <a href="/annet-healthcare/doctor/schedule.php" class="text-slate-600 hover:text-medical-600 font-medium">Schedule</a>
                    <a href="/annet-healthcare/doctor/patients.php" class="text-slate-600 hover:text-medical-600 font-medium">Patients</a>
                    <a href="/annet-healthcare/doctor/consultation.php" class="text-slate-600 hover:text-medical-600 font-medium">Consultation</a>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-semibold text-slate-700"><?= sanitize($user['name']) ?></p>
                    <p class="text-xs text-slate-500 capitalize"><?= $user['role'] ?></p>
                </div>
                <a href="/annet-healthcare/logout.php" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-2 rounded-lg text-sm font-medium">Logout</a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="flex-1">