<?php
/**
 * Footer Component
 */
?>
<footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-auto">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; <?= date('Y') ?> CDN App. All rights reserved.
            </p>
            <div class="flex items-center gap-6">
                <a href="/about"
                    class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">About</a>
                <a href="/privacy"
                    class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">Privacy</a>
                <a href="/terms"
                    class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">Terms</a>
            </div>
        </div>
    </div>
</footer>