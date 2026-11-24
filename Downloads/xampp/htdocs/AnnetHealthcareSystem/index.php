<?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) redirect('/annet-healthcare/dashboard.php');
$pageTitle = 'Annet Healthcare - Your Health, Our Priority';
include __DIR__ . '/includes/header.php';
?>

<section class="bg-gradient-to-br from-medical-50 via-white to-health-50">
    <div class="max-w-7xl mx-auto px-4 py-20 grid md:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block bg-medical-100 text-medical-700 px-4 py-1 rounded-full text-sm font-semibold mb-4">Trusted Care</span>
            <h1 class="text-5xl md:text-6xl font-bold text-slate-800 leading-tight mb-6">
                Your Health,<br><span class="text-medical-600">Our Priority.</span>
            </h1>
            <p class="text-lg text-slate-600 mb-8">
                Annet Healthcare connects patients and doctors in one seamless platform. Book appointments, access medical records, track vitals, and get prescriptions — all in one place.
            </p>
            <div class="flex gap-4 flex-wrap">
                <a href="register.php" class="bg-medical-600 hover:bg-medical-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition">Get Started</a>
                <a href="login.php" class="bg-white border-2 border-medical-600 text-medical-600 hover:bg-medical-50 px-6 py-3 rounded-lg font-semibold transition">Login</a>
            </div>
        </div>
        <div class="relative">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-medical-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-health-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-health-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Comprehensive Care</h3>
                        <p class="text-sm text-slate-500">All your health needs in one place</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-medical-100 rounded-full flex items-center justify-center text-medical-600 font-bold text-sm">✓</div>
                        <span class="text-slate-700">24/7 appointment booking</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-medical-100 rounded-full flex items-center justify-center text-medical-600 font-bold text-sm">✓</div>
                        <span class="text-slate-700">Secure medical records</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-medical-100 rounded-full flex items-center justify-center text-medical-600 font-bold text-sm">✓</div>
                        <span class="text-slate-700">Real-time vitals tracking</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-medical-100 rounded-full flex items-center justify-center text-medical-600 font-bold text-sm">✓</div>
                        <span class="text-slate-700">Direct doctor communication</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-center text-slate-800 mb-12">Our Services</h2>
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="w-12 h-12 bg-medical-100 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-medical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-bold text-lg text-slate-800 mb-2">Appointments</h3>
            <p class="text-slate-600 text-sm">Book and manage your appointments with our expert doctors easily.</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="w-12 h-12 bg-health-100 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-health-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-bold text-lg text-slate-800 mb-2">Medical Records</h3>
            <p class="text-slate-600 text-sm">Access your complete medical history securely anytime, anywhere.</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <h3 class="font-bold text-lg text-slate-800 mb-2">Prescriptions</h3>
            <p class="text-slate-600 text-sm">Receive and manage prescriptions directly from your doctor.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>