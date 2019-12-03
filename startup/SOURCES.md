# poblaciones
Herramienta para la publicaci�n colaborativa de datos espaciales de poblaci�n

En esta carpeta se encuentran los elementos necesarios para inicializar una instalaci�n de desarrollo de Poblaciones.

1. Instalar el software necesario.

PHP 7.1 o posterior.
Python 2.7 (s�lo requerido para conversiones de archivos SPSS)
VueJS.

2. Descargar los fuentes del repositorio.

3. Obtener las dependencias de los fuentes (frontend y services)
  
  frontend>npm install
  services>php composer.phar %*

4. Crear la base de datos 

  crear en mysql (5.6 o posterior) una base de datos nueva.

  si posee una backup de otra instalaci�n:
   restaurar el backup.

  si desea comenzar con una base nueva:  
   ejecutar el script dbscript.sql
   ejecutar scripts de actualizaciones (startup/scripts)
   instalar opcionalmente un data-pack para su pa�s ()
  
5. Archivos de configuraci�n

  renombrar services/config/settings.sample.php a services/config/settings.php 
 
  indicar dentro de ese archivo los datos de conexi�n a la base de datos. opcionalmente indicar configuraciones para el env�o de notificaciones por correo electr�nico.

  renombrar frontend/config/dev.env.sample.js a fronend/config/dev.env.js
 
  indicar dentro de ese archivo un key v�lido de google maps

6. Crear sitios para el servidor de servicios

  agregar en hosts la entrada desa.poblaciones.org (si se utiliza otra, modificar los archivos de configuraci�n en 5 para reflejar el cambio).
  apuntar desde IIS o apache a la carpeta /services/web resolviendo con ella la navegaci�n de http://desa.poblaciones.org (o la ruta que se haya elegido)

7. Storage

  los cach�s y logs ser�n almacenados en /services/storage. Revisar que haya sobre esa carpeta permisos de escritura.

8. Iniciar el frontend (el servidor de vuejs):
 
  frontend>npm run dev
  