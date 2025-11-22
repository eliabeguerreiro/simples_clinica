<?php
session_start();

//var_dump($_SESSION);


include_once "classes/painel.class.php";
include_once "classes/db.class.php";

$clinica = Painel::GetClinica();
$profissionais = Painel::GetProfissionais();
$pacientes = Painel::GetPacientes();
$procedimentos = Painel::GetProcedimentos();

$filtros = [
    'competencia' => $_GET['competencia'] ?? '',
    'data_inicio' => $_GET['data_inicio'] ?? '',
    'data_fim' => $_GET['data_fim'] ?? '',
    'profissional_id' => $_GET['profissional_id'] ?? '',
    'procedimento_id' => $_GET['procedimento_id'] ?? '',
    'paciente_nome' => $_GET['paciente_nome'] ?? '',
    'paciente_id' => $_GET['paciente_id'] ?? ''
];

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 25; // Quantidade de registros por página


// Busca atendimentos filtrados
$atendimentos = Painel::getAtendimentos($filtros, $pagina, $por_pagina);


//var_dump($atendimentos);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vivenciar - Espaço Terapêutico</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> 
</head>
<body>
    
<?php if (!empty($_SESSION['mensagem']) && is_array($_SESSION['mensagem'])): ?>
    <?php 
        $tipo = htmlspecialchars($_SESSION['mensagem']['tipo'] ?? 'erro');
        $texto = htmlspecialchars($_SESSION['mensagem']['texto'] ?? 'Mensagem desconhecida.');
    ?>
    <div class="alert-container">
        <div class="alert alert-<?php echo $tipo === 'sucesso' ? 'success' : 'error'; ?>">
            <?= $texto ?>
        </div>
    </div>
    <?php unset($_SESSION['mensagem']); ?>
<?php endif; ?>

    <!-- Cabeçalho -->
    <header>
        <div class="logo">
            <img src="vivenciar_logov2.png" alt="Logo Vivenciar">
        </div>
        <nav>
            <ul>
                <li><a href="../">Inicio</a></li>
                <li><a href="./">Novos atendimentos</a></li>
                <li><a href="atendimentos.php">Atendimentos</a></li>
                <li><a href="pacientes.php">Pacientes</a></li>
                <li><a href="profissionais.php">Profissionais</a></li>
                <li><a href="procedimentos.php">Procedimentos</a></li>
            </ul>
        </nav>
    </header>


   <!-- Seção de Atendimentos Registrados -->
<section class="appointments">
    <h2 id="titulo-atendimentos">Atendimentos Registrados</h2>
    <div id="filtros-aplicados" class="filtros-aplicados"></div>
    
<div class="filtros">
    <form method="GET" action="">
        <div class="form-row">
            <div class="form-group">
                <label for="filtro_competencia">Competência:</label>
                <input type="text" id="filtro_competencia" name="competencia"
                       placeholder="AAAAMM" pattern="\d{6}" value="<?= htmlspecialchars($filtros['competencia']) ?>">
            </div>
            <div class="form-group">
                <label for="filtro_profissional">Profissional:</label>
                <select id="filtro_profissional" name="profissional_id">
                    <option value="">Todos</option>
                    <?php foreach ($profissionais['dados'] as $prof): ?>
                        <option value="<?= $prof['id'] ?>" <?= ($filtros['profissional_id'] ?? '') == $prof['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prof['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="filtro_procedimento">Procedimento:</label>
                <select id="filtro_procedimento" name="procedimento_id">
                    <option value="">Todos</option>
                    <?php foreach ($procedimentos['dados'] as $proc): ?>
                        <option value="<?= $proc['id'] ?>" <?= ($filtros['procedimento_id'] ?? '') == $proc['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($proc['codigo']) ?> - <?= htmlspecialchars($proc['descricao']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nome_paciente">Nome do Paciente:</label>
                <input type="text" id="nome_paciente" name="paciente_nome"
                    placeholder="Buscar paciente..." value="<?= htmlspecialchars($filtros['paciente_nome'] ?? '') ?>" autocomplete="off">
                <input type="hidden" id="paciente_id" name="paciente_id" value="<?= htmlspecialchars($filtros['paciente_id'] ?? '') ?>">
                <ul id="lista_resultados_pacientes" class="autocomplete-results"></ul>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <button type="submit" class="btn-filter">Filtrar</button>
                <button type="button" class="btn-filter" onclick="limparFiltros()">Remover Filtros</button>
            </div>
        </div>
    </form>
</div>

    <?php if ($atendimentos['total'] > 0): ?>
    <p class="total-atendimentos">Total de atendimentos encontrados: <?= $atendimentos['total'] ?></p>
    <?php else: ?>
        <p class="total-atendimentos">Nenhum atendimento encontrado.</p>
    <?php endif; ?>
    
<!-- Tabela de Atendimentos -->
<table class="patients-list">
    <thead>
        <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th>Data</th>
            <th>Profissional</th>
            <th>Paciente</th>   
            <th>Procedimento</th>
            <th>Competência</th>

        </tr>
    </thead>
    <tbody>
        <?php if (count($atendimentos['dados']) > 0): ?>
            <?php foreach ($atendimentos['dados'] as $atendimento): ?>
                <tr>
                    <td><input type="checkbox" class="checkbox-atendimento" name="ids[]" value="<?= $atendimento['id'] ?>"></td>
                    <td><?= date('d/m/Y', strtotime($atendimento['data_atendimento'])) ?></td>
                    <td><?= htmlspecialchars($atendimento['profissional_nome']) ?></td>
                    <td><?= htmlspecialchars($atendimento['paciente_nome']) ?></td>
                    <td><?= htmlspecialchars($atendimento['procedimento_codigo']) ?></td>
                    <td><?= substr($atendimento['competencia'], 4, 2) ?>/<?= substr($atendimento['competencia'], 0, 4) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">Nenhum atendimento encontrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


<!-- Navegação de Páginas -->
<div class="pagination">
    <?php
    $total_paginas = ceil($atendimentos['total'] / $por_pagina);
    $url_base = "?" . http_build_query(array_merge($_GET, ['pagina' => '__PAGINA__']));

    // Botão "Primeira Página"
    if ($pagina > 1) {
        echo '<a href="' . str_replace('__PAGINA__', 1, $url_base) . '" class="btn-page">&laquo;&laquo;</a>';
    }

    // Botão "Página Anterior"
    if ($pagina > 1) {
        echo '<a href="' . str_replace('__PAGINA__', $pagina - 1, $url_base) . '" class="btn-page">&laquo;</a>';
    }

    // Mostrar páginas próximas à página atual
    $start = max(1, $pagina - 2);
    $end = min($total_paginas, $pagina + 2);

    for ($i = $start; $i <= $end; $i++) {
        echo '<a href="' . str_replace('__PAGINA__', $i, $url_base) . '" class="btn-page ' . ($i == $pagina ? 'active' : '') . '">' . $i . '</a>';
    }

    // Botão "Próxima Página"
    if ($pagina < $total_paginas) {
        echo '<a href="' . str_replace('__PAGINA__', $pagina + 1, $url_base) . '" class="btn-page">&raquo;</a>';
    }

    // Botão "Última Página"
    if ($pagina < $total_paginas) {
        echo '<a href="' . str_replace('__PAGINA__', $total_paginas, $url_base) . '" class="btn-page">&raquo;&raquo;</a>';
    }
    ?>
</div>
    
    <!-- Ações em Lote -->
    <div class="actions-bar">
        <?php if ($atendimentos['total'] > 0): ?>
        <button type="button" id="excluir-selecionados" class="btn-delete">Excluir Selecionados</button>
    <?php endif; ?>
   <form action="gerar_bpai.php" method="POST" style="display:inline;">
        <input type="hidden" name="competencia" value="<?= htmlspecialchars($filtros['competencia']) ?>">
        <input type="hidden" name="data_inicio" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
        <input type="hidden" name="data_fim" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
        <input type="hidden" name="profissional_id" value="<?= htmlspecialchars($filtros['profissional_id']) ?>">
        <input type="hidden" name="procedimento_id" value="<?= htmlspecialchars($filtros['procedimento_id']) ?>">
        <input type="hidden" name="paciente_nome" value="<?= htmlspecialchars($filtros['paciente_nome']) ?>">
        <input type="hidden" name="paciente_id" value="<?= htmlspecialchars($filtros['paciente_id']) ?>">
        <button type="submit" class="btn-action">Gerar Arquivo BPA-I</button>
    </form>
    <button id="btn-exportar-excel" class="btn-action" type="button">Exportar para Excel</button>
    
</div>
    
</section>

<script>
    const todosPacientes = [
        <?php foreach ($pacientes['dados'] as $p): ?>
            {
                id: <?= $p['id'] ?>,
                nome: "<?= addslashes($p['nome']) ?>",
                sexo: "<?= $p['sexo'] ?? '' ?>",
                cns: "<?= $p['cns'] ?? '' ?>",
                cpf: "<?= $p['cpf'] ?? '' ?>",
                nascimento: "<?= date('d/m/Y', strtotime($p['data_nascimento'])) ?>"
            },
        <?php endforeach; ?>
    ];

    document.addEventListener('DOMContentLoaded', function () {
        const buscaInput = document.getElementById('nome_paciente');
        const listaResultados = document.getElementById('lista_resultados_pacientes');

        function filtrarPacientes(termo) {
            return todosPacientes.filter(p =>
                p.nome.toLowerCase().includes(termo.toLowerCase())
            );
        }

        buscaInput.addEventListener('input', function () {
            const termo = this.value.trim();
            listaResultados.innerHTML = '';
            if (termo.length < 2) return;

            const resultados = filtrarPacientes(termo);

            if (resultados.length === 0) {
                const li = document.createElement('li');
                li.textContent = 'Nenhum paciente encontrado';
                li.classList.add('no-result');
                listaResultados.appendChild(li);
                return;
            }

            resultados.slice(0, 10).forEach(p => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <strong>${p.nome}</strong><br>
                    <small>Sexo: ${p.sexo || '-'} | CNS: ${p.cns || 'Não informado'}</small>
                `;
                li.dataset.id = p.id;
                li.dataset.nome = p.nome;
                li.dataset.cns = p.cns;
                li.dataset.cpf = p.cpf;
                li.dataset.sexo = p.sexo;
                li.dataset.nascimento = p.nascimento;

                li.addEventListener('click', () => {
                    buscaInput.value = p.nome;
                    document.getElementById('paciente_id').value = p.id;
                    // Se quiser preencher outros campos:
                    // document.getElementById('campo-sexo').value = p.sexo;
                    // document.getElementById('campo-cns').value = p.cns;
                    listaResultados.innerHTML = '';
                });

                listaResultados.appendChild(li);
            });
        });

        // Ocultar resultados ao clicar fora
        document.addEventListener('click', function (e) {
            if (!buscaInput.contains(e.target) && !listaResultados.contains(e.target)) {
                listaResultados.innerHTML = '';
            }
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const botoesExcluir = document.querySelectorAll('.btn-ajax-delete');
    const btnExcluirSelecionados = document.getElementById('excluir-selecionados');
    const checkboxes = document.querySelectorAll('.checkbox-atendimento');
    const selectAll = document.getElementById('select-all');

    // Ações individuais
    botoesExcluir.forEach(botao => {
        botao.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            if (!confirm('Tem certeza que deseja excluir este atendimento?')) return;

            fetch('excluir_atendimento.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(response => response.json())
            .then(data => {
                if (data.tipo === 'sucesso') {
                    
                    location.reload();
                } else {
                    alert('Erro: ' + data.texto);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                alert('Ocorreu um erro ao tentar excluir o atendimento.');
            });
        });
    });

    // Selecionar/deselecionar todos
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }

    // Exclusão múltipla
    if (btnExcluirSelecionados) {
        btnExcluirSelecionados.addEventListener('click', function () {
            const ids = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    ids.push(cb.value);
                }
            });

            if (ids.length === 0) {
                alert("Selecione pelo menos um atendimento.");
                return;
            }

            if (!confirm(`Tem certeza que deseja excluir ${ids.length} atendimento(s)?`)) {
                return;
            }

            fetch('excluir_atendimentos_multiplos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'ids=' + encodeURIComponent(ids.join(','))
            })
            .then(response => response.json())
            .then(data => {
                if (data.tipo === 'sucesso') {
                   
                    location.reload();
                } else {
                    alert('Erro: ' + data.texto);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                alert('Ocorreu um erro ao tentar excluir os atendimentos.');
            });
        });
    }
});

function limparFiltros() {
    // Limpa os campos do formulário
    document.querySelectorAll("input[type='text'], input[type='date'], select").forEach(input => {
        if (input.name !== '') { // preserva campos importantes com name vazio (se houver)
            input.value = '';
        }
    });

    // Se tiver autocomplete, limpe também
    const buscaInput = document.getElementById('nome_paciente');
    const pacienteIdInput = document.getElementById('paciente_id');
    if (buscaInput) buscaInput.value = '';
    if (pacienteIdInput) pacienteIdInput.value = '';

    // Redirecione para a mesma página, sem parâmetros GET
    window.location.href = window.location.pathname;
}


document.addEventListener('DOMContentLoaded', function () {
    const titulo = document.getElementById("titulo-atendimentos");
    const filtrosAplicados = document.getElementById("filtros-aplicados");

    // Função para montar o novo título com base nos filtros ativos
    function atualizarTitulo() {
        const competencia = document.querySelector("[name='competencia']").value.trim();
        const profissional = document.querySelector("[name='profissional_id']").selectedOptions[0]?.text || '';
        const procedimento = document.querySelector("[name='procedimento_id']").selectedOptions[0]?.text || '';
        const paciente = document.querySelector("[name='paciente_nome']").value.trim();

        let textoFiltro = '';

        if (competencia) {
            const ano = competencia.substring(0, 4);
            const mes = competencia.substring(4, 6);
            textoFiltro += `Competência: ${mes}/${ano}<br>`;
        }
        if (profissional && profissional !== 'Todos') {
            textoFiltro += `Profissional: ${profissional}<br>`;
        }
        if (procedimento && procedimento !== 'Todos') {
            textoFiltro += `Procedimento: ${procedimento.split(" - ")[1] || procedimento}<br>`;
        }
        if (paciente) {
            textoFiltro += `Paciente: ${paciente}`;
        }

        // Atualiza o título principal
        if (textoFiltro) {
            titulo.textContent = "Atendimentos Registrados";
            filtrosAplicados.innerHTML = textoFiltro;
        } else {
            titulo.textContent = "Atendimentos Registrados";
            filtrosAplicados.innerHTML = "";
        }
    }

    // Adiciona evento aos campos de filtro
    document.querySelectorAll("[name='competencia'], [name='profissional_id'], [name='procedimento_id'], [name='paciente_nome']")
        .forEach(input => {
            input.addEventListener('change', atualizarTitulo);
            input.addEventListener('input', atualizarTitulo); // para inputs text
        });

    // Atualiza título ao carregar a página
    atualizarTitulo();
});
</script>
<script>
    document.getElementById("btn-exportar-excel").addEventListener("click", function () {
    // Pega os filtros do formulário
    const params = new URLSearchParams();
    ["competencia", "data_inicio", "data_fim", "profissional_id", "procedimento_id", "paciente_nome", "paciente_id"].forEach(name => {
        const el = document.querySelector(`[name='${name}']`);
        if (el && el.value) params.append(name, el.value);
    });
    // Redireciona para o PHP de exportação
    window.location = "exportar_atendimentos_excel.php?" + params.toString();
});
</script>
</body>
</html>