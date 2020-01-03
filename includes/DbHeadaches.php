<?php

class DbHeadaches
{

    // Variable que almacena la conexión
    private $conn;

    // Constructor
    function __construct()
    {
        include_once dirname(__FILE__) . '/DbConnect.php';
        // Creamos un objeto de DbConnect
        $db = new DbConnect;

        // Llamamos a la funcion connect para conectar con la base de datos
        $this->conn = $db->connect();
    }

    // CRUD Functions

    /********** CREATE **********/

    /**
     * Funcion que crea e inserta un ataque en la tabla headaches
     * CreateHeadache
     */
    public function createCrisis($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale)
    {
        // Primero comprobamos que el patient_id es correcto
        if ($this->isPatientIdValid($patient_id)) {
            // Si el id es correcto, preparamos la consulta
            $stmt = $this->conn->prepare("INSERT INTO headaches (patient_id, start_datetime, end_datetime, sport, alcohol, smoke, medication, feeling, pain_scale) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ");
            // Bindeamos los parámetros
            $stmt->bind_param("ssssssssi", $patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
            // Comprobamos si se ha ejecutado correctamente
            if ($stmt->execute()) {
                // Si se ejecuta correctamente devolvemos HEADACHE_CREATED
                return HEADACHE_CREATED;
            } else {
                // Si no se ejecuta devolvemos HEADACHE_FAILURE
                return HEADACHE_FAILURE;
            }
        } else {
            // Si el id no es válido devolvemos PATIENTID_NOT_VALID
            return PATIENTID_NOT_VALID;
        }
    }

    /**
     * Función que comprueba que el patient_id existe y está en la tabla patients
     */
    private function isPatientIdValid($patient_id)
    {
        // Preparamos la consulta
        $stmt = $this->conn->prepare("SELECT patient_id FROM patients_login WHERE patient_id = ?");
        // Bindeamos los parámetros
        $stmt->bind_param('s', $patient_id);
        // Ejecutamos la consulta
        $stmt->execute();
        // Guardamos el resultado
        $stmt->store_result();
        //var_dump($stmt->affected_rows);
        // Devolvemos cuando el numero de filas que da como resultado es mayor que 0
        return $stmt->num_rows >= 1;
    }

    /********** READ, READBY & READALL **********/

    public function readAllCrisis()
    {
        $stmt = $this->conn->prepare("SELECT patient_id, start_datetime, end_datetime, sport, alcohol, smoke, medication, feeling, pain_scale FROM headaches");
        $stmt->execute();
        $stmt->bind_result($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        $headaches = array();
        while ($stmt->fetch()) {
            $headache = array();
            $headache['patient_id'] = $patient_id;
            $headache['start_datetime'] = $start_datetime;
            $headache['end_datetime'] = $end_datetime;
            $headache['sport'] = $sport;
            $headache['alcohol'] = $alcohol;
            $headache['smoke'] = $smoke;
            $headache['medication'] = $medication;
            $headache['feeling'] = $feeling;
            $headache['pain_scale'] = $pain_scale;
            array_push($headaches, $headache);
        }
        return $headaches;
    }

    public function readAllCrisisByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT h.patient_id, h.start_datetime, h.end_datetime, h.sport, h.alcohol, h.smoke, h.medication, h.feeling, h.pain_scale FROM headaches h JOIN patients_login p ON h.patient_id = p.patient_id WHERE p.email = ? ORDER BY start_datetime DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        $headaches = array();
        while ($stmt->fetch()) {
            $headache = array();
            $headache['patient_id'] = $patient_id;
            $headache['start_datetime'] = $start_datetime;
            $headache['end_datetime'] = $end_datetime;
            $headache['sport'] = $sport;
            $headache['alcohol'] = $alcohol;
            $headache['smoke'] = $smoke;
            $headache['medication'] = $medication;
            $headache['feeling'] = $feeling;
            $headache['pain_scale'] = $pain_scale;
            array_push($headaches, $headache);
        }
        return $headaches;
    }

    public function readAllCrisisById($patient_id)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, start_datetime, end_datetime, sport, alcohol, smoke, medication, feeling, pain_scale FROM headaches WHERE patient_id = ? ORDER BY start_datetime DESC");
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $stmt->bind_result($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        $headaches = array();
        while ($stmt->fetch()) {
            $headache = array();
            $headache['patient_id'] = $patient_id;
            $headache['start_datetime'] = $start_datetime;
            $headache['end_datetime'] = $end_datetime;
            $headache['sport'] = $sport;
            $headache['alcohol'] = $alcohol;
            $headache['smoke'] = $smoke;
            $headache['medication'] = $medication;
            $headache['feeling'] = $feeling;
            $headache['pain_scale'] = $pain_scale;
            array_push($headaches, $headache);
        }
        return $headaches;
    }

    public function readCrisisByDate($patient_id, $date)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, start_datetime, end_datetime, sport, alcohol, smoke, medication, feeling, pain_scale FROM headaches WHERE patient_id = ? AND ? BETWEEN start_datetime AND end_datetime");
        $stmt->bind_param("ss", $patient_id, $date);
        $stmt->execute();
        $stmt->bind_result($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        if ($stmt->fetch()) {
            $headache = array();
            $headache['patient_id'] = $patient_id;
            $headache['start_datetime'] = $start_datetime;
            $headache['end_datetime'] = $end_datetime;
            $headache['sport'] = $sport;
            $headache['alcohol'] = $alcohol;
            $headache['smoke'] = $smoke;
            $headache['medication'] = $medication;
            $headache['feeling'] = $feeling;
            $headache['pain_scale'] = $pain_scale;
            return $headache;
        } else {
            return null;
        }
    }

    public function readAllActiveCrisis()
    {
        $stmt = $this->conn->prepare("SELECT patient_id, start_datetime, end_datetime, sport, alcohol, smoke, medication, feeling, pain_scale FROM headaches WHERE end_datetime = 'NULL'");
        $stmt->execute();
        $stmt->bind_result($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        $headaches = array();
        while ($stmt->fetch()) {
            $headache = array();
            $headache['patient_id'] = $patient_id;
            $headache['start_datetime'] = $start_datetime;
            $headache['end_datetime'] = $end_datetime;
            $headache['sport'] = $sport;
            $headache['alcohol'] = $alcohol;
            $headache['smoke'] = $smoke;
            $headache['medication'] = $medication;
            $headache['feeling'] = $feeling;
            $headache['pain_scale'] = $pain_scale;
            array_push($headaches, $headache);
        }
        return $headaches;
    }

    public function readActiveCrisisByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT h.patient_id, h.start_datetime, h.end_datetime, h.sport, h.alcohol, h.smoke, h.medication, h.feeling, h.pain_scale FROM headaches h JOIN patients_login p ON h.patient_id = p.patient_id WHERE p.email = ? AND end_datetime IS NULL OR end_datetime = 0000-00-00 LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        if ($stmt->fetch()) {
            $headache = array();
            $headache['patient_id'] = $patient_id;
            $headache['start_datetime'] = $start_datetime;
            $headache['end_datetime'] = $end_datetime;
            $headache['sport'] = $sport;
            $headache['alcohol'] = $alcohol;
            $headache['smoke'] = $smoke;
            $headache['medication'] = $medication;
            $headache['feeling'] = $feeling;
            $headache['pain_scale'] = $pain_scale;
            return $headache;
        } else {
            return null;
        }
    }

    public function readActiveCrisisById($patient_id)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, start_datetime, end_datetime, sport, alcohol, smoke, medication, feeling, pain_scale FROM headaches WHERE patient_id = ? AND end_datetime IS NULL OR end_datetime = 0000-00-00 LIMIT 1");
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $stmt->bind_result($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        if ($stmt->fetch()) {
            $headache = array();
            $headache['patient_id'] = $patient_id;
            $headache['start_datetime'] = $start_datetime;
            $headache['end_datetime'] = $end_datetime;
            $headache['sport'] = $sport;
            $headache['alcohol'] = $alcohol;
            $headache['smoke'] = $smoke;
            $headache['medication'] = $medication;
            $headache['feeling'] = $feeling;
            $headache['pain_scale'] = $pain_scale;
            return $headache;
        } else {
            return null;
        }
    }

    /**
     * Funcion que lee las crisis activas de un paciente, si hubiese mas de una crisis activa, eso implicaria un error
     *
     * @param $patient_id
     */
    public function readPatientActiveCrisis($patient_id)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, start_datetime, end_datetime, sport, alcohol, smoke, medication, feeling, pain_scale FROM headaches WHERE  end_datetime IS NULL OR  end_datetime = '0000-00-00 00:00:00.000000' AND patient_id = ? ");
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $stmt->bind_result($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        $headache = array();
        //if ($stmt->affected_rows == 1) {
        $stmt->fetch();
        $headache['patient_id'] = $patient_id;
        $headache['ch_id'] = "1234567";
        $headache['start_datetime'] = $start_datetime;
        $headache['end_datetime'] = $end_datetime;
        $headache['sport'] = $sport;
        $headache['alcohol'] = $alcohol;
        $headache['smoke'] = $smoke;
        $headache['medication'] = $medication;
        $headache['feeling'] = $feeling;
        $headache['pain_scale'] = $pain_scale;
        //}
        return $headache;
    }

    /********** DELETE **********/


    /********** OTRAS **********/

    // Funciones publicas
    /********** UPDATE **********/

    public function updateCrisis($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale)
    {
        $stmt = $this->conn->prepare("UPDATE headaches SET end_datetime = ?, sport = ?, alcohol = ?, smoke = ?, medication = ?, feeling = ?, pain_scale = ? WHERE patient_id = ? AND start_datetime = ?");
        $stmt->bind_param('ssssssiss', $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale, $patient_id, $start_datetime);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Funciones privadas



    public function activeCrisis($patient_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM headaches WHERE end_datetime IS NULL OR end_datetime = '0000-00-00 00:00:00.000000' AND patient_id = ?");
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $stmt->store_result();
        var_dump($stmt->num_rows);
        if ($stmt->num_rows == 1) {
            return ONE_ACTIVE_HEADACHE;
        } elseif ($stmt->num_rows > 1) {
            return MORE_THAN_ONE_ACTIVE_HEADACHE;
        } else {
            return NO_ACTIVE_HEADACHES;
        }
    }

}