#!/bin/bash
# -----------------------------------------------------------------------------
# Injeta as variáveis de ambiente do banco (DB_HOST, DB_NAME, DB_USER, DB_PASS)
# no bloco "extProcessor lsphp" do OpenLiteSpeed. Sem isso, o lsphp NÃO herda
# o ambiente do container e o getenv() do PHP retorna vazio.
# Idempotente: remove entradas antigas antes de reinserir (seguro em restart).
# -----------------------------------------------------------------------------
set -e

CONF="/usr/local/lsws/conf/httpd_config.conf"
VARS="DB_HOST DB_NAME DB_USER DB_PASS TZ"

if [ ! -f "$CONF" ]; then
    echo "[env-entrypoint] ERRO: $CONF não encontrado." >&2
    exit 1
fi

TMP="$(mktemp)"

VARS="$VARS" awk '
BEGIN {
    n = split(ENVIRON["VARS"], names, " ")
    in_block = 0
}
# Início do bloco extProcessor lsphp
/^[[:space:]]*extProcessor[[:space:]]+lsphp[[:space:]]*\{/ {
    in_block = 1
    print
    next
}
in_block {
    # Remove env antigos das nossas variáveis (evita duplicação)
    skip = 0
    for (i = 1; i <= n; i++) {
        pat = "^[[:space:]]*env[[:space:]]+" names[i] "="
        if ($0 ~ pat) { skip = 1; break }
    }
    if (skip) next

    # Fim do bloco: injeta as variáveis antes do "}"
    if ($0 ~ /^[[:space:]]*\}/) {
        for (i = 1; i <= n; i++) {
            if (names[i] in ENVIRON && ENVIRON[names[i]] != "") {
                printf "  env                     %s=%s\n", names[i], ENVIRON[names[i]]
            }
        }
        in_block = 0
    }
    print
    next
}
{ print }
' "$CONF" > "$TMP"

if ! grep -q "env[[:space:]]*DB_HOST=" "$TMP"; then
    echo "[env-entrypoint] AVISO: bloco 'extProcessor lsphp' não encontrado ou DB_HOST vazio." >&2
fi

cat "$TMP" > "$CONF"
rm -f "$TMP"

# Migra listener HTTPS default para SSL_Listener com certificados Cloudflare se necessário
if grep -q "listener HTTPS" "$CONF" && ! grep -q "listener SSL_Listener" "$CONF"; then
    sed -i 's/listener HTTPS[[:space:]]*{/listener SSL_Listener {/' "$CONF"
    sed -i 's|keyFile[[:space:]].*webadmin\.key|keyFile                 /usr/local/lsws/conf/certs/key.pem|' "$CONF"
    sed -i 's|certFile[[:space:]].*webadmin\.crt|certFile                /usr/local/lsws/conf/certs/cert.pem\n  map                     localhost *|' "$CONF"
    sed -i 's/listeners[[:space:]]*HTTP,[[:space:]]*HTTPS/listeners               HTTP, SSL_Listener/' "$CONF"
    echo "[env-entrypoint] Listener SSL_Listener configurado para certificados Cloudflare."
fi

chown lsadm:lsadm "$CONF" 2>/dev/null || true

echo "[env-entrypoint] Variáveis do banco injetadas no extProcessor lsphp."

# Entrega o controle ao entrypoint original da imagem OpenLiteSpeed
exec /entrypoint.sh "$@"

