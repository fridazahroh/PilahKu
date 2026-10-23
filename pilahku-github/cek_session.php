<?php
session_start();

echo "<h2>Cek Session PilahKu+</h2>";

echo "<p><strong>Session ID:</strong> "
    . session_id() . "</p>";

echo "<p><strong>Username:</strong> "
    . ($_SESSION['username'] ?? 'Tidak ada') . "</p>";

echo "<p><strong>Role:</strong> "
    . ($_SESSION['role'] ?? 'Tidak ada') . "</p>";

echo "<p><strong>Total pengajuan:</strong> "
    . count($_SESSION['pengajuan'] ?? []) . "</p>";

echo "<pre>";
print_r($_SESSION['pengajuan'] ?? []);
echo "</pre>";
?>