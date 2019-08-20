# Poblaciones
Aplicaci�n web para la publicaci�n colaborativa de datos espaciales de poblaci�n

# Introducci�n

Poblaciones es una aplicaci�n web que permite visualizar y cargar datos georreferenciados de poblaci�n.

Funciona como un servidor aut�nomo de informaci�n geoespacial para navegadores web, apoyado en PHP 7.1 y MySql 5.6. Utiliza Google Maps como provedor del mapa de base, dando en forma permanente acceso a los servicios asociados de StreetView.

Permite subir datos y metadatos de informaci�n espacial mediante una interfaz moderna y simple, permitiendo a los usuarios administrar sus producciones dentro del sitio. 

# Caracter�sticas

* La aplicaci�n permite a los usuarios registrarse y crear cartograf�as. La informaci�n subida por los usuarios puede ser georreferenciada a partir de cartograf�a de base disponible en el sitio (ej. pol�gonos de provincias, departamentos, radios censales) o por sus coordenadas (latitud/longitud).

* La subida de archivos se realiza por medio de archivos CSV o SPSS (.sav).

* La plataforma permite a m�ltiples usuarios administrar y anexar su informaci�n, pudiendo luego los usuarios que visitan el mapa poder ver esta informaci�n en forma simult�nea (colocar informaci�n provista por diferentes personas en una sola vista). 

* Los administradores del sitio pueden marcar conjuntos de datos como 'datos p�blicos', los cuales son destacados a los usuarios para facilitar la consulta.

* El sitio realiza indexaci�n full-text sobre las regiones en el mapa, las entidades y los indicadores publicados, permitiendo a los usuarios acceder desde un buscador a cualquiera de estos elementos. 

* La informaci�n disponible puede ser visualizada por segmentos espaciales, tales que provincias, municipios, barrios, localidades. La plataforma permite agregar m�ltiples niveles de segmentaci�n, los cuales agrupan luego a los datos subidos por los usuarios (ej. listado de establecimientos educativos).

* Permite capturar las visualizaciones en archivos PNG y descargar los datos en formato CSV o SPSS (.sav).

* La plataforma produce estad�sticas propias de rendimiento y de acceso al sitio, adem�s de integrarse con Google Analytics y AddThis.

# Requerimientos

La aplicaci�n puede ser alojada en un servidor compartido (ej. hostgator), dado que no requiere de la ejecuci�n de instalaciones con permisos de administrador en el servidor. Son sus requisitos de software:

- MySql 5.6 � superior.
- PHP 7.1 � superior.
- Python 2.7 (requisito para permitir subir y descargar informaci�n en formato SPSS [.sav])
- SO: indistinto.

# Demo

El sitio se encuentra operativo con datos demogr�ficos, sociales y pol�ticos de la Argentina en https://poblaciones.org.

# Licencia
Poblaciones - Plataforma abierta de datos espaciales de poblaci�n.

Copyright (C) 2018-2019. Consejo Nacional de Investigaciones Cient�ficas y T�cnicas (CONICET) y Universidad Cat�lica Argentina (UCA). 

El c�digo fuente se encuentra bajo licencia GNU GPL version 3 o posterior.
