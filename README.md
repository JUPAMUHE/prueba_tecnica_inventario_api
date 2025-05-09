
API RESTful de Gestión de Inventario con Roles (Laravel 10)
Este proyecto es una API RESTful desarrollada con Laravel 10 para gestionar un inventario básico de productos y categorías, con un sistema de autenticación y control de acceso por roles (admin y user).

🚀 Tecnologías Utilizadas
PHP 8.x

Laravel 10.x

MySQL

Laravel Sanctum (para autenticación con tokens)

Composer (gestor de dependencias)

📦 Instalación y Configuración Local
Clona el repositorio:

git clone https://github.com/tuusuario/nombre-del-repo.git
cd nombre-del-repo
Instala las dependencias:

composer install
Copia el archivo .env y configura la base de datos:

cp .env.example .env
Luego, configura en .env:

Luego se debe dirigir a sus gestor de BD de Mysql preferido y crear la bd:
prueba_tecnica_inventario_api

php artisan key:generate
Ejecuta las migraciones y el seeder del admin:

php artisan migrate
php artisan db:seed --class=AdminUserSeeder

🔑 Endpoints de Autenticación
Método	Endpoint	    Rol Requerido	Descripción
POST	/api/register	abierto	        Registra un nuevo usuario (por defecto user)
POST	/api/login	    abierto	        Inicia sesión y retorna token
POST	/api/logout	    user/admin	    Cierra sesión y revoca el token


📦 Endpoints de Productos
Método	    Endpoint	        Rol Requerido	    Descripción
GET	        /api/products	    user/admin	        Lista todos los productos
GET	        /api/products/{id}	user/admin	        Muestra detalles de un producto
POST	    /api/products	    admin	            Crea un nuevo producto
PUT	        /api/products/{id}	admin	            Actualiza un producto
DELETE	    /api/products/{id}	admin	            Elimina un producto

🗂️ Endpoints de Categorías 
Método	    Endpoint	            Rol Requerido	    Descripción
POST	    /api/categories	        admin	            Crea una categoría
PUT	        /api/categories/{id}	admin	            Actualiza una categoría
DELETE	    /api/categories/{id}	admin	            Elimina una categoría

🧠 Decisiones de Diseño
Enum en columna role del modelo users: más eficiente que una tabla relacional para dos roles fijos, esto por varias razones:
Simplicidad: No necesitas crear una tabla adicional (roles) ni una relación (user_role o similar).
Evitas JOINs innecesarios al consultar el rol de un usuario.

Mejor rendimiento:Las consultas son más rápidas porque no hay que hacer relaciones.El valor del rol está directamente en la fila del usuario.

Validación y restricción a nivel de base de datos:Con ENUM('admin', 'user'), la base de datos solo aceptará esos valores.
Esto ayuda a mantener la integridad de los datos.

Menor complejidad para un caso simple:Para una app donde los roles están predefinidos y no cambian (ej: admin, user), una tabla adicional es un "overkill".

Autorización: se usa un middleware personalizado role:admin para proteger endpoints críticos.

Autenticación: Laravel Sanctum permite manejar tokens de forma simple y segura para SPAs y APIs.

Validación: realizada en cada controlador con mensajes claros y personalizados.

Semilla de admin: al ejecutar php artisan db:seed --class=AdminUserSeeder se crea un usuario admin por defecto.

📁 Estructura Base de la Base de Datos
users:
  id, name, email, password, role (enum: admin|user), timestamps

categories:
  id, name, description, timestamps

products:
  id, category_id (foreign), name, description, price, stock, timestamps

🧪 Pruebas de API
Incluye una colección de Postman con ejemplos de todas las rutas (registro, login, CRUD productos, logout, CRUD categorias).

📥 Importar colección:
El archivo API_Inventario_Postman_Collection.json se encuentra en la raíz del repositorio.

🌐 Despliegue
La API está desplegada públicamente en:
🔗 https://mi-api-inventario.fly.dev (reemplazar con tu URL real)


🛡️ Seguridad
Todos los endpoints están protegidos con auth:sanctum.

Se valida el rol en los endpoints que requieren permisos de administrador.

Las respuestas a intentos no autorizados incluyen códigos HTTP y mensajes explicativos.