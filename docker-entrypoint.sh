#!/bin/bash
set -e

# Desativar módulos MPM em conflito e ativar o prefork (obrigatório para PHP)
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true
a2enmod mpm_prefork

# Configurar o Apache para escutar na variável de porta dinâmica do Railway ($PORT)
if [ -n "$PORT" ]; then
  sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
  sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf
fi

# Iniciar o Apache em primeiro plano
exec apache2-foreground