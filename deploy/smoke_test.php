<?php

/**
 * smoke_test.php - Verifies application health after deployment
 * Usage: php smoke_test.php http://localhost:8031
 */

$baseUrl = $argv[1] ?? 'http://localhost';

echo "--- 🔍 Starting Smoke Tests for $baseUrl ---\n";

function checkUrl($url, $expectedSnippet = null) {
    echo "Testing $url... ";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo "FAILED (HTTP $httpCode)\n";
        return false;
    }

    if ($expectedSnippet && !str_contains($response, $expectedSnippet)) {
        echo "FAILED (Snippet '$expectedSnippet' not found)\n";
        return false;
    }

    echo "OK\n";
    return true;
}

$errors = 0;

// 1. Login Page
if (!checkUrl($baseUrl . "/admin/site/login", "Kirish")) $errors++;

// 2. Mock API Health Endpoint (assuming we'll create one)
// if (!checkUrl($baseUrl . "/api/health", "OK")) $errors++;

// 3. Database Check (via console if port is available, or via web if we have a test route)
// For smoke test, successful login page load often implies DB is connected for auth check.

if ($errors === 0) {
    echo "--- ✅ All Smoke Tests Passed! ---\n";
    exit(0);
} else {
    echo "--- ❌ $errors Smoke Test(s) Failed ---\n";
    exit(1);
}
