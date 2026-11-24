
<?php
session_start();

$status = $_SESSION['contact_status'] ?? null;
unset($_SESSION['contact_status']);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Us | Annet Agricultural Initiative</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-green-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Reusable Header -->
    <div id="header"></div>

    <main class="flex-grow">

        <!-- Page Heading -->
        <section class="bg-green-700 text-white py-16 text-center px-6">

            <h1 class="text-4xl font-bold mb-4">
                Contact Us
            </h1>

            <p>
                Get in touch with Annet Agricultural Initiative.
            </p>

        </section>

        <!-- Contact Section -->
        <section class="max-w-6xl mx-auto px-6 py-16">

            <div class="grid md:grid-cols-2 gap-10">

                <!-- Contact Details -->
                <div>

                    <h2 class="text-3xl font-bold text-green-800 mb-6">
                        Get in Touch
                    </h2>

                    <p class="mb-8 leading-8">
                        Have questions about sustainable farming,
                        agricultural training, or our programs?

                        Send us a message and we will be happy
                        to hear from you.
                    </p>

                    <div class="space-y-5">

                        <p>📍 Location: Kenya</p>

                        <p>
                            📧 Email:
                            <a href="mailto:info@example.com"
                               class="text-green-700">
                                info@example.com
                            </a>
                        </p>

                        <p>📞 Phone: Add your phone number</p>

                    </div>

                </div>

                <!-- Contact Form -->
                <div class="bg-white p-8 rounded-xl shadow-md">

                    <h2 class="text-2xl font-bold text-green-800 mb-6">
                        Send Us a Message
                    </h2>

                    <!-- PHP Feedback -->
                    <?php if ($status): ?>

                        <div class="mb-5 p-4 rounded-lg
                            <?php echo $status['success']
                                ? 'bg-green-100 text-green-800'
                                : 'bg-red-100 text-red-700'; ?>">

                            <?php echo htmlspecialchars(
                                $status['message'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </div>

                    <?php endif; ?>

                    <form
                        action="handlers/contact_handler.php"
                        method="POST"
                        class="space-y-5"
                    >

                        <!-- Full Name -->
                        <div>

                            <label for="full_name" class="block mb-2 font-medium">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="full_name"
                                id="full_name"
                                maxlength="100"
                                required
                                placeholder="Enter your full name"
                                class="w-full border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                            >

                        </div>

                        <!-- Email -->
                        <div>

                            <label for="email" class="block mb-2 font-medium">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                maxlength="150"
                                required
                                placeholder="Enter your email address"
                                class="w-full border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                            >

                        </div>

                        <!-- Message -->
                        <div>

                            <label for="message" class="block mb-2 font-medium">
                                Your Message
                            </label>

                            <textarea
                                name="message"
                                id="message"
                                rows="5"
                                maxlength="5000"
                                required
                                placeholder="Write your message here..."
                                class="w-full border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                            ></textarea>

                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-green-700 text-white py-3 rounded-lg font-bold hover:bg-green-800 transition"
                        >
                            Send Message
                        </button>

                    </form>

                </div>

            </div>

        </section>

    </main>

    <!-- Reusable Footer -->
    <div id="footer"></div>

    <script src="js/script.js"></script>

</body>
</html>
