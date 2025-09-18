# Prueba Técnica - Symfony 6 + Vue 3

Sistema de gestión de libros y reseñas desarrollado con Symfony 6 en el backend y Vue 3 en el frontend, completamente dockerizado.

## 📋 Requisitos del Sistema

- Docker Desktop (Windows/Mac/Linux)
- Git

**No necesitas instalar PHP, Node.js, MySQL ni Composer** - todo está incluido en los contenedores.

## 🚀 Instrucciones de Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/yuri210/symfony-vue-books.git
cd symfony-vue-books
```

### 2. Configurar variables de entorno
```bash
# El archivo .env ya está incluido con la configuración correcta
# No necesitas modificar nada
```

### 3. Levantar los servicios con Docker
```bash
# Levantar todos los servicios (puede tomar unos minutos la primera vez)
docker-compose up -d

# Verificar que los contenedores estén corriendo
docker-compose ps
```

### 4. Instalar dependencias de Symfony
```bash
# Entrar al contenedor de la aplicación
docker exec -it symfony_app bash

# Instalar dependencias
composer install

# Crear la base de datos
php bin/console doctrine:database:create

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate --no-interaction

# Cargar datos de prueba (fixtures)
php bin/console doctrine:fixtures:load --no-interaction

# Salir del contenedor
exit
```

### 5. Verificar que todo funciona

**Backend (API):**
- URL: http://localhost:8000
- Endpoint de libros: http://localhost:8000/api/books

**Frontend (Vue):**
- URL: http://localhost:5173
- Se conecta automáticamente a la API

**Base de datos:**
- Host: localhost:3306
- Usuario: root
- Contraseña: password
- Base de datos: symfony_books

## 📚 Endpoints de la API

### GET /api/books
Obtiene la lista de libros con rating promedio.

**Respuesta de ejemplo:**
```json
[
  {
    "title": "El Arte de Programar",
    "author": "Donald Knuth", 
    "published_year": 1968,
    "average_rating": 4.7
  },
  {
    "title": "Clean Code",
    "author": "Robert C. Martin",
    "published_year": 2008,
    "average_rating": 4.5
  }
]
```

### POST /api/reviews
Crea una nueva reseña para un libro.

**Request body:**
```json
{
  "book_id": 1,
  "rating": 5,
  "comment": "Excelente libro"
}
```

**Respuesta exitosa (201):**
```json
{
  "id": 7,
  "book_id": 1,
  "rating": 5,
  "comment": "Excelente libro",
  "created_at": "2024-12-25 10:30:15"
}
```

**Respuesta de error (400):**
```json
{
  "error": "Errores de validación",
  "details": [
    "La calificación debe estar entre 1 y 5"
  ]
}
```

## 🧪 Pruebas de la API

### Probar GET /api/books
```bash
curl -X GET http://localhost:8000/api/books
```

### Probar POST /api/reviews
```bash
curl -i -X POST http://localhost:8000/api/reviews \
  -H "Content-Type: application/json" \
  --data-raw '{"book_id":17,"rating":5,"comment":"Increible libro, muy recomendado"}'

```

### Probar validaciones (ejemplo de error)
```bash
curl -X POST http://localhost:8000/api/reviews \
  -H "Content-Type: application/json" \
  -d '{
    "book_id": 999,
    "rating": 6,
    "comment": ""
  }'
```

## 📊 Datos de Prueba Incluidos

El sistema incluye los siguientes datos de prueba:

### Libros (3):
1. "El Arte de Programar" - Donald Knuth (1968)
2. "Clean Code" - Robert C. Martin (2008) 
3. "Refactoring" - Martin Fowler (1999)

### Reseñas (6):
- 3 reseñas para "El Arte de Programar" (ratings: 5, 4, 5)
- 2 reseñas para "Clean Code" (ratings: 5, 4)
- 1 reseña para "Refactoring" (rating: 3)

## 🛠️ Comandos Útiles

### Ver logs de los contenedores
```bash
# Logs de todos los servicios
docker-compose logs -f

# Logs solo del backend
docker-compose logs -f app

# Logs solo del frontend
docker-compose logs -f frontend
```

### Parar y reiniciar servicios
```bash
# Parar todos los servicios
docker-compose down

# Reiniciar todos los servicios
docker-compose up -d
```

### Ejecutar comandos dentro del contenedor
```bash
# Entrar al contenedor de Symfony
docker exec -it symfony_app bash

# Ejecutar comandos de Doctrine
docker exec -it symfony_app php bin/console doctrine:migrations:status
```

## 🔧 Solución de Problemas

### Si el frontend no carga:
1. Verificar que el puerto 5173 esté libre
2. Revisar logs: `docker-compose logs frontend`

### Si la API no responde:
1. Verificar que el puerto 8000 esté libre
2. Revisar logs: `docker-compose logs app`

### Si hay problemas con la base de datos:
```bash
# Recrear la base de datos
docker exec -it symfony_app php bin/console doctrine:database:drop --force
docker exec -it symfony_app php bin/console doctrine:database:create
docker exec -it symfony_app php bin/console doctrine:migrations:migrate --no-interaction
docker exec -it symfony_app php bin/console doctrine:fixtures:load --no-interaction
```

## 💡 Pregunta Técnica

**¿Qué cambiarías para escalar esta app a cientos de miles de libros y usuarios?**

Para escalar la aplicación a gran escala, implementaría:

1. **Base de datos**: Migrar a PostgreSQL con índices optimizados en columnas frecuentemente consultadas (title, author). Implementar particionamiento por fecha en reviews.

2. **Cache**: Redis para cachear consultas de libros populares y ratings promedio. Cache distribuido para sesiones de usuario.

3. **API**: Implementar paginación en endpoints, rate limiting por IP/usuario, y compresión gzip. Usar API versioning para backward compatibility.

4. **Arquitectura**: Microservicios separando libros, reseñas y usuarios. Message queues (RabbitMQ) para operaciones asíncronas como cálculo de ratings.

5. **Performance**: CDN para assets estáticos, load balancer con múltiples instancias de la aplicación, database read replicas para consultas de lectura.

6. **Monitoreo**: Logging centralizado (ELK stack), métricas de performance (New Relic), alertas automáticas para errores críticos.

## 📝 Información del Proyecto

- **Branch evaluado**: main
- **Commit final**: HEAD
- **Tiempo estimado de desarrollo**: 5 horas
- **Tecnologías**: Symfony 6.4, Vue 3, Docker, MySQL 8.0

---

## 📋 Checklist de Entregables ✅

- ✅ Repositorio GitHub público
- ✅ Backend Symfony 6 con estructura limpia
- ✅ Frontend Vue 3 funcional
- ✅ Migraciones Doctrine
- ✅ Fixtures con 3 libros y 6 reseñas
- ✅ README completo con instrucciones
- ✅ Dockerización completa
- ✅ Endpoints REST funcionales
- ✅ Validaciones en backend
- ✅ Consulta DQL eficiente para ratings promedio
- ✅ Frontend consumiendo API correctamente