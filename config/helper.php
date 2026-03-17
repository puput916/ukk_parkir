<?php
function hitungBiaya($waktu_masuk, $waktu_keluar, $tarif_per_jam) {
    $masuk = new DateTime($waktu_masuk);
    $keluar = new DateTime($waktu_keluar);
    $selisih = $masuk->diff($keluar);
    
    $jam = $selisih->h + ($selisih->days * 24);
    
    if ($selisih->i > 0 || $selisih->s > 0) {
        $jam++;
    }
    
    $total_jam = ($jam == 0) ? 1 : $jam;
    
    return [
        'durasi' => $total_jam,
        'total'  => $total_jam * $tarif_per_jam
    ];
}
?>