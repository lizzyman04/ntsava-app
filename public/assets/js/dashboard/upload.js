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

    dropZone.on('click', function () {
        fileInput.click();
    });

    dropZone.on('dragover', function (e) {
        e.preventDefault();
        dropZone.addClass('drag-over');
    });

    dropZone.on('dragleave', function () {
        dropZone.removeClass('drag-over');
    });

    dropZone.on('drop', function (e) {
        e.preventDefault();
        dropZone.removeClass('drag-over');
        handleFiles(e.originalEvent.dataTransfer.files);
    });

    fileInput.on('change', function (e) {
        handleFiles(e.target.files);
    });

    function handleFiles(selectedFiles) {
        for (var i = 0; i < selectedFiles.length; i++) {
            files.push(selectedFiles[i]);
        }
        updateQueue();
    }

    function updateQueue() {
        if (files.length > 0) {
            uploadQueue.show();
            var html = '';
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                html += `
                    <div class="flex justify-between items-center border-b py-2" data-index="${i}">
                        <div>
                            <span class="font-medium">${escapeHtml(file.name)}</span>
                            <span class="text-sm text-gray-500 ml-2">(${(file.size / 1024).toFixed(2)} KB)</span>
                        </div>
                        <button onclick="window.removeFile(${i})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }
            queueList.html(html);

            if (queueList.find('.upload-btn').length === 0) {
                var uploadBtn = $('<button>')
                    .addClass('btn-primary mt-4 w-full upload-btn')
                    .html('<i class="fas fa-upload mr-2"></i> Upload ' + files.length + ' file(s)')
                    .on('click', uploadFiles);
                queueList.append(uploadBtn);
            }
        } else {
            uploadQueue.hide();
        }
    }

    window.removeFile = function (index) {
        files.splice(index, 1);
        updateQueue();
    };

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

        for (var i = 0; i < files.length; i++) {
            (function (file) {
                uploads.push(
                    window.FileUploader.upload(file, path)
                        .done(function (result) {
                            window.Toast.show('Uploaded: ' + file.name, 'success');
                        })
                        .fail(function (error) {
                            window.Toast.show('Failed: ' + file.name + ' - ' + error.message, 'error');
                        })
                );
            })(files[i]);
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