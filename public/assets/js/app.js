/**
 * CDN App - Main JavaScript (jQuery)
 * Handles theme toggle, mobile menu, dropdowns, and common utilities
 */

$(document).ready(function () {
    // ============================================
    // Theme Toggle
    // ============================================
    function initTheme() {
        var savedTheme = localStorage.getItem('theme');

        // Apply theme based on saved preference or system preference
        if (savedTheme === 'dark') {
            $('html').addClass('dark');
        } else if (savedTheme === 'light') {
            $('html').removeClass('dark');
        } else {
            // Check system preference
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                $('html').addClass('dark');
            } else {
                $('html').removeClass('dark');
            }
        }

        // Update button icons based on current theme
        updateThemeIcons();
    }

    function updateThemeIcons() {
        var isDark = $('html').hasClass('dark');
        var $moonIcon = $('#theme-toggle .fa-moon');
        var $sunIcon = $('#theme-toggle .fa-sun');

        if (isDark) {
            $moonIcon.addClass('hidden');
            $sunIcon.removeClass('hidden');
        } else {
            $moonIcon.removeClass('hidden');
            $sunIcon.addClass('hidden');
        }
    }

    $('#theme-toggle').on('click', function () {
        var isDark = $('html').hasClass('dark');

        if (isDark) {
            $('html').removeClass('dark');
            localStorage.setItem('theme', 'light');
        } else {
            $('html').addClass('dark');
            localStorage.setItem('theme', 'dark');
        }

        updateThemeIcons();

        // Dispatch event for other components
        $(window).trigger('themeChanged', { dark: !isDark });
    });

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                $('html').addClass('dark');
            } else {
                $('html').removeClass('dark');
            }
            updateThemeIcons();
        }
    });

    // ============================================
    // Mobile Menu
    // ============================================
    function initMobileMenu() {
        var $mobileMenuBtn = $('#mobile-menu-btn');
        var $sidebar = $('.sidebar');

        $mobileMenuBtn.on('click', function (e) {
            e.preventDefault();
            $sidebar.toggleClass('open');
            $(this).find('i').toggleClass('fa-bars fa-times');
        });

        // Close when clicking outside
        $(document).on('click', function (e) {
            if ($sidebar.hasClass('open') &&
                !$sidebar.is(e.target) &&
                $sidebar.has(e.target).length === 0 &&
                !$mobileMenuBtn.is(e.target) &&
                $mobileMenuBtn.has(e.target).length === 0) {
                $sidebar.removeClass('open');
                $mobileMenuBtn.find('i').removeClass('fa-times').addClass('fa-bars');
            }
        });

        // Close on escape key
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $sidebar.hasClass('open')) {
                $sidebar.removeClass('open');
                $mobileMenuBtn.find('i').removeClass('fa-times').addClass('fa-bars');
            }
        });
    }

    // ============================================
    // User Dropdown (fallback in case header.js doesn't load)
    // ============================================
    function initUserDropdown() {
        var $userMenuBtn = $('#user-menu-btn');
        var $userDropdown = $('#user-dropdown');

        if ($userMenuBtn.length === 0) return;

        var $chevron = $userMenuBtn.find('.fa-chevron-down');

        function toggleDropdown(show) {
            if (show) {
                $userDropdown.addClass('show');
                $userMenuBtn.attr('aria-expanded', 'true');
                $chevron.css('transform', 'rotate(180deg)');
            } else {
                $userDropdown.removeClass('show');
                $userMenuBtn.attr('aria-expanded', 'false');
                $chevron.css('transform', 'rotate(0deg)');
            }
        }

        $userMenuBtn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var isOpen = $userDropdown.hasClass('show');
            toggleDropdown(!isOpen);
        });

        // Close dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$userMenuBtn.is(e.target) &&
                $userMenuBtn.has(e.target).length === 0 &&
                !$userDropdown.is(e.target) &&
                $userDropdown.has(e.target).length === 0) {
                toggleDropdown(false);
            }
        });

        // Close on escape key
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $userDropdown.hasClass('show')) {
                toggleDropdown(false);
            }
        });
    }

    // ============================================
    // Toast Notifications
    // ============================================
    window.Toast = {
        show: function (message, type) {
            type = type || 'success';
            var $toast = $('#toast');
            var colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };

            $toast.removeClass('bg-green-500 bg-red-500 bg-yellow-500 bg-blue-500')
                .addClass(colors[type] || colors.success)
                .html('<i class="fas ' + getIconForType(type) + ' mr-2"></i>' + message)
                .fadeIn(300);

            setTimeout(function () {
                $toast.fadeOut(300);
            }, 3000);
        }
    };

    function getIconForType(type) {
        switch (type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            case 'info': return 'fa-info-circle';
            default: return 'fa-info-circle';
        }
    }

    // ============================================
    // AJAX Helper
    // ============================================
    window.API = {
        request: function (url, options) {
            options = options || {};
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            var defaults = {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            };

            var settings = $.extend(true, {}, defaults, options);

            return $.ajax(url, settings).fail(function (xhr) {
                var message = xhr.responseJSON?.message || 'Request failed';
                window.Toast.show(message, 'error');
            });
        },

        get: function (url) {
            return this.request(url, { method: 'GET' });
        },

        post: function (url, data) {
            return this.request(url, {
                method: 'POST',
                data: JSON.stringify(data)
            });
        },

        put: function (url, data) {
            return this.request(url, {
                method: 'PUT',
                data: JSON.stringify(data)
            });
        },

        delete: function (url) {
            return this.request(url, { method: 'DELETE' });
        }
    };

    // ============================================
    // File Upload Helper
    // ============================================
    window.FileUploader = {
        upload: function (file, path, onProgress) {
            var formData = new FormData();
            formData.append('file', file);
            formData.append('path', path || '');

            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var deferred = $.Deferred();
            var xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function (e) {
                if (onProgress && e.lengthComputable) {
                    onProgress(e.loaded / e.total);
                }
            });

            xhr.addEventListener('load', function () {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        deferred.resolve(response);
                    } else {
                        deferred.reject(new Error(response.message || 'Upload failed'));
                    }
                } catch (e) {
                    deferred.reject(new Error('Invalid response'));
                }
            });

            xhr.addEventListener('error', function () {
                deferred.reject(new Error('Network error'));
            });

            xhr.open('POST', '/dashboard/upload');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.send(formData);

            return deferred.promise();
        }
    };

    // Initialize all components
    initTheme();
    initMobileMenu();
    initUserDropdown();
});