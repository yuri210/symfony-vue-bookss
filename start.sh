#!/bin/bash

echo "🚀 Iniciando Prueba Técnica Symfony 6 + Vue 3"
echo "=============================================="

echo "📦 Levantando servicios Docker..."
docker-compose up -d

echo "⏳ Esperando que los servicios estén listos..."
sleep 10

echo "🔧 Configurando Symfony..."
docker exec -it symfony_app composer install --no-interaction
docker exec -it symfony_app php bin/console doctrine:database:create --if-not-exists
docker exec -it symfony_app php bin/console doctrine:migrations:migrate --no-interaction
docker exec -it symfony_app php bin/console doctrine:fixtures:load --no-interaction

echo "✅ ¡Todo listo!"
echo ""
echo "🌐 URLs disponibles:"
echo "   Backend API: http://localhost:8000/api/books"
echo "   Frontend:    http://localhost:5173"
echo "   MySQL:       localhost:3306 (root/password)"
echo ""
echo "📚 Para probar la API:"
echo "   curl http://localhost:8000/api/books"
echo ""
echo "🛑 Para parar los servicios:"
echo "   docker-compose down"