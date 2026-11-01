#!/usr/bin/env bash
set -e

# Configura porta dinamica do Render
if [ -n "$PORT" ]; then
    echo "Configurando Apache na porta $PORT..."
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

# Garante que moodledata exista e tenha permissoes totais
mkdir -p /var/www/moodledata
chown -R www-data:www-data /var/www/moodledata
chmod -R 777 /var/www/moodledata

# Aguarda banco de dados se DATABASE_URL estiver presente
if [ -n "$DATABASE_URL" ]; then
    echo "Aguardando conexao com o banco de dados..."
    php -r "
        \$url = getenv('DATABASE_URL');
        \$p = parse_url(\$url);
        \$host = \$p['host'] ?? 'localhost';
        \$port = \$p['port'] ?? 5432;
        for (\$i = 0; \$i < 30; \$i++) {
            \$fp = @fsockopen(\$host, \$port, \$errno, \$errstr, 2);
            if (\$fp) {
                fclose(\$fp);
                echo \"Banco de dados acessivel em \$host:\$port\n\";
                exit(0);
            }
            sleep(2);
        }
        echo \"Aviso: Timeout aguardando conexao com o banco.\n\";
    " || true

    echo "Verificando se o Moodle precisa de instalacao inicial..."
    NEED_INSTALL=$(php -r "
        define('CLI_SCRIPT', true);
        require_once('/var/www/html/config.php');
        try {
            \$tables = \$DB->get_tables();
            echo empty(\$tables) ? 'yes' : 'no';
        } catch (Exception \$e) {
            echo 'yes';
        }
    " 2>/dev/null || echo "yes")

    if [ "$NEED_INSTALL" = "yes" ]; then
        echo "Iniciando instalacao inicial do Moodle no banco..."
        ADMIN_USER=${MOODLE_ADMIN_USER:-admin}
        ADMIN_PASS=${MOODLE_ADMIN_PASS:-Admin@IFSP2026}
        ADMIN_EMAIL=${MOODLE_ADMIN_EMAIL:-admin@ifsp.edu.br}
        php /var/www/html/admin/cli/install_database.php \
            --adminuser="$ADMIN_USER" \
            --adminpass="$ADMIN_PASS" \
            --adminemail="$ADMIN_EMAIL" \
            --fullname="Monitoria Online — IFSP" \
            --shortname="Monitoria" \
            --agree-license || echo "Instalacao inicial concluida ou dispensada."
    else
        echo "Banco ja inicializado. Executando atualizacoes e registro do plugin local_monitoria..."
        php /var/www/html/admin/cli/upgrade.php --non-interactive || echo "Upgrade finalizado."
    fi
fi

echo "Iniciando servidor web Apache..."
exec "$@"
