DROP FUNCTION `FixGeoJson`;

DELIMITER $$
CREATE FUNCTION `FixGeoJson`(`cad` LONGTEXT) RETURNS longtext CHARSET utf8 COLLATE utf8_unicode_ci
    NO SQL
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN

IF LOCATE('{', cad) = 0 THEN
 RETURN cad;
END IF;

SET cad = REPLACE(cad, '\n', '');
SET cad = REPLACE(cad, '\r', '');
SET cad = REPLACE(cad, ' ', '');
IF LEFT(cad, 1) != "{" THEN
  RETURN cad;
END IF;
IF LEFT(cad, 15) = '{"type":"Point"' THEN
 SET cad = REPLACE(cad, ']', ']]');
 SET cad = REPLACE(cad, '[', '[[');
END IF;

SET cad = REPLACE(cad, '{"type":"', '');
SET cad = REPLACE(cad, '","coordinates":', '');
SET cad = REPLACE(cad, '],', ']@');
SET cad = REPLACE(cad, ',', ' ');
SET cad = REPLACE(cad, '[[[[', '~3');
SET cad = REPLACE(cad, '[[[', '~2');
SET cad = REPLACE(cad, '[[', '~1');
SET cad = REPLACE(cad, '[', '');
SET cad = REPLACE(cad, '~3', '(((');
SET cad = REPLACE(cad, '~2', '((');
SET cad = REPLACE(cad, '~1', '(');
SET cad = REPLACE(cad, ']]]]', '~3');
SET cad = REPLACE(cad, ']]]', '~2');
SET cad = REPLACE(cad, ']]', '~1');
SET cad = REPLACE(cad, '~3', ')))');
SET cad = REPLACE(cad, '~2', '))');
SET cad = REPLACE(cad, '~1', ')');
SET cad = REPLACE(cad, ']', '');
SET cad = REPLACE(cad, '@', ',');
SET cad = REPLACE(cad, '}', '');

RETURN cad;
END$$
DELIMITER ;

DROP FUNCTION `GeometryIsValid`;

DELIMITER $$
CREATE FUNCTION `GeometryIsValid`(`ele` GEOMETRY) RETURNS tinyint(4)
    NO SQL
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
  DECLARE t VARCHAR(20);
SET t = ST_GeometryType(ele);

IF t = 'POINT' THEN
  RETURN 100;
END IF;
IF t = 'LINESTRING' THEN
  IF ST_NumPoints(ele) > 0 THEN
    RETURN 100;
  ELSE
    RETURN 101;
  END IF;
END IF;

IF t = 'MULTILINESTRING' THEN
  IF ST_NumPoints(ST_GeometryN(ele, 1)) > 0 THEN
    RETURN 100;
  ELSE
    RETURN 101;
  END IF;
END IF;

IF t = 'POLYGON' THEN
  RETURN PolygonIsValid(ele);
END IF;
IF t = 'MULTIPOLYGON' THEN
  RETURN MultiPolygonIsValid(ele);
END IF;

RETURN 2;
END$$
DELIMITER ;

DROP FUNCTION `GeoreferenceErrorCode`;

DELIMITER $$

CREATE FUNCTION `GeoreferenceErrorCode`(`error_code` INT) RETURNS VARCHAR(255) CHARSET utf8 DETERMINISTIC NO SQL SQL SECURITY INVOKER BEGIN

DECLARE ret VARCHAR(255);

SET ret = (CASE error_code
WHEN 1 THEN 'La latitud o la longitud no est�n en un rango v�lido (-90 a 90 y -180 a 180).'
WHEN 2 THEN 'La coordenada indicada no se encuentra dentro de ning�n elemento de la geograf�a seleccionada.'
WHEN 3 THEN 'El valor para el c�digo no puede ser nulo'
WHEN 4 THEN 'El valor para el c�digo no fue encontrado en la geograf�a indicada.'
WHEN 5 THEN 'El valor para el pol�gono no puede ser nulo'
WHEN 6 THEN 'El valor indicado en la columna del pol�gono no es un texto WKT o GeoJson correcto.'
WHEN 7 THEN 'El pol�gono reconocido no es una geometr�a v�lida.'
WHEN 8 THEN 'El centroide del pol�gono indicado no se encuentra dentro de ning�n elemento de la geograf�a seleccionada.'
WHEN 9 THEN 'La latitud o la longitud contienen valores vac�os.'

WHEN 10 THEN 'La geometr�a no tiene signos de cierre. Es posible que se encuentre incompleta.'
WHEN 101 THEN 'El per�metro exterior del pol�gono no posee puntos.'
WHEN 102 THEN 'El per�metro exterior del pol�gono no est� cerrado. El �ltimo punto debe coincidir con el primero.'
WHEN 103 THEN 'El per�metro exterior del pol�gono debe tener sus puntos ordenados en el sentido de las agujas del reloj (clockwise).'
WHEN 104 THEN 'El per�metro exterior del pol�gono se intersecta consigo mismo.'
WHEN 105 THEN 'Uno de los huecos del pol�gono no posee puntos.'
WHEN 106 THEN 'Uno de los huecos del pol�gono no est� cerrado. El �ltimo punto debe coincidir con el primero.'
WHEN 107 THEN 'Los huecos del pol�gono deben tener sus puntos ordenados en el sentido contrario a las agujas del reloj (counter-clockwise).'
WHEN 108 THEN 'Uno de los huecos del pol�gono se intersecta consigo mismo.'
WHEN 109 THEN 'Un hueco del pol�gono excede los l�mites de su per�metro.'
WHEN 110 THEN 'Los pol�gonos de un pol�gono m�ltiple no pueden superponerse.'
WHEN 111 THEN 'Los huecos de un pol�gono no pueden superponerse.'
WHEN 120 THEN 'El pol�gono m�ltiple no contiene pol�gonos.'

ELSE 'C�digo no identificado'

END);

RETURN ret;

END$$
DELIMITER ;

DELIMITER $$

CREATE FUNCTION `LineStringCentroid`(`ele` GEOMETRY) RETURNS point
    NO SQL
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN

DECLARE n INT;
DECLARE ttl INT;
DECLARE totalLength DOUBLE;
DECLARE length DOUBLE;
DECLARE nX DOUBLE;
DECLARE nY DOUBLE;
DECLARE p1 POINT;
DECLARE p2 POINT;

SET ttl = ST_NumPoints(ele);

IF ttl = 0 THEN
  RETURN NULL;
END IF;
IF ttl = 1 THEN
  RETURN ST_PointN(ele, 1);
END IF;

 SET n = 1;
 SET totalLength = 0;
 SET nX = 0;
 SET nY = 0;

 count_loop: LOOP
    IF n >= ttl THEN
      LEAVE count_loop;
    END IF;
    SET n = n + 1;
    SET p1 = ST_PointN(ele, n-1);
    SET p2 = ST_PointN(ele, n);
    SET length = ST_DISTANCE(p1, p2);
    SET nX = nX + (ST_X(p1) + ST_X(p2)) / 2 * length;
    SET nY = nY + (ST_Y(p1) + ST_Y(p2)) / 2 * length;
    SET totalLength = totalLength + length;
  END LOOP;

RETURN POINT(nX / totalLength, nY / totalLength);

END$$
DELIMITER ;


DELIMITER $$

CREATE FUNCTION `GeometryCentroid`(`ele` GEOMETRY) RETURNS point
    NO SQL
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN

DECLARE t VARCHAR(20);
DECLARE nPoints INT;
DECLARE nX DOUBLE;
DECLARE nY DOUBLE;

  SET t = ST_GeometryType(ele);

  IF t = 'POLYGON' OR t = 'MULTIPOLYGON' THEN
    RETURN ST_CENTROID(ele);
  END IF;

IF t = 'LINESTRING' THEN
	RETURN LineStringCentroid(ele);
END IF;

IF t = 'MULTILINESTRING' THEN
	RETURN MultiLineStringCentroid(ele);
END IF;

IF t = 'POINT' THEN
    RETURN ele;
  END IF;

RETURN NULL;

END$$
DELIMITER ;

DELIMITER $$

CREATE FUNCTION `MultiLineStringCentroid`(`ele` GEOMETRY) RETURNS point
    NO SQL
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN

DECLARE n INT;
DECLARE ttl INT;
DECLARE totalLength DOUBLE;
DECLARE length DOUBLE;
DECLARE nX DOUBLE;
DECLARE nY DOUBLE;
DECLARE p1 POINT;
DECLARE p2 POINT;
DECLARE currentLine INT;
DECLARE totalLines INT;

DECLARE line LINESTRING;

SET totalLength = 0;
SET nX = 0;
SET nY = 0;

SET currentLine = 0;
SET totalLines = ST_NumGeometries(ele);

lines_loop: LOOP
    IF currentLine >= totalLines THEN
      LEAVE lines_loop;
    END IF;
	SET currentLine = currentLine + 1;
    SET line = ST_GeometryN(ele, currentLine);
    SET ttl = ST_NumPoints(line);
	IF ttl > 1 THEN
		SET n = 1;
		count_loop: LOOP
			IF n >= ttl THEN
			  LEAVE count_loop;
			END IF;
			SET n = n + 1;
			SET p1 = ST_PointN(line, n-1);
			SET p2 = ST_PointN(line, n);
			SET length = ST_DISTANCE(p1, p2);
			SET nX = nX + (ST_X(p1) + ST_X(p2)) / 2 * length;
			SET nY = nY + (ST_Y(p1) + ST_Y(p2)) / 2 * length;
			SET totalLength = totalLength + length;
		END LOOP;
    END IF;
  END LOOP;

RETURN POINT(nX / totalLength, nY / totalLength);

END$$
DELIMITER ;

UPDATE version SET ver_value = '037' WHERE ver_name = 'DB';

