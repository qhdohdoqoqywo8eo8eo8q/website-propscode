<?php
function laporan_table_actions($baseUrl, $params = []) {
    $query = http_build_query($params);
    $printUrl = $baseUrl;
    $excelUrl = $baseUrl . '?' . $query;
    return '<div class="d-flex justify-content-end gap-2 mb-3">'
        . '<button type="button" onclick="window.print()" class="btn btn-outline-secondary">Cetak</button>'
        . '<a href="' . htmlspecialchars($excelUrl) . '" class="btn btn-success">Ekspor Excel</a>'
        . '</div>';
}
