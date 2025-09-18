@echo off
title 🚀 Prueba Técnica Symfony 6 + Vue 3
echo ==============================================
echo 🚀 Iniciando Prueba Técnica Symfony 6 + Vue 3
echo ==============================================
echo.

echo 📦 Levantando servicios Docker...
docker compose up -d --build

echo ⏳ Esperando que los servicios estén listos (30s)...
timeout /t 30 /nobreak >nul

echo 🔧 Configurando Symfony (backend)...
docker exec -it symfony_app composer install --no-interaction
docker exec -it symfony_app php bin/console doctrine:database:create --if-not-exists
docker exec -it symfony_app php bin/console doctrine:migrations:migrate --no-interaction
docker exec -it symfony_app php bin/console doctrine:fixtures:load --no-interaction

echo.
echo ✅ ¡Proyecto listo para usar!
echo.
echo 🌐 URLs disponibles:
echo    ➤ Backend API: http://localhost:8000/api/books
echo    ➤ Frontend:    http://localhost:5173
echo    ➤ MySQL:       localhost:3306 (root / password)
echo.
echo 📚 Para probar la API:
echo    curl http://localhost:8000/api/books
echo.
echo 🧪 Para ejecutar tests:
echo    docker exec -it symfony_app ./bin/phpunit
echo    docker exec -it vue_frontend npm test
echo.
echo 🛑 Para detener servicios:
echo    docker compose down
echo.
pause
