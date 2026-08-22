UPDATE `desenhos`
SET `diretorio` = CONCAT('/srv/wl/', SUBSTRING(`diretorio`, 7))
WHERE LOWER(`diretorio`) LIKE 'c:/wl/%';

UPDATE `desenhos_temp`
SET `diretorio` = CONCAT('/srv/wl/', SUBSTRING(`diretorio`, 7))
WHERE LOWER(`diretorio`) LIKE 'c:/wl/%';

UPDATE `lixo_desenhos`
SET `diretorio` = CONCAT('/srv/wl/', SUBSTRING(`diretorio`, 7))
WHERE LOWER(`diretorio`) LIKE 'c:/wl/%';

UPDATE `projeto`
SET `diretorio` = CONCAT('/srv/wl/', SUBSTRING(`diretorio`, 7))
WHERE LOWER(`diretorio`) LIKE 'c:/wl/%';

UPDATE `desenhos`
SET `diretorio` = REPLACE(`diretorio`, CHAR(92), '/')
WHERE INSTR(`diretorio`, CHAR(92)) > 0;

UPDATE `desenhos_temp`
SET `diretorio` = REPLACE(`diretorio`, CHAR(92), '/')
WHERE INSTR(`diretorio`, CHAR(92)) > 0;

UPDATE `lixo_desenhos`
SET `diretorio` = REPLACE(`diretorio`, CHAR(92), '/')
WHERE INSTR(`diretorio`, CHAR(92)) > 0;

UPDATE `projeto`
SET `diretorio` = REPLACE(`diretorio`, CHAR(92), '/')
WHERE INSTR(`diretorio`, CHAR(92)) > 0;

UPDATE `desenhos`
SET `diretorio` = REPLACE(`diretorio`, '//', '/')
WHERE INSTR(`diretorio`, '//') > 0;

UPDATE `desenhos_temp`
SET `diretorio` = REPLACE(`diretorio`, '//', '/')
WHERE INSTR(`diretorio`, '//') > 0;

UPDATE `lixo_desenhos`
SET `diretorio` = REPLACE(`diretorio`, '//', '/')
WHERE INSTR(`diretorio`, '//') > 0;

UPDATE `projeto`
SET `diretorio` = REPLACE(`diretorio`, '//', '/')
WHERE INSTR(`diretorio`, '//') > 0;
