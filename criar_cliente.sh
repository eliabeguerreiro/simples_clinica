#!/bin/bash
# Criar Novo Cliente (Clínica)
# Uso: sudo /root/criar_cliente.sh <nome_cliente>
# Ex:  sudo /root/criar_cliente.sh clinicavivenciar

set -euo pipefail
IFS=$'\n\t'

if [[ $EUID -ne 0 ]]; then
  echo "❌ Execute como root ou com sudo"
  exit 1
fi

if [ $# -lt 1 ]; then
  echo "❌ Uso: $0 <nome_do_cliente>"
  exit 1
fi

CLIENTE="$1"
# Normaliza (apenas letras, números e hífen)
CLIENTE_SAFE=$(echo "$CLIENTE" | tr '[:upper:]' '[:lower:]' | sed 's/[^a-z0-9-]//g')
DOMINIO="${CLIENTE_SAFE}.seu-dominio.com"
DB_NAME="${CLIENTE_SAFE}_db"
DB_USER="${CLIENTE_SAFE}_user"
DB_PASS=$(openssl rand -base64 16 | tr -d "=+/" | cut -c1-14)
LOGFILE="/root/clients_created.log"

echo "🚀 Criando ambiente para: $DOMINIO"

# Obtain MySQL root password from env or prompt
MYSQL_ROOT_PW="${MYSQL_ROOT_PW:-}"
if [ -z "$MYSQL_ROOT_PW" ]; then
  read -s -p "Senha root MySQL: " MYSQL_ROOT_PW
  echo
fi

# 1) Criar banco e usuário (usa o root MySQL)
mysql -u root -p"$MYSQL_ROOT_PW" <<EOF
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

echo "✅ Banco criado: $DB_NAME (usuário: $DB_USER)"

# 2) Pasta do site
SITE_DIR="/var/www/$DOMINIO/public_html"
mkdir -p "$SITE_DIR"
chown -R www-data:www-data "/var/www/$DOMINIO"
chmod -R 755 "/var/www/$DOMINIO"

# 3) VirtualHost Apache
VHOST_CONF="/etc/apache2/sites-available/$DOMINIO.conf"
cat > "$VHOST_CONF" <<EOF
<VirtualHost *:80>
    ServerName $DOMINIO
    ServerAlias www.$DOMINIO
    DocumentRoot $SITE_DIR

    <Directory $SITE_DIR>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/${CLIENTE_SAFE}_error.log
    CustomLog \${APACHE_LOG_DIR}/${CLIENTE_SAFE}_access.log combined
</VirtualHost>
EOF

# 4) Habilitar site e reload
a2ensite "$DOMINIO.conf" >/dev/null 2>&1 || true
a2enmod rewrite >/dev/null 2>&1 || true
systemctl reload apache2

# 5) Página de boas-vindas
cat > "$SITE_DIR/index.php" <<PHP
<?php
echo '<h1>✅ Bem-vindo à $DOMINIO!</h1>';
echo '<p>Banco: $DB_NAME | Usuário DB: $DB_USER</p>';
?>
PHP
chown www-data:www-data "$SITE_DIR/index.php"

# 6) Registrar as credenciais (arquivo protegido)
{
  echo "=== $(date +'%F %T') ==="
  echo "dominio: $DOMINIO"
  echo "site_dir: $SITE_DIR"
  echo "db_name: $DB_NAME"
  echo "db_user: $DB_USER"
  echo "db_pass: $DB_PASS"
  echo
} >> "$LOGFILE"
chmod 600 "$LOGFILE"

# 7) Resultado
echo ""
echo "✅ Cliente criado com sucesso!"
echo "Subdomínio: http://$DOMINIO"
echo "Pasta:      $SITE_DIR"
echo "Banco:      $DB_NAME"
echo "Usuário DB: $DB_USER"
echo "Senha DB:   $DB_PASS"
echo ""
echo "➡️ Adicione um registro DNS A apontando $CLIENTE para o IP da VPS"
echo "➡️ Para habilitar HTTPS execute: sudo certbot --apache -d $DOMINIO"
echo "➡️ Credenciais salvas em $LOGFILE (permissões 600)"