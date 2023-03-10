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

* Habilite PHP en su servidor web (versión sugerida: *8.1* o superior). En caso de necesitar información sobre
  cómo habilitar PHP sobre un servidor web Apache, puede consultar esta página:
  [PHP con Apache sobre Windows](https://micode-manager.blogspot.com/2023/01/php-con-apache-sobre-windows.html).
* Descargue el contenido de este repositorio en un directorio del servidor web (nombre sugerido: *tareas*).
* Cree una base de datos en un servidor mariaDB o mySQL (nombre sugerido para la base de datos: *sti_tareas*).
* Cree las tablas requeridas usando el SQL contenido en el archivo `bdd.sql`.
* Realice una copia o renombre el archivo `lib/data/bdd.ini-ejemplo` con nombre `lib/data/bdd.ini`. Editelo
  con los datos de acceso a la base de datos. El archivo debe contener la siguiente información:
	 * **servidor**: Path o nombre del servidor donde se encuentra el motor de base de datos.
	 * **bdd**: Nombre dado a la base de datos.
	 * **usuario**: Nombre del usuario autorizado para consultas.
	 * **password**: Contraseña.
* Abra el archivo `index.php` en su navegador web y podrá usar el registro de actividades.

Este ejercicio no usa URLs amigables o direccionamiento dinámico, por lo que no requiere personalizaciones al
respecto en el servidor web.

(Marzo 08/2023)