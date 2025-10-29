# 🏥 Sistema Multi-Clínicas – Guia de deploy em VPS (Apache + PHP + MySQL)

Este documento descreve como implantar rapidamente o sistema em uma VPS Ubuntu e um script auxiliar para criar ambientes isolados por cliente (subdomínios).

Principais pontos
- Cada cliente recebe um diretório / VirtualHost e um banco de dados dedicado.
- Script automático para criar pasta, vhost e banco.
- Recomendações de segurança, backup e SSL.

Pré-requisitos
- VPS Ubuntu 20.04+ (ou similar)
- Acesso SSH com sudo/root
- Apache2, PHP (versão compatível) e MySQL/MariaDB
- Domínio principal configurado para aceitar subdomínios
- Porta 80/443 abertas no firewall

Instalação básica do servidor (exemplo)
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-zip php-gd php-json php-curl -y
sudo systemctl enable apache2
sudo systemctl enable mysql
```

Script para criar novo cliente (resumo)
- Cria database + usuário
- Cria pasta /var/www/<cliente>/public_html
- Cria VirtualHost Apache e ativa o site
- Gera uma página index simples e registra credenciais em /root/clients_created.log

Uso do script
1. Transfira o script para a VPS em `/root/criar_cliente.sh`
2. Torne executável:
   sudo chmod +x /root/criar_cliente.sh
3. Execute:
   sudo /root/criar_cliente.sh clinicavivenciar

Observações importantes
- O script pede a senha root do MySQL se não estiver definida via variável de ambiente `MYSQL_ROOT_PW`.
- Após criar o site, adicione um registro DNS A apontando `clinicavivenciar.seudominio.com` para o IP da VPS.
- Para habilitar HTTPS use Certbot:
  sudo apt install certbot python3-certbot-apache
  sudo certbot --apache -d clinicavivenciar.seudominio.com

Segurança recomendada
- Não use root no PHP; crie usuários específicos para a aplicação.
- Não exponha MySQL para a internet (porta 3306).
- Ative UFW e permita apenas SSH/HTTP/HTTPS:
  sudo ufw allow OpenSSH
  sudo ufw allow 80/tcp
  sudo ufw allow 443/tcp
  sudo ufw enable
- Configurar backups automáticos do banco (cron + mysqldump) e das pastas /var/www

Backup rápido de banco
```bash
mysqldump -u root -p nome_do_banco > /root/backups/nome_do_banco_$(date +%F).sql
```

Registro de clientes criados
- As credenciais são armazenadas em `/root/clients_created.log`. Proteja esse arquivo.

Suporte e troubleshooting
- Logs do Apache: /var/log/apache2/error.log
- Logs do MySQL: /var/log/mysql/error.log
- Permissões: chown -R www-data:www-data /var/www/<domínio>/public_html

---
Pequenas melhorias e customizações podem ser feitas conforme seu fluxo de deploy. Se quiser, adapto o script para criar também um banco de testes, importar um dump inicial ou provisionar via Ansible.


