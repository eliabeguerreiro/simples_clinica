<?php
// Teste rápido para validar cálculo do campo de controle usado em gerar_bpai.php
// Reproduz a lógica: somar (codigo numericamente) + quantidade para cada atendimento,
// obter resto %1111 e somar 1111.

function calcula_campo_controle($atendimentos) {
    $soma = 0;
    foreach ($atendimentos as $a) {
        $codigo = $a['codigo'] ?? '0';
        $quantidade = $a['quantidade'] ?? 0;
        $codigoDigits = preg_replace('/[^0-9]/', '', (string)$codigo);
        $codigoInt = $codigoDigits === '' ? 0 : (int)$codigoDigits;
        $quantidadeInt = is_numeric($quantidade) ? (int)$quantidade : 0;
        $soma += $codigoInt + $quantidadeInt;
    }
    $resto = $soma % 1111;
    $campo = 1111 + $resto;
    return ['soma' => $soma, 'resto' => $resto, 'campo' => $campo];
}

$tests = [
    [
        'name' => 'Teste simples com códigos numéricos',
        'dados' => [
            ['codigo' => '100', 'quantidade' => 1],
            ['codigo' => '200', 'quantidade' => 2],
            ['codigo' => '300', 'quantidade' => 3],
        ]
    ],
    [
        'name' => 'Códigos com letras',
        'dados' => [
            ['codigo' => 'A123', 'quantidade' => 1],
            ['codigo' => 'B045', 'quantidade' => 2],
        ]
    ],
    [
        'name' => 'Códigos com zeros e nulos',
        'dados' => [
            ['codigo' => '0001', 'quantidade' => 1],
            ['codigo' => '', 'quantidade' => 5],
            ['codigo' => null, 'quantidade' => '3'],
        ]
    ],
];

foreach ($tests as $t) {
    $res = calcula_campo_controle($t['dados']);
    echo "--- {$t['name']} ---\n";
    echo "Soma: {$res['soma']}\n";
    echo "Resto (%1111): {$res['resto']}\n";
    echo "Campo controle (1111 + resto): {$res['campo']}\n\n";
}

?>