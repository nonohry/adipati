<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'ADIPATI' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?= url('/') ?>" class="text-xl font-bold text-blue-900">ADIPATI</a>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="<?= url('/proceedings') ?>" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Proceedings</a>
                        <a href="<?= url('/search') ?>" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Search</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if(\Core\Auth::check()): ?>
                        <a href="<?= url('/dashboard') ?>" class="text-gray-600 hover:text-gray-900 text-sm">Dashboard</a>
                        <form action="<?= url('/logout') ?>" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="text-gray-600 hover:text-gray-900 text-sm">Login</a>
                        <a href="<?= url('/register') ?>" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if(session_flash('success')): ?>
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"><?= session_flash('success') ?></div>
        </div>
    <?php endif; ?>
    
    <?php if(session_flash('error')): ?>
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"><?= session_flash('error') ?></div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="py-8">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4">
            <p class="text-center text-gray-500 text-sm">&copy; <?= date('Y') ?> ADIPATI Conference System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
