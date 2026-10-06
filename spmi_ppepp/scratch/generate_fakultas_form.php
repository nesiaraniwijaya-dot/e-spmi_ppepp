<?php
$source = file_get_contents(__DIR__ . '/../views/admin_prodi/document_form.php');

// Replace prodi references with fakultas
$replaceMap = [
    "Admin Prodi: Form Upload / Edit Dokumen Mutu PPEPP" => "Admin Fakultas: Form Upload / Edit Dokumen Mutu PPEPP",
    "Program Studi: <strong><?= htmlspecialchars(\$prodi['nama_prodi']) ?></strong> (<?= htmlspecialchars(\$prodi['jenjang']) ?>)" => "Fakultas: <strong><?= htmlspecialchars(\$fakultas['nama_fakultas']) ?></strong> (<?= htmlspecialchars(\$fakultas['kode_fakultas']) ?>)",
    "base_url('prodi/dokumen')" => "base_url('fakultas/dokumen')",
    "base_url('prodi/dokumen/save')" => "base_url('fakultas/dokumen/save')",
    "base_url('prodi/dashboard')" => "base_url('fakultas/dashboard')",
    "Admin Prodi" => "Admin Fakultas",
    "admin_prodi" => "admin_fakultas",
    "dokumen mutu prodi" => "dokumen mutu fakultas",
    "Dokumen Mutu Program Studi" => "Dokumen Mutu Fakultas"
];

$adapted = str_replace(array_keys($replaceMap), array_values($replaceMap), $source);

file_put_contents(__DIR__ . '/../views/admin_fakultas/document_form.php', $adapted);
echo "Successfully generated views/admin_fakultas/document_form.php (" . strlen($adapted) . " bytes)\n";
