<?php
session_start();
ob_start();
$pasta = isset($_GET['A']) && $_GET['A'] ? $_GET['A'] : 'pcnt';

// Verifica se o diretório existe
if (!is_dir($pasta)) {
    header("Location: pcnt/");
    exit;
}

header("Location: $pasta/");
exit;
