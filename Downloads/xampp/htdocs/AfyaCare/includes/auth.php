<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function check_csrf(): void { if (!hash_equals(csrf(), (string)($_POST['csrf'] ?? ''))) { http_response_code(403); exit('Invalid request. Refresh and retry.'); } }
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function page(string $title): void { echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' - AfyaCare</title><script src="https://cdn.tailwindcss.com"></script><script src="style.js" defer></script></head><body class="bg-teal-50 min-h-screen flex items-center justify-center p-5"><main class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md"><h1 class="text-3xl font-bold text-teal-700 mb-2">AfyaCare</h1><h2 class="text-xl font-semibold mb-5">'.e($title).'</h2>'; }
function endpage(): void { echo '</main></body></html>'; }
function field(string $name, string $label, string $type='text', string $autocomplete=''): void { echo '<label class="block mb-3 text-sm font-medium">'.e($label).'<input class="mt-1 w-full border rounded-lg p-3 focus:outline-teal-600" type="'.e($type).'" name="'.e($name).'" '.($autocomplete?'autocomplete="'.e($autocomplete).'"':'').' required></label>'; }
function submit_button(string $label): void { echo '<button class="w-full bg-teal-700 hover:bg-teal-800 text-white rounded-lg p-3 font-semibold">'.e($label).'</button>'; }
function message(string $text): void { echo '<p class="bg-amber-50 text-amber-900 p-3 rounded mb-4">'.e($text).'</p>'; }
