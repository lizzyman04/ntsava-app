/**
 * Upload Page Script (jQuery)
 */

$(document).ready(function () {
    var dropZone = $('#dropZone');
    var fileInput = $('#fileInput');
    var pathInput = $('#path');
    var uploadQueue = $('#uploadQueue');
    var queueList = $('#queueList');
    var files = [];

    var maxSize = parseInt(dropZone.data('max-size'), 10) || 0;
    var allowedTypes = (dropZone.data('allowed-types') || '').split(',').map(function (t) { return t.trim().toLowerCase(); }).filter(Boolean);

    function formatSize(bytes) {
        if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(1) + ' GB';
        if (bytes >= 1048576) return Math.round(bytes / 1048576) + ' MB';
        return Math.round(bytes / 1024) + ' KB';
    }

    dropZone.on('click', function (e) {
        e.stopPropagation();
        fileInput[0].click();
    });

    dropZone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.addClass('drag-over');
    });

    dropZone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.removeClass('drag-over');
    });

    dropZone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.removeClass('drag-over');
        
        var dt = e.originalEvent.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            handleFiles(dt.files);
        }
    });

    fileInput.on('change', function (e) {
        e.stopPropagation();
        if (e.target.files && e.target.files.length) {
            handleFiles(e.target.files);
            fileInput.val('');
        }
    });

    function handleFiles(selectedFiles) {
        for (var i = 0; i < selectedFiles.length; i++) {
            var file = selectedFiles[i];

            if (maxSize > 0 && file.size > maxSize) {
                window.Toast.show(escapeHtml(file.name) + ': exceeds the ' + formatSize(maxSize) + ' limit for your plan', 'error');
                continue;
            }

            var ext = file.name.split('.').pop().toLowerCase();
            if (allowedTypes.length > 0 && allowedTypes.indexOf(ext) === -1) {
                window.Toast.show(escapeHtml(file.name) + ': .' + ext + ' is not allowed on your plan', 'error');
                continue;
            }

            files.push(file);
        }
        updateQueue();
    }

    function updateQueue() {
        if (files.length > 0) {
            uploadQueue.show();
            var html = '';
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var fileSize = (file.size / 1024).toFixed(2);
                var sizeUnit = 'KB';
                if (file.size > 1048576) {
                    fileSize = (file.size / 1048576).toFixed(2);
                    sizeUnit = 'MB';
                }
                html += '<div class="flex justify-between items-center border-b border-gray-200 py-3 file-item" data-index="' + i + '">' +
                    '<div>' +
                        '<span class="font-medium text-gray-700">' + escapeHtml(file.name) + '</span>' +
                        '<span class="text-sm text-gray-500 ml-2">(' + fileSize + ' ' + sizeUnit + ')</span>' +
                    '</div>' +
                    '<button type="button" class="remove-file text-red-500 hover:text-red-700 transition-colors" data-index="' + i + '">' +
                        '<i class="fas fa-times"></i>' +
                    '</button>' +
                '</div>';
            }
            queueList.html(html);

            queueList.off('click', '.remove-file').on('click', '.remove-file', function () {
                var index = $(this).data('index');
                removeFile(index);
            });

            if (queueList.find('.upload-btn').length === 0) {
                var uploadBtn = $('<button>')
                    .addClass('btn-primary mt-4 w-full upload-btn')
                    .html('<i class="fas fa-upload mr-2"></i> Upload ' + files.length + ' file(s)')
                    .on('click', uploadFiles);
                queueList.append(uploadBtn);
            }
        } else {
            uploadQueue.hide();
            queueList.empty();
        }
    }

    function removeFile(index) {
        files.splice(index, 1);
        updateQueue();
    }

    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function (m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function uploadFiles() {
        var path = pathInput.val();
        var uploads = [];
        var $uploadBtn = $('.upload-btn');
        var originalText = $uploadBtn.html();
        
        $uploadBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...');

        for (var i = 0; i < files.length; i++) {
            (function (file, index) {
                var promise = window.FileUploader.upload(file, path)
                    .done(function (result) {
                        window.Toast.show('Uploaded: ' + file.name, 'success');
                        var $item = queueList.find('[data-index="' + index + '"]');
                        $item.find('.remove-file').remove();
                        $item.addClass('opacity-50');
                        $item.append('<span class="text-green-500 ml-2"><i class="fas fa-check"></i></span>');
                    })
                    .fail(function (error) {
                        window.Toast.show('Failed: ' + file.name + ' - ' + error.message, 'error');
                        var $item = queueList.find('[data-index="' + index + '"]');
                        $item.addClass('bg-red-50');
                        $item.append('<span class="text-red-500 ml-2"><i class="fas fa-times"></i></span>');
                    });
                uploads.push(promise);
            })(files[i], i);
        }

        $.when.apply($, uploads).always(function () {
            files = [];
            updateQueue();
            setTimeout(function () {
                window.location.href = '/dashboard/files';
            }, 2000);
        });
    }
});