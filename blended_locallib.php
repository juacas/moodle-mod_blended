<?php

/* 
 * Copyright (C) 2015 juacas
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * Find the list of users and get a list with the ids of students and a list of non-students
 * @param type $context_course
 * @return array(array($studentIds), array($non_studentIds), array($activeids), array($user_records))
 */
function blended_get_users_by_type($context_course) {
    // Get users with gradable roles
    global $CFG;
    $gradable_roles = $CFG->gradebookroles;
    $roles = explode(',', $gradable_roles);
    $students = array();
    foreach ($roles as $roleid) {
        $users_in_role = get_role_users($roleid, $context_course);
        $ids = array_keys($users_in_role);
        $students = array_merge($students, $ids);
        $students = array_unique($students);
    }
    // get enrolled users
    $user_records = get_enrolled_users($context_course, '', 0, '*');
    $users = array_keys($user_records);
    $non_students = array_diff($users, $students);
    // select active userids
    $activeids = array();
    global $DB;
    list($select, $params) = $DB->get_in_or_equal($students);
    $select = "userid $select";
    $select.= " AND courseid = ?";
    $params[] = (int) $context_course->instanceid;
    $last_accesses = $DB->get_records_select('user_lastaccess', $select, $params);
    foreach ($last_accesses as $record) {
        $activeids[] = $record->userid;
    }
    return array($students, $non_students, $activeids, $user_records);
}


/* * ******************************************************************************
 * Genera el c�digo num�rico del identificador del estudiante a partir de los
 * parametros especificados en la instancia del modulo.
 * No aplica filtrado ni codificaci�n.
 * Selecciona el tipo de identificador (user->id, user->idnumber,
 * user_info_data->data)
 *
 * En caso de utilizar como
 * identificador el user->idnumber o user_info_data se asegura de quitarle el gui�n y la letra.
 *
 * @see blended_clean_idnumber()
 * @see blended_gen_ean()
 *
 * @param object $user el objeto de la tabla "user" perteneciente al estudiante
 * @param object $blended objeto de la instancia del modulo blended
 * @return string|int c�digo del identificador del estudiante o -1 si error
 *                    debido a user->idnumber no introducido
 * ****************************************************************************** */
function blended_gen_cleanidvalue($user, $blended) {
    global $DB;

    // Tipo de identificador -----------------------------------------------------
    switch (substr($blended->idtype, 0, 1)) {

        // `idnumber` tabla "user"
        case "i": // idnumber

            if (!empty($user->idnumber))
                $code = $user->idnumber;
            else
                return -1;

            break;

        // Si no se ha introducido el `idnumber` finalizamos pues lo necesitamos para el c�digo

        case "u": //userid

            $code = $user->id;
            break;
        // `data` tabla "user_info_data"

        case "2": // customfield encoded as 2fieldname
            $fieldid = intval(substr($blended->idtype, 1));

            if (!$code = $DB->get_field('user_info_data', 'data', array('userid' => $user->id, 'fieldid' => (int) $fieldid))) {
                return -2;
                break;
            }
            break;
    }
    //Limpia guion y letra final
    $code = blended_clean_idnumber($code);
    return $code;
}

/* * ******************************************************************
 * Genera el c�digo del identificador del estudiante a partir de los
 * parametros especificados en la instancia del modulo.
 * ***************************************************************** */

function blended_gen_idvalue($user, $blended) {

    $ean = blended_gen_cleanidvalue($user, $blended);
    return $ean;
}

/* * ******************************************************************************
 * Elimina del user->idnumber (DNI) el guión y la letra pues en el código EAN
 * solo deben figurar digitos.
 *
 * @param string $ean el user->idnumber
 * @return string el user->idnumber sin guión ni letra
 * ****************************************************************************** */

function blended_clean_idnumber($ean) {

    // Para limpiar el guion del DNI
    if ((substr($ean, -2, 1) == '-') && !ctype_digit(substr($ean, -1, 1))) {
        $ean = substr($ean, 0, strlen($ean) - 2);
    }

    // Para limpiar la letra del DNI
    if (ctype_digit(substr($ean, -2, 1)) && !ctype_digit(substr($ean, -1, 1))) {
        $ean = substr($ean, 0, strlen($ean) - 1);
    }

    return $ean;
}

/* * ******************************************************************************
 * Completa la generación el código EAN de 13 digitos: recibe el identificador
 * del estudiante (codificado o en texto plano), le añade ceros a la izquierda
 * hasta 12 digitos y finalmente le añade a la derecha el checksum de todos los
 * digitos
 *
 * @see blended_barcode_gen_ean_sum()
 *
 * @param string $ean identificador de estudiante códificado o plano
 * @return string código EAN-13 del identificador de estudiante
 * ****************************************************************************** */

function blended_gen_ean($ean) {

    $ean = str_pad($ean, 12, "0", STR_PAD_LEFT);
    $eansum = blended_barcode_gen_ean_sum($ean);
    $ean.= $eansum;

    return $ean;
}

/* * ******************************************************************************
 * Calcula el checksum de los 12 digitos significativos del códgio EAN del
 * identificador del estudiante
 *
 * @param string $ean 12 digitos del identificador de estudiante códificado o
 *                    plano con ceros delante
 * @return  int el checksum de los 12 digitos
 * ****************************************************************************** */

function blended_barcode_gen_ean_sum($ean) {
    $even = true;
    $esum = 0;
    $osum = 0;
    for ($i = strlen($ean) - 1; $i >= 0; $i--) {
        if ($even)
            $esum+=$ean[$i];
        else
            $osum+=$ean[$i];
        $even = !$even;
    }
    return (10 - ((3 * $esum + $osum) % 10)) % 10;
}

/* * ******************************************************************************
 * Obtenemos el identificador del estudiante (user->id, user->idnumber,
 * user_info_data->data) a partir del código EAN de 13 digitos.
 *
 * Primero elimina el último digito de checksum para obtener los 12 digitos
 * significativos del código EAN. Si se han añadido ceros a la izquierda se
 * eliminan. Si se ha codificado el identificador mediante una operación XOR
 * se vuelve a realizar la operación XOR con la misma clave (operación
 * idempotente: dos aplicaciones sucesivas producen el mismo valor). Y se
 * devuelve el identificador de estudiante limpio.
 *
 * @param $id_member código (plano o codificado, sin formato o EAN-13) del identificador del estudiante
 * @param object $blended objeto de la instancia del modulo blended
 * @return string identificador de estudiante
 * ****************************************************************************** */

function blended_get_idvalue($id_member, $blended) {

    $id_member = blended_remove_checksums($id_member, $blended);

    // Eliminamos los ceros a la izquierda
    $id_member = trim($id_member);
    $id_member = trim($id_member . '*', '0'); // introduzco un * para evitar quitar ceros a la derecha
    $id_member = substr($id_member, 0, strlen($id_member) - 1);

    //$id_member = (string)(int)$id_member;
    // Codificado
    if ($blended->idmethod == 0) {

        // Obtenemos la clave aleatoria
        $key = (int) $blended->randomkey;
        // Decodificamos
        $value = $id_member ^ $key;
    } else
    // Plano
    if ($blended->idmethod == 1) {

        // Eliminamos el checksum del codigo EAN
        $id_member = substr($id_member, 0, 12);
        // Eliminamos los ceros a la izquierda
        //$id_member=intval($id_member);
        $value = $id_member;
    }
    /*
      if ($blended->idtype == "1"){

      // Para idnumber de 7 digitos añadimos un cero delante
      $value = str_pad($value, 8, "0", STR_PAD_LEFT);

      }
      else if(substr($blended->idtype,0,1)=="2"){

      // Añadimos los ceros delante que sean necesarios
      $value = str_pad($value, $blended->lengthuserinfo, "0", STR_PAD_LEFT);
      }
     */
    return $value;
}

/**
 * Remove checksums from code if needed
 * @param $id_member
 * @param $blended
 * @return unknown_type
 */
function blended_remove_checksums($id_member, $blended) {
    // Eliminamos el checksum del codigo EAN
    if ($blended->codebartype = "EAN13" && strlen($id_member) == 13)
        $id_member = substr($id_member, -strlen($id_member), -1);
    return $id_member;
}

/********************************************************************************
 * Print an id label for an user according to the policy selected
 * TODO: Document this function!!
 * Codebartypes:
 * *  <ul><li>DATAMATRIX : Datamatrix (ISO/IEC 16022)</li>
 * <li>PDF417 : PDF417 (ISO/IEC 15438:2006)</li><li>PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6 : PDF417 with parameters: a = aspect ratio (width/height); e = error correction level (0-8); t = total number of macro segments; s = macro segment index (0-99998); f = file ID; o0 = File Name (text); o1 = Segment Count (numeric); o2 = Time Stamp (numeric); o3 = Sender (text); o4 = Addressee (text); o5 = File Size (numeric); o6 = Checksum (numeric). NOTES: Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional. To use a comma character ',' on text options, replace it with the character 255: "\xff".</li>
 * <li>QRCODE : QRcode Low error correction</li>
 * <li>QRCODE,L : QRcode Low error correction</li>
 * <li>QRCODE,M : QRcode Medium error correction</li>
 * <li>QRCODE,Q : QRcode Better error correction</li>
 * <li>QRCODE,H : QR-CODE Best error correction</li>
 * <li>RAW: raw mode - comma-separad list of array rows</li>
 * <li>RAW2: raw mode - array rows are surrounded by square parenthesis.</li>
 * <li>TEST : Test matrix</li></ul>
 *********************************************************************************/
function blended_print_student_label($pdf, $blended, $codebartype, $style, $identifyLabels, $user, $columnsWidth, $rowsHeight) {

    $x = $pdf->getX();
    $y = $pdf->getY();
    $align = '';
    $code = blended_gen_idvalue($user, $blended);
// 	if($identifyLabels!='none')
    $identifyHeight = 3;
// 	else
// 	$identifyHeight=0;

    switch ($identifyLabels) {
        case 'fullname': $idText = fullname($user)/* .'<br>'.$code */;
            break;
        case 'id': $idText = $code;
            break;
        //blended_gen_cleanidvalue($user,$blended);break; // cleanidvalue do not include checksum needed for EAN13
        case 'none': $idText = '';
            break;
        case 'code': $idText = "ID:$code";
    }

    if ($code == -1 || $code == -2) {
        $pdf->writeHTMLCell($columnsWidth, $rowsHeight - $identifyHeight, '', '', fullname($user) . ": No posee código de identificación.", 0, 2, 0, true, 'C');
    } else {
        switch ($codebartype) {
            case 'EAN13':

                if ($identifyLabels != 'none' && $identifyLabels != 'code') {
                    $pdf->Cell($columnsWidth, $identifyHeight, $idText, '', 2, 'L', 0, '', 0);
                }

                $pdf->SetXY($x, $identifyHeight + $y);
                $pdf->write1DBarcode($code, $codebartype, '', //$margins['left']+$c*$columnsWidth, 
                        '', //$r*$rowsHeight, 
                        $columnsWidth, $rowsHeight - $identifyHeight, 0.2, $style, $align);
            default:

                $dim = min($columnsWidth, $rowsHeight);

                if ($identifyLabels != 'none') {
                    //	$pdf->SetXY($x+$dim,$identifyHeight+$y);
                    $pdf->writeHTMLCell($columnsWidth - $dim, $rowsHeight, '', '', $idText, 0, 0);
                } else {
                    $pdf->writeHTMLCell($columnsWidth - $dim, $rowsHeight, '', '', "ID not shown.", 0, 0);
                }
                $pdf->SetXY($x + $columnsWidth - $dim, $y);
                $pdf->write2DBarcode($code, $codebartype, $x + $columnsWidth - $dim + 2, $y + 2, $dim - 4, $dim - 4);
//				$pdf->writeBlended2DBarcode($code,'QRCode,Q',$x+$columnsWidth-$dim,$y,$dim,$dim);
                break;
        }
    }
    $pdf->Rect($x, $y, $columnsWidth, $rowsHeight);
}
