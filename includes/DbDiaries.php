<?php


class DbDiaries
{


    private $conn;

    function __construct()
    {
        include_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect;

        $this->conn = $db->connect();
    }

    // CRUD Functions

    /********** CREATE **********/


    public function createDiary($patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling)
    {
        //$date = date("Y-m-d");
        if (!$this->checkLastDiaryDate($patient_id, $date)) {
            $stmt = $this->conn->prepare("INSERT INTO diary (patient_id, date, sleep_time, change_residence, sport_time, alcohol, smoke, feeling) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            //REVISAR
            $stmt->bind_param("ssiissss", $patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling);
            if ($stmt->execute()) {
                return DIARY_CREATED;
            } else {
                return DIARY_FAILURE;
            }
        } else {
            return DIARY_ALREADY_INSERTED;
        }
    }

    private function checkLastDiaryDate($patient_id, $date)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, date FROM diary WHERE patient_id = ? AND date = ?");
        $stmt->bind_param("ss", $patient_id, $date);
        // Ejecutamos la consulta
        $stmt->execute();
        // Guardamos el resultado
        $stmt->store_result();
        // Devolvemos cuando el numero de filas que da como resultado es mayor que 0
        return $stmt->num_rows > 0;
    }

    /********** READ, READBY & READALL **********/


    public function readAllDiaries()
    {
        $stmt = $this->conn->prepare("SELECT patient_id, date, sleep_time, change_residence, sport_time, alcohol, smoke, feeling FROM diary");
        $stmt->execute();
        $stmt->bind_result($patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling);
        $diaries = array();
        while ($stmt->fetch()) {
            $diary = array();
            $diary['patient_id'] = $patient_id;
            $diary['date'] = $date;
            $diary['sleep_time'] = $sleep_time;
            $diary['change_residence'] = $change_residence;
            $diary['sport_time'] = $sport_time;
            $diary['alcohol'] = $alcohol;
            $diary['smoke'] = $smoke;
            $diary['feeling'] = $feeling;
            array_push($diaries, $diary);
        }
        return $diaries;
    }

    public function readAllDiariesByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT d.patient_id, d.date, d.sleep_time, d.change_residence, d.sport_time, d.alcohol, d.smoke, d.feeling FROM diary d JOIN patients_login p ON d.patient_id = p.patient_id WHERE p.email = ? ORDER BY date DESC");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling);
        $diaries = array();
        while ($stmt->fetch()) {
            $diary = array();
            $diary['patient_id'] = $patient_id;
            $diary['date'] = $date;
            $diary['sleep_time'] = $sleep_time;
            $diary['change_residence'] = $change_residence;
            $diary['sport_time'] = $sport_time;
            $diary['alcohol'] = $alcohol;
            $diary['smoke'] = $smoke;
            $diary['feeling'] = $feeling;
            array_push($diaries, $diary);
        }
        return $diaries;
    }

    public function readAllDiariesById($patient_id)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, date, sleep_time, change_residence, sport_time, alcohol, smoke, feeling FROM diary WHERE patient_id = ? ORDER BY date DESC");
        $stmt->bind_param('s', $patient_id);
        $stmt->execute();
        $stmt->bind_result($patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling);
        $diaries = array();
        while ($stmt->fetch()) {
            $diary = array();
            $diary['patient_id'] = $patient_id;
            $diary['date'] = $date;
            $diary['sleep_time'] = $sleep_time;
            $diary['change_residence'] = $change_residence;
            $diary['sport_time'] = $sport_time;
            $diary['alcohol'] = $alcohol;
            $diary['smoke'] = $smoke;
            $diary['feeling'] = $feeling;
            array_push($diaries, $diary);
        }
        return $diaries;
    }

    public function readLastDiary($patient_id)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, date, sleep_time, change_residence, sport_time, alcohol, smoke, feeling FROM diary WHERE patient_id = ? ORDER BY date DESC LIMIT 1");
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $stmt->bind_result($patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling);
        if ($stmt->fetch()) {
            $diary = array();
            $diary['patient_id'] = $patient_id;
            $diary['date'] = $date;
            $diary['sleep_time'] = $sleep_time;
            $diary['change_residence'] = $change_residence;
            $diary['sport_time'] = $sport_time;
            $diary['alcohol'] = $alcohol;
            $diary['smoke'] = $smoke;
            $diary['feeling'] = $feeling;
            return $diary;
        } else {
            return null;
        }
    }

    public function readDiaryByDate($patient_id, $date)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, date, sleep_time, change_residence, sport_time, alcohol, smoke, feeling FROM diary WHERE patient_id = ? AND date = ?");
        $stmt->bind_param("ss", $patient_id, $date);
        $stmt->execute();
        $stmt->bind_result($patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling);
        if ($stmt->fetch()) {
            $diary = array();
            $diary['patient_id'] = $patient_id;
            $diary['date'] = $date;
            $diary['sleep_time'] = $sleep_time;
            $diary['change_residence'] = $change_residence;
            $diary['sport_time'] = $sport_time;
            $diary['alcohol'] = $alcohol;
            $diary['smoke'] = $smoke;
            $diary['feeling'] = $feeling;
            return $diary;
        } else {
            return null;
        }
    }

    /********** UPDATE **********/

    public function updateDiary($patient_id, $date, $sleep_time, $sport_time, $alcohol, $smoke, $feeling)
    {
        // Si coinciden ambas contraseñas
        // Si es necesario hasheamos la nueva contraseña password_hash($new_password, PASSWORD_DEFAULT);
        // Preparamos la consulta
        $stmt = $this->conn->prepare("UPDATE diary SET sleep_time = ? sport_time = ? alcohol = ? smoke = ? feeling = ? WHERE patient_id = ? AND date = ?");
        // Bindeamos los parametros
        $stmt->bind_param("sssssss", $sleep_time, $sport_time, $alcohol, $smoke, $feeling, $patient_id, $date);
        // Comprobamos si se ejecuta con un if
        if ($stmt->execute()) {
            // Si se ejecuta correctamente
            return true;
        } else {
            // Si no se ejecuta correctamente
            return false;
        }
    }

    /********** DELETE **********/

    public function deleteDiary($patient_id, $date)
    {
        $stmt = $this->conn->prepare("DELETE FROM diary WHERE patient_id = ? AND date = ?");
        $stmt->bind_param("ss", $patient_id, $date);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Funciones privadas

    /********** OTRAS **********/

    public function checkDiaryFilled($patient_id, $date)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, date FROM diary WHERE patient_id = ? AND date = ?");
        $stmt->bind_param("ss", $patient_id, $date);
        // Bindeamos el resultado
        $stmt->bind_result($patient_id, $date);
        // Fetcheamos(Extraemos) los resultados
        $stmt->fetch();
        // Como devolvemos varias cosas en la consulta creamos un array objeto donde almacenamos cada resultado
        $diary_data = array();
        // Asignamos un par clave valor a cada resultado
        $diary_data['patient_id'] = $patient_id;
        $diary_data['date'] = $date;
        $today = date("Y-m-d");
        if ($diary_data['date'] == $today) {
            return true;
        } else {
            return false;
        }
    }

}