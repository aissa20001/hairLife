HairLife: Plataforma de Cuidado Capilar Personalizado 


Descripción del Proyecto HairLife


Es una plataforma web interactiva diseñada para ofrecer recomendaciones personalizadas de productos y rutinas para el cuidado del cabello. El sistema permite a los usuarios completar un cuestionario sobre su tipo de pelo y cuero cabelludo para recibir consejos y sugerencias a medida. La plataforma cuenta con dos roles principales: el usuario, que accede a la información personalizada, y el administrador, que gestiona el catálogo de productos.

Tecnologías Utilizadas Frontend: HTML, CSS y JavaScript

Backend: PHP con el framework Laravel

Base de Datos: MySQL

Funcionalidades Clave Cuestionario interactivo para el análisis capilar.

Recomendación de productos personalizados.

Gestión de productos para el administrador (CRUD).

DEMOSTRACIÓN DE LA PLATAFORMA HAIRLIFE:


<img width="576" height="318" alt="image" src="https://github.com/user-attachments/assets/9fd46b96-0172-461b-ae29-202d8a4c418b" />

<img width="591" height="285" alt="image" src="https://github.com/user-attachments/assets/127573f9-c979-4a8b-ba89-4dc27b769d48" />

<img width="597" height="287" alt="image" src="https://github.com/user-attachments/assets/cd8590c5-beb2-47ce-b232-886971c33135" />

<img width="610" height="295" alt="image" src="https://github.com/user-attachments/assets/2857791b-c243-4d89-9ddf-8bbe5ee6c83e" />










Dado que el proyecto HairLife no está desplegado, puedes probar toda su funcionalidad siguiendo estos pasos:

1- Clonar el repositorio en tu máquina:
git clone https://github.com/aissa20001/hairlife.git

2- Instalar las dependencias de PHP usando Composer:
composer install

3- Configurar la base de datos en el archivo .env

4- Inicializar la Base de Datos y Crear perfiles de prueba
php artisan migrate --seed

5- Crear el enlace simbólico
php artisan storage:link

6- Iniciar el servidor de desarrollo de Laravel
php artisan serve

PERFILES DE PRUEBA

user: aissa   password: 1234
user: carlota password: 5678
user: ana     password:9876

Documentación Completa del Proyecto Para obtener información detallada sobre la estructura, la implementación, las capturas de pantalla de la aplicación y la gestión de bases de datos, puedes consultar el documento completo del proyecto:

Haz clic aquí para ver la documentación completa en PDF:



[documentación.pdf](https://github.com/aissa20001/hairLife/blob/main/documentaci%C3%B3n.pdf)
