#!/usr/bin/env bash
# Render start script for PHP project

set -o errexit  # exit on error

echo "🚀 Starting PHP server on port $PORT..."
echo "📁 Document root: public/"
echo "🌍 Environment: production"

# Démarrer le serveur PHP
exec php -S 0.0.0.0:$PORT -t public
