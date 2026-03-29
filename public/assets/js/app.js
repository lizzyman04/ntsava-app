/**
 * Ntsava App - Main JavaScript (jQuery)
 * Handles mobile menu, dropdowns, and common utilities
 */

$(document).ready(function () {
    // ============================================
    // Mobile Menu
    // ============================================
    function initMobileMenu() {
        var $mobileMenuBtn = $('#mobile-menu-btn');
        var $sidebar = $('.sidebar');
        var $overlay = $('.sidebar-overlay');
        var $closeSidebar = $('#close-sidebar');

        if ($mobileMenuBtn.length === 0) return;

        function openMenu() {
            $sidebar.addClass('open');
            if ($overlay.length) {
                $overlay.addClass('show');
            }
            $mobileMenuBtn.find('i').removeClass('fa-bars').addClass('fa-times');
            $('body').css('overflow', 'hidden');
        }

        function closeMenu() {
            $sidebar.removeClass('open');
            if ($overlay.length) {
                $overlay.removeClass('show');
            }
            $mobileMenuBtn.find('i').removeClass('fa-times').addClass('fa-bars');
            $('body').css('overflow', '');
        }

        $mobileMenuBtn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if ($sidebar.hasClass('open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        if ($closeSidebar.length) {
            $closeSidebar.on('click', closeMenu);
        }

        if ($overlay.length) {
            $overlay.on('click', closeMenu);
        }

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $sidebar.hasClass('open')) {
                closeMenu();
            }
        });
    }

    // ============================================
    // User Dropdown
    // ============================================
    function initUserDropdown() {
        var $userMenuBtn = $('#user-menu-btn');
        var $userDropdown = $('#user-dropdown');

        if ($userMenuBtn.length === 0) return;

        var $chevron = $userMenuBtn.find('.fa-chevron-down');

        function openDropdown() {
            $userDropdown.removeClass('invisible opacity-0');
            $userDropdown.addClass('visible opacity-100');
            $userMenuBtn.attr('aria-expanded', 'true');
            if ($chevron.length) {
                $chevron.css('transform', 'rotate(180deg)');
            }
        }

        function closeDropdown() {
            $userDropdown.removeClass('visible opacity-100');
            $userDropdown.addClass('invisible opacity-0');
            $userMenuBtn.attr('aria-expanded', 'false');
            if ($chevron.length) {
                $chevron.css('transform', 'rotate(0deg)');
            }
        }

        function toggleDropdown() {
            if ($userDropdown.hasClass('opacity-100')) {
                closeDropdown();
            } else {
                openDropdown();
            }
        }

        $userMenuBtn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleDropdown();
        });

        $(document).on('click', function (e) {
            if (!$userMenuBtn.is(e.target) &&
                $userMenuBtn.has(e.target).length === 0 &&
                !$userDropdown.is(e.target) &&
                $userDropdown.has(e.target).length === 0) {
                closeDropdown();
            }
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $userDropdown.hasClass('opacity-100')) {
                closeDropdown();
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

            if ($toast.length === 0) {
                $toast = $('<div>')
                    .attr('id', 'toast')
                    .addClass('toast')
                    .appendTo('body');
            }

            var icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            $toast.removeClass('success error warning info')
                .addClass(type)
                .html('<i class="fas ' + (icons[type] || icons.info) + ' mr-2"></i>' + message)
                .fadeIn(300);

            setTimeout(function () {
                $toast.fadeOut(300);
            }, 3000);
        }
    };

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

    initMobileMenu();
    initUserDropdown();
});