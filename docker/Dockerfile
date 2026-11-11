# OpenLiteSpeed + PHP 8.3 (lsphp83)
ARG OLS_VERSION=1.8.1
FROM litespeedtech/openlitespeed:${OLS_VERSION}-lsphp83

ENV TZ=America/Sao_Paulo

# Atualiza lsphp83 e lsphp83-common para alinhar a versão do core PHP (8.3.36)
# com os módulos mais recentes do repositório LiteSpeed (lsphp83-curl, lsphp83-mysql, lsphp83-intl).
# Isso previne o 'symbol lookup error: smart_str_ferealloc' que causava 503 Service Unavailable.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        lsphp83 lsphp83-common lsphp83-mysql lsphp83-curl lsphp83-intl tzdata \
    && rm -rf /var/lib/apt/lists/*

# Ajustes de php.ini (equivalentes ao XAMPP)
COPY php/99-app.ini /usr/local/lsws/lsphp83/etc/php/8.3/mods-available/99-app.ini

# Virtual host e template Docker (docRoot + suporte a .htaccess e rotas amigáveis)
COPY ols/docker.conf /usr/local/lsws/conf/templates/docker.conf
COPY ols/vhconf.conf /usr/local/lsws/conf/vhosts/Example/vhconf.conf
COPY ols/httpd_config.conf /usr/local/lsws/conf/httpd_config.conf

# Entrypoint que injeta as variáveis do banco no extProcessor do OLS
COPY env-entrypoint.sh /usr/local/bin/env-entrypoint.sh
RUN chmod +x /usr/local/bin/env-entrypoint.sh \
    && mkdir -p /var/www/vhosts/localhost/html \
    && chown -R lsadm:lsadm /usr/local/lsws/conf

WORKDIR /var/www/vhosts/localhost/html

EXPOSE 80 443

ENTRYPOINT ["/usr/local/bin/env-entrypoint.sh"]
