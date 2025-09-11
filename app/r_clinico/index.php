<?php
session_start();
ob_start();
$pasta = isset($_GET['A']) && $_GET['A'] ? $_GET['A'] : 'pcnt';
header("Location: $pasta/");
exit;