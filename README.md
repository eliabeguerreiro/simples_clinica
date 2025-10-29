# 🏥 Sistema Multi-Clínicas – Ambiente VPS (Apache + PHP + MySQL)

Este repositório contém a documentação e scripts para implantar um sistema web **multi-clínicas** em um VPS Ubuntu, com:

- **Subdomínios isolados** por cliente (ex: `clinicax.solucoesmedicas.online`)  
- **Banco de dados dedicado** para cada clínica  
- **Ambiente de testes** (`teste.solucoesmedicas.online`)  
- **Scripts de automação** para criação rápida de novos clientes  

> ✅ Ideal para sistemas de gestão médica, odontológica ou qualquer aplicação multi-tenant com isolamento de dados.

---

## 🛠️ Pré-requisitos

- VPS com **Ubuntu 20.04+**
- Apache2, PHP e MySQL instalados
- Domínio principal configurado (ex: `solucoesmedicas.online`)
- Acesso SSH como `root` ou usuário com `sudo`
- Porta 80 aberta no firewall

---

## 📁 Estrutura de Pastas

Cada cliente terá seu próprio ambiente isolado:

/var/www/
├── clinicavivenciar.solucoesmedicas.online/
│   └── public_html/       ← arquivos PHP da clínica
├── outraclinica.solucoesmedicas.online/
│   └── public_html/
└── teste.solucoesmedicas.online/          ← ambiente de testes
    └── public_html/


---

## 🧪 Ambiente de Testes

Antes de entregar para o cliente, use o subdomínio de testes:

- **URL**: `http://teste.solucoesmedicas.online`
- **Banco**: `ambiente_teste_db`
- **Usuário**: `teste_user`

Configure manualmente uma vez (veja [Configuração Inicial](#configuração-inicial)).

---

## 🚀 Script: Criar Novo Cliente

Use o script abaixo para **criar um novo cliente em segundos**.

### Passo 1: Crie o script no VPS

```bash
sudo nano /root/criar_cliente.sh
#!/bin/bash

# -------------------------------------------------
# Script: Criar Novo Cliente (Clínica)
# Uso: sudo /root/criar_cliente.sh nome_do_cliente
# Ex:  sudo /root/criar_cliente.sh clinicavivenciar
# -------------------------------------------------

if [ $# -eq 0 ]; then
    echo "❌ Uso: $0 <nome_do_cliente>"
    echo "   Ex: $0 clinicavivenciar"
    exit 1
fi

CLIENTE=$1
DOMINIO="${CLIENTE}.solucoesmedicas.online"
DB_NAME="${CLIENTE}_db"
DB_USER="${CLIENTE}_user"
DB_PASS=$(openssl rand -base64 16 | tr -d "=+/" | cut -c1-14)
ROOT_MYSQL_PASS="SUA_SENHA_ROOT_AQUI"  # 🔐 ALTERE ISSO!

echo "🚀 Criando ambiente para: $DOMINIO"

# 1. Criar banco de dados
mysql -u root -p"$ROOT_MYSQL_PASS" <<EOF
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

if [ $? -ne 0 ]; then
    echo "❌ Erro ao criar o banco. Verifique a senha do root do MySQL."
    exit 1
fi

# 2. Pasta do site
mkdir -p /var/www/$DOMINIO/public_html
chown -R www-www-data /var/www/$DOMINIO
chmod -R 755 /var/www/$DOMINIO

# 3. Virtual Host
cat > /etc/apache2/sites-available/$DOMINIO.conf <<EOF
<VirtualHost *:80>
    ServerName $DOMINIO
    ServerAlias www.$DOMINIO
    DocumentRoot /var/www/$DOMINIO/public_html

    <Directory /var/www/$DOMINIO/public_html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/${CLIENTE}_error.log
    CustomLog \${APACHE_LOG_DIR}/${CLIENTE}_access.log combined
</VirtualHost>
EOF

# 4. Habilitar e recarregar
a2ensite $DOMINIO.conf > /dev/null
systemctl reload apache2

# 5. Página de boas-vindas
echo "<?php echo '<h1>✅ Bem-vindo à $DOMINIO!</h1>' . '<p>Banco: $DB_NAME | Usuário: $DB_USER</p>'; ?>" > /var/www/$DOMINIO/public_html/index.php
chown www-www-data /var/www/$DOMINIO/public_html/index.php

# 6. Resumo
echo ""
echo "✅ Cliente criado com sucesso!"
echo "--------------------------------------------------"
echo "Subdomínio: http://$DOMINIO"
echo "Pasta:      /var/www/$DOMINIO/public_html"
echo "Banco:      $DB_NAME"
echo "Usuário DB: $DB_USER"
echo "Senha DB:   $DB_PASS"
echo "--------------------------------------------------"
echo "⚠️  Anote a senha do banco! Ela não será mostrada novamente."
echo "➡️  Agora, adicione o registro DNS:"
echo "    Tipo: A | Nome: $CLIENTE | IP: 191.252.210.60"

Passo 2: Torne executável e configure
sudo chmod +x /root/criar_cliente.sh

Passo 3: Use o script
sudo /root/criar_cliente.sh clinicavivenciar

Saída:

✅ Cliente criado com sucesso!
Subdomínio: http://clinicavivenciar.solucoesmedicas.online
Banco:      clinicavivenciar_db
Usuário DB: clinicavivenciar_user
Senha DB:   k8Lm2nPq9RsT4v



🌐 Configuração DNS (obrigatória) 

Após rodar o script, adicione um registro DNS no seu provedor (ex: KingHost): 

Tipo: A
Nome(Host): clinicax
IP: 191.252.210.60
⏳ A propagação pode levar até 1 hora.


🔒 Boas Práticas 

    Nunca use root no PHP — sempre crie usuários dedicados.
    Nunca exponha a porta 3306 — use SSH Tunnel para acesso remoto.
    Use o ambiente de testes antes de entregar para o cliente.
    Anote credenciais em gerenciador seguro (ex: Bitwarden).
    Faça backups regulares dos bancos de dados.

🧰 Ferramentas Recomendadas 

    WinSCP: Upload de arquivos via SFTP  
    MySQL Workbench: Acesso ao banco via SSH Tunnel  
    PuTTY: Terminal SSH com atalho para sudo su -
     
     
