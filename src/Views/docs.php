<?php
// Load helpers
require_once __DIR__ . '/components/helpers.php';

/**
 * @var string $title
 */
use Fluxor\View;
?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title ?? 'API Documentation') ?>
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .code-block {
        background: #1e1e1e;
        border-radius: 0.75rem;
        padding: 1rem;
        overflow-x: auto;
        margin: 1rem 0;
    }

    .code-block code {
        color: #d4d4d4;
        font-family: 'Fira Code', monospace;
        font-size: 0.875rem;
    }

    .endpoint {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid var(--primary-500);
    }

    .method {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.75rem;
    }

    .method-get {
        background: #10b981;
        color: white;
    }

    .method-post {
        background: #3b82f6;
        color: white;
    }

    .method-delete {
        background: #ef4444;
        color: white;
    }

    .method-put {
        background: #f59e0b;
        color: white;
    }

    .endpoint-url {
        font-family: monospace;
        font-size: 0.875rem;
        color: var(--primary-600);
    }

    .param-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
    }

    .param-table th,
    .param-table td {
        padding: 0.5rem;
        text-align: left;
        border-bottom: 1px solid var(--gray-200);
    }

    .param-table th {
        font-weight: 600;
        color: var(--gray-600);
    }

    .dark .param-table td {
        border-bottom-color: var(--gray-700);
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<!-- Hero -->
<section class="relative overflow-hidden py-16 bg-gradient-to-br from-primary-50 to-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">API Documentation</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Everything you need to integrate with Ntsava's powerful CDN API
        </p>
    </div>
</section>

<!-- Overview -->
<section class="py-12">
    <div class="container mx-auto px-4 max-w-5xl">
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Overview</h2>
            <p class="text-gray-600 mb-4">
                Ntsava API provides a simple, RESTful interface to manage your files, tokens, and account.
                All API requests require authentication via API tokens.
            </p>
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-globe mr-2 text-primary-500"></i>
                    <strong>Base URL:</strong> <code
                        class="bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">https://cdn.omeu.space/api/v1</code>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    <i class="fas fa-key mr-2 text-primary-500"></i>
                    <strong>Authentication:</strong> All endpoints require <code>X-User-UUID</code> and
                    <code>X-Token</code> headers
                </p>
            </div>
        </div>

        <!-- Authentication -->
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Authentication</h2>
            <p class="text-gray-600 mb-4">
                All API requests must include your user UUID and an API token in the headers.
            </p>
            <div class="code-block">
                <code>
                    X-User-UUID: 550e8400-e29b-41d4-a716-446655440000<br>
                    X-Token: your-api-token-here
                </code>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-4">
                <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                <span class="text-sm text-yellow-700">
                    <strong>Note:</strong> The <code class="bg-yellow-100 px-1 rounded">/resize</code> endpoint is
                    <strong>public</strong>
                    and does <strong>NOT</strong> require any authentication headers. You can use it with any publicly
                    accessible image.
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                You can generate API tokens from your dashboard. Tokens can have specific permissions (upload, delete,
                read).
            </p>
        </div>

        <!-- Upload File -->
        <div class="endpoint">
            <div class="flex items-center mb-3">
                <span class="method method-post">POST</span>
                <span class="endpoint-url">/upload</span>
            </div>
            <p class="text-gray-600 mb-3">Upload a file to your storage.</p>

            <h4 class="font-semibold mt-4 mb-2">Headers</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Header</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>X-User-UUID</td>
                        <td>Your user UUID</td>
                    </tr>
                    <tr>
                        <td>X-Token</td>
                        <td>API token with upload permission</td>
                    </tr>
                    <tr>
                        <td>Content-Type</td>
                        <td>multipart/form-data</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="font-semibold mt-4 mb-2">Parameters</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>file</td>
                        <td>file</td>
                        <td>The file to upload</td>
                    </tr>
                    <tr>
                        <td>path</td>
                        <td>string</td>
                        <td>Optional path (e.g., "photos/2026/")</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="font-semibold mt-4 mb-2">Example</h4>
            <div class="code-block">
                <code>
                    curl -X POST https://cdn.omeu.space/api/v1/upload \<br>
                    &nbsp;&nbsp;-H "X-User-UUID: 550e8400-e29b-41d4-a716-446655440000" \<br>
                    &nbsp;&nbsp;-H "X-Token: your-token" \<br>
                    &nbsp;&nbsp;-F "file=@/path/to/image.jpg" \<br>
                    &nbsp;&nbsp;-F "path=photos/2026/"
                </code>
            </div>

            <h4 class="font-semibold mt-4 mb-2">Response</h4>
            <div class="code-block">
                <code>
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"data": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"url": "https://cdn.ntsava.space/u/username/photos/2026/image.jpg",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"size": 245760,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"mime": "image/jpeg"<br>
                    &nbsp;&nbsp;}<br>
                    }
                </code>
            </div>
        </div>

        <!-- Delete File -->
        <div class="endpoint">
            <div class="flex items-center mb-3">
                <span class="method method-delete">DELETE</span>
                <span class="endpoint-url">/delete</span>
            </div>
            <p class="text-gray-600 mb-3">Delete a file by UUID or path.</p>

            <h4 class="font-semibold mt-4 mb-2">Headers</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Header</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>X-User-UUID</td>
                        <td>Your user UUID</td>
                    </tr>
                    <tr>
                        <td>X-Token</td>
                        <td>API token with delete permission</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="font-semibold mt-4 mb-2">Parameters</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>uuid</td>
                        <td>string</td>
                        <td>File UUID (optional if path provided)</td>
                    </tr>
                    <tr>
                        <td>path</td>
                        <td>string</td>
                        <td>File path (optional if uuid provided)</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="font-semibold mt-4 mb-2">Example</h4>
            <div class="code-block">
                <code>
                    curl -X DELETE https://cdn.omeu.space/api/v1/delete \<br>
                    &nbsp;&nbsp;-H "X-User-UUID: 550e8400-e29b-41d4-a716-446655440000" \<br>
                    &nbsp;&nbsp;-H "X-Token: your-token" \<br>
                    &nbsp;&nbsp;-d "uuid=a1b2c3d4-e5f6-7890-abcd-ef1234567890"
                </code>
            </div>
        </div>

        <!-- Get File Info -->
        <div class="endpoint">
            <div class="flex items-center mb-3">
                <span class="method method-get">GET</span>
                <span class="endpoint-url">/info</span>
            </div>
            <p class="text-gray-600 mb-3">Get file metadata by UUID or path.</p>

            <h4 class="font-semibold mt-4 mb-2">Headers</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Header</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>X-User-UUID</td>
                        <td>Your user UUID</td>
                    </tr>
                    <tr>
                        <td>X-Token</td>
                        <td>API token with read permission</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="font-semibold mt-4 mb-2">Parameters</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>uuid</td>
                        <td>string</td>
                        <td>File UUID (optional if path provided)</td>
                    </tr>
                    <tr>
                        <td>path</td>
                        <td>string</td>
                        <td>File path (optional if uuid provided)</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="font-semibold mt-4 mb-2">Example</h4>
            <div class="code-block">
                <code>
                    curl -X GET https://cdn.omeu.space/api/v1/info \<br>
                    &nbsp;&nbsp;-H "X-User-UUID: 550e8400-e29b-41d4-a716-446655440000" \<br>
                    &nbsp;&nbsp;-H "X-Token: your-token" \<br>
                    &nbsp;&nbsp;-d "uuid=a1b2c3d4-e5f6-7890-abcd-ef1234567890"
                </code>
            </div>
        </div>
        <!-- Resize Image -->
        <div class="endpoint">
            <div class="flex items-center mb-3">
                <span class="method method-get">GET</span>
                <span class="endpoint-url">/resize</span>
            </div>
            <p class="text-gray-600 mb-3">
                Resize and transform images on the fly.
                <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full ml-2">Public
                    Endpoint</span>
                <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full ml-2">No
                    Authentication Required</span>
            </p>

            <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                <i class="fas fa-globe text-green-600 mr-2"></i>
                <span class="text-sm text-green-700">This endpoint is publicly accessible. No API tokens needed.</span>
            </div>

            <h4 class="font-semibold mt-4 mb-2">Parameters</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">photo</code></td>
                        <td>string</td>
                        <td>
                            Full path to the image: <code>u/username/path/to/image.jpg</code><br>
                            <span class="text-xs text-gray-500">Example: <code>u/johndoe/photos/summer.jpg</code></span>
                        </td>
                    </tr>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">w</code></td>
                        <td>int</td>
                        <td>Target width in pixels (optional)</td>
                    </tr>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">h</code></td>
                        <td>int</td>
                        <td>Target height in pixels (optional)</td>
                    </tr>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">format</code></td>
                        <td>string</td>
                        <td>Output format: <code>jpg</code>, <code>png</code>, <code>webp</code>, <code>gif</code>
                            (optional)</td>
                    </tr>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">filter</code></td>
                        <td>string</td>
                        <td>Image filter: <code>grayscale</code>, <code>blur</code>, <code>brightness</code>,
                            <code>contrast</code> (optional)
                        </td>
                    </tr>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">blur</code></td>
                        <td>int</td>
                        <td>Blur intensity (1-10) (optional)</td>
                    </tr>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">brightness</code></td>
                        <td>int</td>
                        <td>Brightness adjustment (-255 to 255) (optional)</td>
                    </tr>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">contrast</code></td>
                        <td>int</td>
                        <td>Contrast adjustment (-100 to 100) (optional)</td>
                    </tr>
                    <tr>
                        <td><code class="bg-gray-100 px-1 rounded">sharpen</code></td>
                        <td>int</td>
                        <td>Sharpen intensity (1-10) (optional)</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="font-semibold mt-4 mb-2">Examples</h4>

            <p class="text-sm text-gray-600 mb-2">Resize to 800px width (maintains aspect ratio):</p>
            <div class="code-block mb-3">
                <code>
                    https://cdn.omeu.space/api/v1/resize?photo=u/johndoe/photos/summer.jpg&w=800
                </code>
            </div>

            <p class="text-sm text-gray-600 mb-2">Resize to 800x600 and convert to WebP:</p>
            <div class="code-block mb-3">
                <code>
                    https://cdn.omeu.space/api/v1/resize?photo=u/johndoe/photos/summer.jpg&w=800&h=600&format=webp
                </code>
            </div>

            <p class="text-sm text-gray-600 mb-2">Apply grayscale filter and resize:</p>
            <div class="code-block mb-3">
                <code>
                    https://cdn.omeu.space/api/v1/resize?photo=u/johndoe/photos/summer.jpg&w=800&filter=grayscale
                </code>
            </div>

            <p class="text-sm text-gray-600 mb-2">Blur effect:</p>
            <div class="code-block">
                <code>
                    https://cdn.omeu.space/api/v1/resize?photo=u/johndoe/photos/summer.jpg&blur=5&w=800
                </code>
            </div>
        </div>

        <!-- Error Responses -->
        <div class="glass-card p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Error Responses</h2>
            <p class="text-gray-600 mb-4">All errors return a JSON response with the following structure:</p>
            <div class="code-block">
                <code>
                    {<br>
                    &nbsp;&nbsp;"success": false,<br>
                    &nbsp;&nbsp;"message": "Error description",<br>
                    &nbsp;&nbsp;"details": {} // Optional additional details<br>
                    }
                </code>
            </div>

            <h4 class="font-semibold mt-6 mb-3">Common HTTP Status Codes</h4>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>200</td>
                        <td>Success</td>
                    </tr>
                    <tr>
                        <td>400</td>
                        <td>Bad Request - Missing parameters</td>
                    </tr>
                    <tr>
                        <td>401</td>
                        <td>Unauthorized - Invalid or missing token</td>
                    </tr>
                    <tr>
                        <td>403</td>
                        <td>Forbidden - Insufficient permissions or suspended account</td>
                    </tr>
                    <tr>
                        <td>404</td>
                        <td>Not Found - File or resource not found</td>
                    </tr>
                    <tr>
                        <td>413</td>
                        <td>Payload Too Large - File exceeds size limit</td>
                    </tr>
                    <tr>
                        <td>500</td>
                        <td>Internal Server Error</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Rate Limits -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 max-w-5xl">
        <div class="glass-card p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Rate Limits</h2>
            <p class="text-gray-600 mb-4">
                API requests are limited based on your plan:
            </p>
            <div class="grid md:grid-cols-3 gap-4 text-center">
                <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                    <p class="font-bold text-primary-600">Free</p>
                    <p class="text-sm text-gray-600">100 requests/hour</p>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                    <p class="font-bold text-primary-600">Plus</p>
                    <p class="text-sm text-gray-600">1,000 requests/hour</p>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                    <p class="font-bold text-primary-600">Pro</p>
                    <p class="text-sm text-gray-600">10,000 requests/hour</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Syntax highlighting for code blocks (optional)
    document.querySelectorAll('.code-block code').forEach(block => {
        // Simple highlighting
        let html = block.innerHTML;
        html = html.replace(/(".*?")/g, '<span style="color: #ce9178;">$1</span>');
        html = html.replace(/(\b\w+:\b)/g, '<span style="color: #9cdcfe;">$1</span>');
        html = html.replace(/(\{|\}|\[|\]|\(|\))/g, '<span style="color: #ffd700;">$1</span>');
        block.innerHTML = html;
    });
</script>
<?php View::endSection(); ?>