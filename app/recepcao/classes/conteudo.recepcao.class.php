<?php
class ContentRecepcao
{
    public function renderHeader()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Clinica - Recepção</title>
                <link rel="stylesheet" href="./src/style.css">
                
            </head>
        HTML;
        return $html;
    }

    public function renderBody()
    {

    $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);
    $html = <<<HTML

        <body>
            <header>
                <div class="logo">
                    <img src="" alt="LogoTipo">
                </div>
                <nav>
                    <ul>
                        <li><a href="../">Novos atendimentos</a></li>
                        <li><a href="../atendimentos.php">Atendimentos</a></li>
                        <li><a href="#">Pacientes</a></li>
                        <li><a href="../profissional">Profissionais</a></li>
                        <li><a href="../procedimento">Procedimentos</a></li>
                    </ul>
                </nav>
            </header>
            <div class="toggle-buttons">
                <button type="button" onclick="mostrarSeccao('cadastro')">Cadastro</button>
                <button type="button" onclick="mostrarSeccao('listagem')">Listagem</button>
            </div>
                <section id="secao-cadastro" class="conteudo-seccao">
                    <!-- Aqui vai todo o conteúdo do cadastro -->
                    <section class="form-section">
                    <h2>Cadastro de Paciente</h2>
                    <form action="" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome">Nome Completo*</label>
                                <input required type="text" id="nome" name="nome" required maxlength="100" placeholder="Digite o nome completo">
                            </div>
                            <div class="form-group">
                                <label for="cns">CNS*</label>
                                <input required type="text" id="cns" name="cns" required maxlength="100" placeholder="Digite o CNS">
                            </div>
                            <div class="form-group">
                                <label for="data_nascimento">Data*</label>
                                <input type="date" id="data_nascimento" name="data_nascimento" required placeholder="dd/mm/aaaa">
                            </div>
                            <div class="form-group">
                                <label for="raca_cor">Raça/Cor*</label>
                                <select required id="raca_cor" name="raca_cor" required>
                                    <option value="">Selecionar</option>
                                    <option value="01">Branca</option>
                                    <option value="02">Preta</option>
                                    <option value="03">Parda</option>
                                    <option value="04">Amarela</option>
                                    <option value="05">Indígena</option>
                                    <option value="99">Sem informação</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="sexo">Sexo*</label>
                                <select required id="sexo" name="sexo" required>
                                    <option value="">Selecionar</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="etnia">Etnia</label>
                                <input type="text" id="etnia" name="etnia" maxlength="4" placeholder="Selecionar">
                            </div>
                            <div class="form-group">
                                <label for="nacionalidade">Nacionalidade*</label>
                                <select required id="nacionalidade" name="nacionalidade" required>
                                    <option value="">Selecionar</option>
                                    <option value="10">Brasileira</option>
                                    <option value="20">Naturalizado</option>
                                    <option value="30">Estrangeiro</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="codigo_logradouro">Tipo do Logradouro*</label>
                                <select required id="codigo_logradouro" name="codigo_logradouro" required>
                                    <option value="">Selecionar</option>
                                    <option value='81'>Rua</option>
                                    <option value="8">Avenida</option>
                                </select>
                            </div>  
                        </div>  
                        <div class="form-group">
                                <label for="endereco">Logradouro*</label>
                                <input required type="text" id="endereco" name="endereco" required maxlength="100" placeholder="Digite o logradouro">
                            </div>
                            <div class="form-group">
                                <label for="numero">Número*</label>
                                <input required type="text" id="numero" name="numero" required maxlength="10" placeholder="Digite o número">
                            </div>
                            <div class="form-group">
                                <label for="complemento">Complemento</label>
                                <input type="text" id="complemento" name="complemento" maxlength="30" placeholder="Ex: Apt 101">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro*</label>
                                <input required type="text" id="bairro" name="bairro" required maxlength="60" placeholder="Informe o bairro">
                            </div>
                            <div class="form-group">
                                <label for="cep">CEP*</label>
                                <input required type="text" id="cep" name="cep" required maxlength="9" placeholder="00000-000">
                            </div>
                            <div class="form-group">
                                <label for="telefone">Telefone*</label>
                                <input required type="text" id="telefone" name="telefone" required maxlength="15" placeholder="(00) 00000-0000">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" maxlength="50" placeholder="exemplo@email.com">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="situacao_rua">Situação de Rua?</label>
                                <select required id="situacao_rua" name="situacao_rua" required>
                                    <option value="N">Não</option>
                                    <option value="S">Sim</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn-add">Salvar</button>
                </form>
                    </section>
                </section>

                <section id="secao-listagem" class="conteudo-seccao">
                <!-- Aqui vai toda a listagem -->
                <section class="patients-list">
                <h2>Pacientes Cadastrados</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Data de Nascimento</th>
                                <th>CNS/CPF</th>
                                <th>Telefone</th>
                                <th>Bairro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Exemplo de linha de paciente -->
                            <tr>
                                <td>João da Silva</td>
                                <td>15/03/1985</td>
                                <td>123.456.789-00</td>
                                <td>(11) 91234-5678</td>
                                <td>Centro</td>
                                <td>
                                    <button class="btn-edit">Editar</button>
                                    <button class="btn-delete">Excluir</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            </section>



                <script src="./src/script.js"></script>
            </body>
    HTML;
    return $html;
    }
}