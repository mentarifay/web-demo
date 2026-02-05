<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$csvFile = 'scrappingnetta.csv';

if (!file_exists($csvFile)) {
    die(" File CSV tidak ditemukan! Pastikan file ada di root project.\n");
}

echo " Memulai import data...\n\n";

$file = fopen($csvFile, 'r');

// Skip header row - PAKAI DELIMITER ';'
$header = fgetcsv($file, 0, ';');

$count = 0;
$errors = 0;

// Kosongkan tabel dulu
DB::table('gas_volume')->truncate();
echo " Tabel dikosongkan\n";

// PAKAI DELIMITER ';'
while (($row = fgetcsv($file, 0, ';')) !== false) {
    try {
        // Skip baris kosong
        if (empty($row[0])) {
            continue;
        }
        
        DB::table('gas_volume')->insert([
            'shipper' => trim($row[0]),
            'tahun' => (int)$row[1],
            'bulan' => (int)$row[2],
            'periode' => trim($row[3]),
            'daily_average_mmscfd' => (float)str_replace(',', '.', trim($row[4])),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $count++;
        
        if ($count % 100 == 0) {
            echo " $count data berhasil diimport...\n";
        }
        
    } catch (Exception $e) {
        $errors++;
        echo "  Error pada baris: " . implode('; ', $row) . "\n";
        echo "   Pesan error: " . $e->getMessage() . "\n";
    }
}

fclose($file);

echo "\n";
echo "====================================\n";
echo " IMPORT SELESAI!\n";
echo "====================================\n";
echo " Total data berhasil: $count\n";
echo " Total error: $errors\n";
echo "====================================\n";