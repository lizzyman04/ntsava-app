<?php

/**
 * Ntsava App - View Helpers
 * Collection of helper functions for views and components
 */

/**
 * Component Helper - Load a view component
 * 
 * @param string $name Component name (without .php extension)
 * @param array $data Data to pass to the component
 * @throws Exception If component file not found
 * @return void
 */
function component($name, $data = [])
{
    $componentPath = __DIR__ . '/' . $name . '.php';

    if (!file_exists($componentPath)) {
        throw new Exception("Component not found: {$name} at {$componentPath}");
    }

    // Extract data to variables
    extract($data);

    // Include the component
    include $componentPath;
}

/**
 * CSRF Field Helper - Generate hidden CSRF token input
 * 
 * @return string HTML input field
 */
function csrf_field()
{
    $token = \App\Core\Auth::csrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Format File Size - Convert bytes to human readable format
 * 
 * @param int $bytes File size in bytes
 * @param int $precision Decimal precision
 * @return string Formatted file size
 */
function format_file_size($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Truncate String - Cut text to a specific length
 * 
 * @param string $string Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to add if truncated
 * @return string Truncated text
 */
function truncate($string, $length = 50, $suffix = '...')
{
    if (strlen($string) <= $length) {
        return $string;
    }

    return substr($string, 0, $length) . $suffix;
}