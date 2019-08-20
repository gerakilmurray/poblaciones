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

# Cr�ditos

Agust�n Salvia y Pablo De Grande establecieron en 2016 el alcance inicial, proyectada como una herramienta para la publicaci�n y visualizaci�n de informaci�n espacial de datos sociales de poblaci�n. 
El dise�o de la arquitectura y el modelo de datos dependieron de Pablo De Grande y Rodrigo Queipo, quienes desarrollar el primer prototipo funcional del visor durante 2017. 

La participaci�n de Gimena del R�o, a partir de 2018, permiti� enmarcar al proyecto como plataforma colaborativa acad�mica de ciencia abierta y situar sus caracter�sticas dentro del espacio de discusi�n de las Humanidades Digitales. 

Entre 2018 y 2019 se complet� la segunda versi�n del visor y la interfaz web de administraci�n de datos espaciales, etapa de desarrollo que estuvo a cargo de Gonzalo M�ndez y Pablo De Grande. Juan Ignacio Bonfiglio fue responsable de la evaluaci�n de uso y comentarios de dicha etapa.

Las cartograf�as publicadas del Observatorio de la Deuda Social en la versi�n 2019 fueron puestas online por Juan Bonfiglio. Los datos p�blicos de la versi�n 2019 fueron procesados por Pablo De Grande, en colaboraci�n con Agust�n Salvia para los indicadores censales 2010-2001-1991.

Nidia Her�ndez y Romina De Le�n trabajaron en la comunicaci�n de contenidos, elaborando videos, tutoriales y estructurando el contenido de la p�gina institucional, bajo la supervisi�n de Gimena del R�o. Ezequiel Soto trabaj� en el dise�o gr�fico de la p�gina institucional.

La realizaci�n del proyecto se benefici� durante todo el proceso del apoyo del Observatorio de la Deuda Social Argentina de la Universidad Cat�lica Argentina (ODSA).

# Licencia
Poblaciones - Plataforma abierta de datos espaciales de poblaci�n.

Copyright (C) 2018-2019. Consejo Nacional de Investigaciones Cient�ficas y T�cnicas (CONICET) y Universidad Cat�lica Argentina (UCA). 

El c�digo fuente se encuentra bajo licencia GNU GPL version 3 o posterior.
