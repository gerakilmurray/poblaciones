#!/bin/bash

# Restaura un dump de sql en la base de beta.
# Los datos de conexión a la base los obtiene del archivo de configuración de beta.php
# El dump debe estar descomprimido.
# Recibe cómo parámetro el nombre del dump.
# Hace splits del archivo de dump para que no de timeout antes de restaurar uno por uno.
# Los tamaño se configuran en las variables maxsize y splitsize.

# Validación de parámetros
if [ $# -lt 2 ] ; then
	echo "Debe pasarse como parámetro el archivo de dump (no puede estar comprimido) y la base de datos"
	exit
fi

# Tamaño a pardir del cual divide archivos, en bytes
maxsize=60000000

# Tamaño máximo de los splits en bytes
splitsize=30000000

# Subdirectorio para creación de archivos
base=restoredb

# Datos de conexión a la base de datos
dbname=$2
dbuser=root
dbpass=ffG.2019

echo "- Restaurando $2 desde $1."


echo "- Crea subdirectorio para generar los archivos y lo limpia... "
rm -rf $base
mkdir -p $base
cd $base
rm -f *.sql

echo "- Crea archivos seriados uno por cada tabla..."
csplit -s -ftable "../$1" "/-- Table structure for table/" {*}

# Si hubo error sale
[ $? -eq 0 ] || exit

# La primera tabla tiene solamente el header que va a ir en cada archivo
# Renombra header a head
mv -v table00 header
cat header

# La última tabla tiene el footer que va a ir en cada archivo
FILE=`ls -v1 table*| tail -n 1`
# Extrae el footer
csplit -b '%d' -s -f$FILE $FILE "/40103 SET TIME_ZONE=@OLD_TIME_ZONE/" {*}
# Renombra el footer a foot
mv -v ${FILE}1 foot
cat foot

# Borra tabla anterior al split
mv -vf ${FILE}0 ${FILE}

echo "- Para los archivos grandes separa los inserts en partes..."
for FILE in `ls -S1 table*`; do
	FILESIZE=$(stat -c%s "$FILE")
	# Si el archivo es mayor a 150 mb
	if (($FILESIZE > $maxsize)); then
		echo "- Separa el create de los inserts: ${FILE}"
		csplit -s -f$FILE $FILE "/INSERT INTO /"
		echo "- Separa los insert por líneas"
		split --line-bytes=$splitsize ${FILE}01 ${FILE}01
		# Limpia luego de la separación
		mv -vf "${FILE}00" $FILE
		rm -v ${FILE}01
	fi
done

echo "- Crea archivos definitivos con header y footer..."
for FILE in `ls -1 table*`; do
	# Obtiene el nombre de la tabla para nombrar los archivos
	NAME=`head -n1 $FILE | cut -d$'\x60' -f2`
	cat header $FILE foot > "_${FILE}.${NAME}.sql"
done

echo "- Borra archivos intermedios"
rm -vf header foot table*


echo "- Restaura en la base de datos..."
for FILE in `ls -v1 _table*.sql`; do
	echo "Archivo a restaurar: ${FILE}"
	mysql -u$dbuser -p$dbpass $dbname < "$FILE"
	# Si hubo error sale
	[ $? -eq 0 ] || exit
	echo "...OK"
done

cd ..

echo "- Restaura funciones..."
mysql -u$dbuser -p$dbpass $dbname < "${1}-fn"

#read -p "Borrar archivos creados? [S/n]" -n 1 -r
#echo # empty line
#if ! [[ $REPLY =~ ^[Nn]$ ]]; then#
	echo "- Borra archivos"
	rm -rf $base
#fi

echo "- FIN."

