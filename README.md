# Sistema para registro de actividades usando PHP y MariaDB

## Alcance

Se requiere hacer un sistema web para:

* Registrar actividades o tareas en una empresa para saber qué trabajo está pendiente por hacer.
* A dichas actividades se les debe asignar quién de los empleados las ejecutará desde una lista.
* A una actividad se le puede asignar sólo un empleado.
* Un empleado puede estar asignado a más de una actividad.

De la actividad se desea saber:

* Estado (si se realizó o está pendiente),
* La fecha y hora estimada de ejecución.
* Días de retraso de la ejecución respecto al día de hoy (si no hay retraso indicar "0" cero).
* Quién está asignado a dicha actividad.

Para ello se debe crear una base de datos que contenga la información de dichas actividades
y los empleados.

## Objetivos a alcanzar

Se desea que haga lo siguiente:

1. Crear un script con la estructura y datos.
2. Crear un CRUD que permita: listar las actividades, crear una actividad llenando los
   campos incluyendo el empleado asignado, editar la actividad y eliminarla

## Instrucciones de uso

Siga las siguientes instrucciones para ejecutar este proyecto correctamente:

* Habilite PHP en su servidor web (versión sugerida: *8.1.4* o superior).
* Descargue el contenido de este repositorio en un directorio del servidor web (nombre sugeridos: *tareas*).
* Cree una base de datos en un servidor mariaDB o mySQL (nombre sugerido para la base de datos: *sti_tareas*).
* Cree las tablas requeridas usando el SQL contenido en el archivo `bdd.sql`.
* Realice una copia o renombre el archivo `lib/data/bdd.ini-ejemplo` con nombre `lib/data/bdd.ini`. Editelo
  con los datos de acceso a la base de datos.

Este ejercicio no usa URLs amigables o direccionamiento dinámico, por lo que no requiere personalizaciones al
respecto en el servidor web.

(Marzo 08/2023)