#!/bin/bash
# Inicia o servidor embutido do PHP apontando diretamente para a pasta public e usando a porta do Railway
php -S 0.0.0.0:$PORT -t public