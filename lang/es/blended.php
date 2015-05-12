<?PHP
/*********************************************************************************
 * Module developed at the University of Valladolid
 * Designed and directed by Juan Pablo de Castro with the effort of many other
 * students of telecommunication engineering of Valladolid
 * Copyright 2009-2011 EdUVaLab http://www.eduvalab.uva.es
 * this module is provides as-is without any guarantee. Use it as your own risk.

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

 * @author Jéssica Olano López,Pablo Galan Sabugo, David Fernández, Natalia Haro, Juan Pablo de Castro and other contributors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blended
 * 
 *
 * Library of functions and constants for module blended
 *
 *********************************************************************************/

$string['modulename']       = 'Blended';
$string['modulenameplural'] = 'Blendeds';

//mod_form.php
$string['name']               = 'Nombre';
$string['description']        = 'Descripción';
$string['idmethod']           = 'Método de identificación';
$string['idmethod_help']           = 'Los usuarios se pueden identificar mediante diversos identificadores: el número interno de Moodle, su número de identificación introducido en su perfil y cualquier campo personalizado añadido por el administrador.';

$string['coded']              = 'Codificado';
$string['plain']              = 'Plano';
$string['idtype']             = 'Identificador';
$string['idtype_help'] = '<br><br> <b>Id. de usuario: </b>Identificador interno de <i>Moodle</i> para cada usuario.
<br><br><b>Número de identificación de Moodle: </b> Valor del campo ID Number introducido en el perfil del usuario.
<br><br><b>Identificador personalizado: </b> Identificador creado por el administrador del módulo. Es el campo que aparece
 en el perfil de usuario con el nombre indicado.';
$string['username']           = 'Nombre de usuario';
$string['idnumber']           = 'Número de identificación de Moodle';
$string['userid']             = 'Id de usuario';
$string['byteacher']          = 'Longitud del campo personalizado';
$string['teammethod']         = 'Creación de los equipos';
$string['teammethod_help']         = ' Indique si se permite o no a los estudiantes que gestionen los grupos de personas en los que trabajarán 
para completar las actividades.';

$string['bystudents']         = 'Por los alumnos';
$string['byteacher']          = 'Por el profesor';
$string['bystudentswithleaders']= 'Por los alumnos con líder';
$string['defaultassignmentf'] = 'Tarea por defecto para creación de los equipos por los alumnos';
$string['any']                = 'Cualquiera';
$string['defaultnumteams']    = 'Numero de equipos por defecto';
$string['defaultnumteams_help']    = 'Fija el número máximo de equipos que podrán inscribirse en un grupo de trabajo.';
$string['defaultnummembers']  = 'Número de miembros por defecto';
$string['defaultnummembers_help'] = 'Fija el número máximo de alumnos que podrán inscribirse en un grupo de trabajo.';

$string['required']           = 'Requerido';
$string['userinfo']           = 'Id. personalizado: ';
$string['lengthuserinfo']     = 'Longitud del id. personalizado';
$string ['lengthuserinfo_help'] = 'En caso de que se haya seleccionado un identificador personalizado, este parámetro indica la longitud que debe tener.';
$string['numbercols']     	  = 'Número de columnas';
$string['numbercols_help']     	  = '<p> Indica el número de columnas en las que se dispondrán las preguntas en el cuestionario en formato PDF.';

$string['codebartype']        = 'Tipo de código de barras';
$string['codebartype_help']        = 'Elija el tipo de imagen que se usará para representar el código de identificación.';

//view.php
$string['mainpage'] = "Blended es un módulo que ayuda a realizar acividades en el aula.";
$string['mainpage_help'] = "Blended es un módulo que ayuda a realizar acividades en el aula.";

$string['labelspage']          = 'Generar página de etiquetas';
$string['labelsdesc']		   = '<b>$a</b> de identificación de los alumnos para pegar en los cuestionarios. <br>
								  Podrá configurar el número de etiquetas que desea generar por página así como seleccionar los alumnos para los que desea generar las etiquetas. <p align=center></p> ';
								  
$string['labelsGenerateStickersdesc']  = 'Imprimir páginas de pegatinas de identificación de los alumnos para pegar en los cuestionarios. <br>
								  Podrá configurar el número de etiquetas que desea generar por página así como seleccionar los alumnos para los que desea generar las etiquetas. <p align=center></p>';
								  
$string['blendedquizzes']	   = 'Cuestionarios Blended';

								
$string['blendedquizPrintPDFdesc']  = 'Crear un PDF con un grupo de ejemplares para imprimir a partir de un cuestionario de preguntas Moodle.';
$string['blendedquizUploadScansdesc']  = 'Cargar y seleccionar un archivo PDF o ZIP de imágenes escaneadas para su procesamiento automático a través del detector de marcas.';
$string['blendedquizReviewScansdesc']  = 'Verificar, corregir y pasar las respuestas de cada cuestionario a Moodle.';
$string['blendedquizReviewResultsdesc']  = 'Comprobar la corrección y la calificación de un test. (Sólo para los alumnos).';


$string['management']		   = 'Gestión de equipos y de tareas';
$string['managementGenerateTaskSheet']	   = 'Esta sección le permitirá generar un documento para responder a una entrega de Moodle.';
$string['managementTeamGrading'] = 'Introducir calificaciones para una tarea seleccionada. Permite calificar por equipos.';
$string['managementTeamCreation'] ='Permite la creación de equipos para cada tarea, de forma manual o aleatoria.';
								
$string['studentOptions']			   = "Opciones para los alumnos.";

$string['studentlabelsGenerateStickers'] = 'Pegatinas de identificación para pegar en los cuestionarios.
								  Podrá configurar el número de etiquetas que desea generar por página.';
$string['studentmanagementGenerateTaskSheet'] = 'Si una entrega se realizará en clase puedes obtener aquí un documento formateado para facilitar la entrega de Moodle.';

$string['studentJoinAGroup']	= 'Unirse a un grupo de trabajo para resolver una tarea.'; 
$string['studentReviewResults']	= 'Comprobar la corrección y la calificación de un test. Si observa errores de marcado en las respuestas o de evaluación, contacte con su profesor.';
	
$string['assignmentpage']      = 'Generar página de tarea';
$string['gradepage']           = 'Calificar tareas';
$string['introgradepage']      = 'Introducir calificaciones';
$string['teamsmanagementpage'] = 'Administración de equipos';
$string['signupteampage']      = 'Inscribirse en equipo';
$string['edituserinfo']        = 'Para acceder deber introducir su DNI en el campo - Numero de id. - de su perfil';
$string['edituserinfo2']       = 'Para acceder deber introducir el identificador personalizado requerido en su perfil';
$string['nostudentsincourse']  = 'Vínculos desactivados:<br><br>! No se puede acceder porque no hay estudiantes en el curso ¡';
$string['noneisactive']        = '¡ No hay ningún estudiante activo en el curso !';
$string['studentisnotactive']  = '¡ Hay estudiantes en el curso que no están activos !';
$string['noidnumberview']      = '¡ Hay estudiantes en el curso que no tienen asignado el código de identificación !';
$string['nouserinfodataview']  = '¡ Hay estudiantes en el curso que no han introducido su ID. PERSONALIZADO !';
$string['generatepaperquiz']   = 'Generar tanda de cuestionarios en papel';
$string['scan'] 			   = 'Procesar cuestionarios escaneados';
$string['correction'] 		   = 'Supervisar el estado de la corrección';
$string['revision'] 		   = 'Revisión de los test';
$string['header1'] 		  	   = 'Módulo de gestión de etiquetas y creacion de grupos';
$string['header2'] 		   	   = 'Módulo para integración de cuestionarios en papel';
$string['scannedJob']	    	  = 'Hojas Digitalizadas';
$string['imagepage']	    	  = 'Ver Imagen';
$string['reviewdetails']	      = 'Ver Cuestionario';
$string['showdetailspage']	      = 'Calificaciones';
$string['evaluatepage']	      = 'Evaluación';
$string['deletescanjob']		= 'Borrar Trabajo';
$string['deletequiz']			= 'Borrar Cuestionario';
$string['jobstatus'] 			= 'Estado del trabajo';
$string['statusdetails']		= 'Informe detallado';
$string['viewalerts']		= 'Mensajes de alerta';
$string['finished']			= 'Terminado';
$string['error']			= 'Con error';
$string['waiting']			= '<img src=\"images/ajax-loader.gif\" width=\"16\"/>En espera';
$string['busy']				= '<img src=\"images/ajax-loader.gif\" width=\"16\"/>En proceso';

//Descripciones de pagina
$string['assignmentpagedescr']     = 'Seleccione la hoja de tarea que desee imprimir ';
$string['assignmentpagedescr2']    = 'Hoja de tarea: ';
$string['teamsmanagementpagedesc'] = 'Permite crear equipos para cada una de las tareas de forma manual o aleatoria';
$string['gradepagedescr']          = 'Seleccione la tarea que desee calificar.';
$string['signupteampagedesc']      = 'Inscribase en un equipo para dicha tarea. Sólo el que crea el equipo puede eliminar miembros del mismo.';
$string['scanpagedesc']            = 'Suba y/o seleccione el archivo de imágenes que desea procesar. 
<BR> El archivo de imágenes debe ser un documento PDF o bien un archivo ZIP con imágenes en formato JPG o TIF.
<BR> Los trabajos serán procesados automáticamente por Moodle. Este proceso puede durar algunos minutos. 
Puede revisar los resultados en la sección \"Supervisar el estado de la corrección\" o bien haciendo clic en el enlace Ver en aquellos trabajos que se encuentren terminados.';
$string['correctionpagedesc']      = 'Seleccione el trabajo que desea ver en detalle.';
$string['imagepagedesc']      	   = 'Detalle de la imagen seleccionada';
$string['scannedjobpagedesc']      = 'Resultados correspondientes al trabajo <a href=\"$a->href\">$a->hrefText</a>';
$string['revisionpagedesc']        = 'En la siguiente tabla se muestran los cuestionarios realizados en el curso.';
$string['evaluatepagedesc']        = 'Su cuestionario ha sido evaluado. Puede verificar los resultados en el informe <a href=\"$a->href\">$a->hrefText</a>.
									  <BR>Si la nota no se actualiza correctamente, pulse el enlace recalificar que encontrará en aquella página.';
$string['showdetailspagedesc']     = 'Resultados del cuestionario número $a->acode perteneciente al trabajo <a href=\"$a->href\">$a->hrefText</a>';
$string['reviewdetailspagedesc']     = 'Detalles del cuestionario seleccionado';

$string['deletescanjobdesc'] 		= 'ATENCIÓN: Está a punto de eliminar los resultados del reconocimiento automático obtenidos de $a->jobname. 
										Si elimina el trabajo también quedarán eliminados los resultados obtenidos para los cuestionarios 
										y las imágenes generadas durante su procesado, así como los resultados de evaluación obtenidos.
										<BR><BR>
										No se borrarán los PDF de los enunciados ni los ficheros escaneados que usted haya subido.
										<BR><BR> Si está seguro de que desea eliminar los resultados de este trabajo de reconocimiento en su totalidad pulse borrar. ';

$string['deletequizdesc'] 		= 'ATENCIÓN: Está a punto de eliminar el cuestionario número $a->acode. Si elimina el cuestionario se perderán todos los datos correspondientes a su 
										corrección y evaluación. <BR><BR> Si está seguro de que desea eliminar el cuestionario, pulse borrar.';

$string['deleteimgdesc'] 		= 'ATENCIÓN: Está a punto de eliminar la imagen $a->imgout. La imagen podría pertenecer a un cuestionario que desea evaluar. <BR><BR> Si está seguro de que desea eliminar la imagen, pulse borrar.';

$string['jobstatusdesc'] = 'Estado del trabajo seleccionado. Para ver los detalles pulse el enlace correspondiente.<BR> (Sólo administradores o usuarios con permisos)';
$string['viewalertsdesc'] 	= 'Información sobre los mensajes de alerta';
$string['undetectedActivitycodepagedesc'] 	= 'Cuestionario con código de identificación no detectado o erróneo: El escáner no ha podido identificar el código identificador de este ejemplar por lo que no se han podido encontrar las marcas. Introduzca el código que observa en la imagen y en la siguiente página complete manualmente las marcas observadas.';

//Labelspage.php
$string['labelspagedescr1']      = 'Seleccione el alumno y número de etiquetas por fila y por columna que desee imprimir';
$string['labelspagedescr2']      = 'Seleccione el número de etiquetas por fila y por columna que desee imprimir';
$string['numrows']               = 'Número de filas: ';
$string['numcolumns']            = 'Número de columnas: ';
$string['noselected']            = 'Selecciona';
$string['noactiveuser']          = '¡ Con (*) los estudiantes no activos en el curso !';
$string['noidnumber']            = '¡ Con (**) los estudiantes en el curso que no tienen asignado el código de identificación !';
$string['nouserinfodata']        = '¡ Con (#) los estudiantes en el curso que no han introducido su ID. PERSONALIZADO !';
$string['printlabels']           = 'Imprimir etiquetas';
$string['printlabelscourse']           = 'Imprimir etiquetas del curso';
$string['cantprintlabel']        = ' Hasta que el estudiante posea código de identificación no se puede imprimir su etiqueta';
$string['cantprintlabel2']       = ' Hasta que el estudiante no introduzca su ID. PERSONALIZADO no se puede imprimir su etiqueta';
$string['numrowsnotselected']    = 'No ha seleccionado el numero de etiquetas por fila';
$string['numcolumnsnotselected'] = 'No ha seleccionado el numero de etiquetas por columna';
$string['pageformat']			 = 'Formato de Página';
$string['margin_top_mm']		 = 'Margen superior mm. (Mídalo cuidadosamente en su hoja de pegatinas)';
$string['margin_bottom_mm']		 = 'Margen inferior mm.(Mídalo cuidadosamente en su hoja de pegatinas)';
$string['margin_right_mm']		 = 'Margen derecho mm. (Mídalo cuidadosamente en su hoja de pegatinas).';
$string['margin_left_mm']		 = 'Margen izquierdo mm. (Mídalo cuidadosamente en su hoja de pegatinas).';
$string['printforone']			 = 'Imprimir para un alumno';
$string['layoutmethod']			 = 'Métodos de disposición de las etiquetas';
$string['oneforeachactive']		 = 'Una etiqueta por cada estudiante activo en el curso.';
$string['oneforeachenrolled']	 = 'Una etiqueta por cada estudiante matriculado en el curso.';
$string['fullpages']			 = 'Páginas completas de etiquetas para los estudiantes seleccionados en la lista de la izquierda.';
$string['labelsformat']			 = 'Formato de las etiquetas';
$string['identifyforhumans']	 = 'Identificar las etiquetas para que sean legibles por humanos';
$string['donotidentify']		 = 'No identificar';
$string['showreadableid']		 = 'Mostrar código ID legible';
$string['showfullname']			 = 'Mostrar nombre completo del usuario';

//Assignmentpage.php
$string['assignments']              = 'Selecciona la tarea: ';
$string['defaultassignment']        = 'Tarea: ';
$string['user']                     = 'Alumno: ';
$string['assignment']               = 'Tarea (si no sale en la lista): ';
$string['printassignmentpage']      = 'Imprimir pagina de tarea';
$string['assignmentnotselected1']   = 'No ha selecionado la tarea de la lista ni ha introducido un nombre de tarea.';
$string['usernotselected']          = 'No ha selecionado un alumno de la lista.';
$string['noassignments']            = 'No hay tareas en el curso';
$string['cantprintassignmentpage']  = ' Hasta que el estudiante posea el código de identificación no se puede imprimir su página de tarea';
$string['cantprintassignmentpage2'] = ' Hasta que el estudiante no introduzca su ID. PERSONALIZADO no se puede imprimir su página de tarea';
$string['noidnumber2']              = '¡ No posee código de identificación !';

//Grades.php
$string['assignmentnotselected2'] = 'No ha selecionado la tarea a calificar de la lista.';
$string['gradeassignments']       = 'Tarea a calificar:';
$string['teamassignment']         = 'Tarea de donde extraer los equipos:';
$string['numteams']               = 'Número de equipos:';
$string['nummembers']             = 'Número de miembros por equipo:';
$string['gradeassignment']        = 'Calificar tarea';

//Introgrades.php
$string['search']               = 'Buscar identificador';
$string['idteam']               = 'Equipo:';
$string['idmembers']            = 'Identificadores:';
$string['grade']                = 'Calificación:';
$string['nograded']             = '-';
$string['sendgrades']           = 'Guardar calificaciones';
$string['teamsfromassignment']  = 'Equipos creados anteriormente para la tarea: ';
$string['rewritegrades']        = '<center>¿Sobrescribir?<br>Calificación individual<br>distinta a la de grupo.</center>';
$string['confirmrewritegrades'] = '¿Está seguro de que desea sobreescribir la calificación individual distinta a la de grupo?';
$string['checkbox']             = 'Deseleccione el checkbox correspondiente.';
$string['existinglinkedteams']  = 'Existen equipos definidos para esta tarea ya vinculados a otra tarea.<br>Esta tarea no se puede vincular a la tarea: ';
$string['existingteams']        = 'Existen equipos definidos para esta tarea <br> No se pueden utilizar en esta tarea los equipos definidos anteriormente para la tarea:  ';
$string['alertgrade']='La calificación individual es distinta a la grupal, si cambia la grupal sobreescribirá la individual';

//Save.php
$string['save']       = 'Guardar';
$string['inserted']   = '<center>Operación realizada con exito</center>';
$string['noinserted'] = '<center>No se ha realizado ninguna operación</center>';

//Printassignment.php
$string['AssignmentName']       = 'Nombre de tarea';
$string['TimeDue']       = 'Tiempo permitido';
$string['StudentName']       = 'Nombre del alumno';
$string['CourseName']       = 'Nombre del curso';


//Teamsmanagement.php
$string['creationmethod']    = 'Método de creación de equipos: ';
$string['byhand']            = 'Manual';
$string['randomly']          = 'Aleatorio';
$string['name']              = 'Nombre de tarea';
$string['assignmenttype']    = 'Tipo de tarea';
$string['duedate']           = 'Fecha limite de entrega';
$string['teams']             = 'Número de equipos';
$string['graded']            = 'Calificada';
$string['createteams']       = 'Inicializar equipos';
$string['createteams2']      = 'Modificar equipos';
$string['rewriteteams']      = 'Eliminar vinculación <br> y crear equipos';
$string['confirmrewrite']    = '¿Esta seguro de que desea eliminar la vinculación con la tarea y crear nuevos equipos?';
$string['linked']            = '  vinculado a tarea:  ';
$string['linked2']           = '  vinculados a tarea:  ';
$string['no']                = 'No';
$string['yes']               = 'Si';
$string['partially']         = 'Parcialmente';
$string['studentsselection'] = 'Selección de estudiantes:';
$string['activestudents']    = 'Solo activos';
$string['allstudents']       = 'Todos';

//Introteams.php
$string['gradeit']             = 'Calificar equipos';
$string['introteams']          = 'Gestión de equipos para ';
$string['sendteams']           = 'Guardar equipos';
$string['withoutidnumber']     = '<br>Sin Número de id.';
$string['withoutuserinfodata'] = '<br>Sin Id. personalizado.';
$string['select_grouping']			   = 'Elija el agrupamiento: ';

//Signupteam.php
$string['teamscount']             = 'Equipos';
$string['teammembers']            = 'Miembros de equipo';
$string['membercount']            = 'Número de miembros';
$string['newteam']                = 'Crear nuevo equipo';
$string['nameteam']               = '<center>Nombre de equipo<br>(opcional)</center>';
$string['signupteampage2']        = 'Equipos para la tarea: ';
$string['deletemember']           = 'Eliminar miembros';
$string['selectassignpage']       = 'Inscribirse en equipo';
$string['signupteam']             = 'Inscribirse en equipo';
$string['assignmentnotselected3'] = 'No ha selecionado la tarea para inscribirse en un equipo';
$string['strnosigned']			  = 'No puede inscribirse en esta tarea hasta que el profesor 
									le asigne un agrupamiento';
//edit_paperquiz.php
$string['paperquiz'] 			  = 'Generar cuestionarios en papel';
$string['paperquizdescr']		  = 'Seleccione el cuestionario del que desea generar un número de ejemplares.
    								 Se generará un fichero PDF en el directorio raiz de ficheros de su curso con los documentos  generados. ';
$string['paperquizformat']	      = 'Formato de PaperQuiz';
$string['selectquiz']	    	  = 'Seleccione el cuestionario';
$string['numquiz']	    	  	  = 'Número de ejemplares';
$string['later']	    	  	  = 'Generar tests más tarde';
$string['labelformat']	    	  = 'Formato de las etiquetas';
$string['identify']	    	 	  = 'Identificar las etiquetas para los humanos';
$string['notidentify']	    	  = 'No identificar';
$string['readable']	    	  	  = 'Mostrar código legible para los humanos';
$string['table']	    	  	  = '';
$string['noquizzes']			  = 'No hay cuestionarios creados en el curso.';
$string['noquizzes_help']			  = 'Para poder crear un documento en PDF, primero necesita crear un cuestionario de preguntas en su curso de
<i>Moodle</i>.
<br><br> La creación de cuestionarios la puede realizar en la página principal del curso, en la sección
<i>Agregar actividad...</i> -- <i>Cuestionario</i>, siempre que cuente con los permisos necesarios para poder realizar esta
acción.';

$string['logofile']				  = 'Seleccione el logotipo de los cuestionarios.';
$string['fontsize']					= 'Tamaño de la tipografía.';
$string['smallfont']					= 'Pequeño.';
$string['midsizefont']					= 'Medio.';
$string['largefont']					= 'Grande.';
$string['onecolumn']			= 'Una columna';
$string['twocolumns']			= 'Dos columnas';
$string['howToMarkInstructions']= 'Marque cuidadosamente: Bien: $a->ok_marks Mal: $a->ko_marks';
$string['viewPDF']				= 'Ver el PDF';
//scan.php, correction.php, scannedJob.php
$string['resultlink']	    	  = 'Resultados';
$string['ScannedFolderName']	  = 'Scans';
$string['templatecode']			  = 'Identificador de página';
$string['activitycode']			  = 'Identificador de cuestionario';
$string['activitycodeRemoveLastDigit'] = 'Introduzca todos los dígitos de la etiqueta salvo el último que indica el número de página'.
$string['idlabel']			 	  = 'USERID';	
$string['accept']				  = 'Aceptar'; 
$string['processcancelled']		  = '<CENTER>El proceso ha sido cancelado.</CENTER><BR>';	
$string['notclosed']				= 'No realizado aún.';
$string['blendedPassToQuiz']	= 'Pasar a QUIZ';
$string['blendedPassAgainToQuiz']	= 'Pasar de nuevo a QUIZ';
$string['blendedPassSelectedToQuiz']	= 'Pasar a QUIZ los seleccionados';
$string['launchJob']				= 'Lanzar proceso.';
$string['NotYet']				=	'Aún no.';
$string['blendedPassedToQuiz']		= 'Pasado';
$string['UnclassifiedPage']			= 'No identificada';
$string['MarkWarning']			= 'Marca dudosa o con problemas.';
$string['unmarked']				= 'Ninguno';
$string['errors_resolved']		= 'Dudas resueltas';
$string['page']					= 'Página';
$string['pages']					= 'Páginas';
$string['pluginname'] = 'Blended';

//help files
$string['EVAL']				  = 'Campos Eval'; 
$string['PREGUNTA']				  = 'Preguntas'; 
$string['SCANJOB']				  = 'Trabajo de Escaneo'; 
$string['SCANJOB_help']				  = '<h1>Elegir o subir un archivo</h1>
<p>El archivo elegido debe ser un documento en formato PDF o bien un archivo ZIP con imágenes en formato JPG o TIFF.
<br><br>Si sube el archivo, recuerde situarlo en la carpeta donde se encuentran los archivos <i>fieldset</i> para su correcto procesado.
</body>';
$string['defaultassignment_help']= 'Ayuda con esta tarea';
$string['pagehelp']			   = 'Ayuda con esta página';
$string['Student']			= 'Estudiante';
$string['pluginadministration'] = 'Blended administration';

// Mensajes de error
$string['alert_error_1']  = '- Los estudiantes con identificadores:  ';
$string['alert_error_2']  = '- El estudiante con identificador:  ';
$string['alert_error_3']  = '  introducido$a en el equipo: ';
$string['alert_error_4']  = '<br>Se ha encontrado un problema al buscar:<br><br> $a <br>';
$string['alert_error_5']  = '<br>No están matriculados en el curso:<br><br> $a <br>';
$string['alert_error_6']  = '<br>No está matriculado en el curso:<br><br> $a <br>';
$string['alert_error_7']  = '<br>Ya están inscritos en otro equipo/s para esta tarea:<br><br> $a <br>';
$string['alert_error_8']  = '<br>Ya está inscrito en otro equipo para esta tarea:<br><br> $a <br>';
$string['alert_error_9']  = '<br><br>Estos estudiantes no han sido insertados.';
$string['alert_error_10'] = '<br><br>Este estudiante no ha sido insertado.';

$string['PDFgeneratedMessage']="<p>PDF file generated. You can find it in the Files section of your course, under the ". 
								'<a href=\"$a->href\">$a->hrefText</a> directory.</p>'.
								'<p>Direct download link: <a href=\"$a->directLinkhref\">$a->directLinkText</a></p>';
$string['ErrorUserIDEmpty']='El campo USERID está vacío. Debe rellenar este campo en el formulario de edición del cuestionario.';
$string['ErrorUserNotInCourse']='No existe dicho usuario en este curso.';
$string['ErrorCouldNotCreateAttempt']='No se pudo crear un intento nuevo.';
$string['ErrorActivityCodeNotFound']='No existe ningún cuestionario con número $a registrado en '.$string['modulename'].'. Esta situación impide que esta prueba pueda procesarse. Deberá evaluarse manualmente sobre el papel.';
$string['ErrorQuizNotFound']='No existe ningún quiz con identificación: $a en la tabla quiz.';
$string['ErrorScannedImageNotFound']= 'No hay ninguna imagen o resultado para mostrar en este trabajo de escaneo.';
?>