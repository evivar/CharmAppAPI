<?php

/**
 * Class DbPatients
 *
 * Clase que contiene las operaciones CRUD relacionadas con los pacientes
 *
 * @package includes
 * @subpackage none
 * @author Ernesto Vivar Laviña <evivar@ucm.es>
 *
 */
class DbPatients
{

    /**
     * @var mysqli|null Variable que almacena la conexión con la base de datos
     */
    private $conn;

    /**
     * DbPatients constructor.
     */
    function __construct()
    {
        include_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect;
        $this->conn = $db->connect();
    }

    // CRUD Functions

    /********** CREATE **********/

    /**
     * Crea e inserta un paciente en la tabla patients_login, en este caso se pasa el patient_id
     *
     * @param string $patient_id el id del paciente
     * @param string $email el email del paciente
     * @param string $password la contraseña del paciente hasheada con SHA512
     * @param string $name el nombre del paciente
     * @param string $surname1 el primer apellido del paciente
     * @param string $surname2 el segundo apellido del paciente
     * @param string $init_date la fecha en la que se incluyó al paciente en el estudio
     * @param string $end_date la fecha en el que el paciente terminará el estudio
     * @param int $phone teléfono del paciente
     *
     * @return int Resultado de la operación
     */
    public function createPatient_login($patient_id, $email, $password, $name, $surname1, $surname2, $init_date, $end_date, $phone)
    {
        if (!$this->isEmailValid($email)) {
            $stmt = $this->conn->prepare("INSERT INTO patients_login (patient_id, email, password, name, surname1, surname2, init_date, end_date, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssi", $patient_id, $email, $password, $name, $surname1, $surname2, $init_date, $end_date, $phone);
            if ($stmt->execute()) {
                return USER_CREATED;
            } else {
                return USER_FAILURE;
            }
        } else {
            return USER_EXISTS;
        }
    }

    /**
     * Compruba si el email existe en la tabla patients_login de la base de datos
     *
     * @param string $userEmail el email a comprobar
     *
     * @return bool True si el email existe, False en caso contrario
     */
    private function isEmailValid($userEmail)
    {
        // Preparamos la consulta, en este caso quiero seleccionar el email para comprobar que existe en la base de datos
        $stmt = $this->conn->prepare("SELECT email FROM patients_login WHERE email = ?");
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }


    /********** READ, READBY & READALL **********/

    /**
     * Crea e inserta un paciente en la tabla patients_login siempre y cuando el paciente no exista previamente
     *
     * @param string $email el email del paciente
     * @param string $password la contraseña del paciente hasheada con SHA512
     * @param string $name el nombre del paciente
     * @param string $surname1 el primer apellido del paciente
     * @param string $surname2 el segundo apellido del paciente
     * @param string $init_date la fecha en la que se incluyó al paciente en el estudio
     * @param string $end_date la fecha en el que el paciente terminará el estudio
     * @param int $phone teléfono del paciente
     *
     * @return int Resultado de la operación
     */
    public function createPatient($email, $password, $name, $surname1, $surname2, $init_date, $end_date, $phone)
    {
        if (!$this->isEmailValid($email)) {
            $stmt = $this->conn->prepare("INSERT INTO patients_login (patient_id, email, password, name, surname1, surname2, init_date, end_date, phone) VALUES ('', ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssi", $email, $password, $name, $surname1, $surname2, $init_date, $end_date, $phone);
            if ($stmt->execute()) {
                // Insertar en la tabla patients
                return USER_CREATED;
            } else {
                return USER_FAILURE;
            }
        } else {
            return USER_EXISTS;
        }
    }

    /**
     * Lee todos los pacientes de la tabla patients_login
     * @return array Todos los pacientes leidos
     */
    public function readAllPatients()
    {
        $stmt = $this->conn->prepare("SELECT patient_id, email, name, surname1, surname2, init_date, end_date, phone FROM patients_login WHERE patient_id LIKE 'CH%'");
        $stmt->execute();
        $stmt->bind_result($patient_id, $email, $name, $surname1, $surname2, $init_date, $end_date, $phone);
        $patients = array();
        while ($stmt->fetch()) {
            $patient = array();
            $patient['patient_id'] = $patient_id;
            $patient['email'] = $email;
            $patient['name'] = $name;
            $patient['surname1'] = $surname1;
            $patient['surname2'] = $surname2;
            $patient['init_date'] = $init_date;
            $patient['end_date'] = $end_date;
            $patient['phone'] = $phone;
            array_push($patients, $patient);
        }
        return $patients;
    }

    /**
     * Lee un paciente dado su email
     *
     * @param string $email el email del paciente
     *
     * @return array La información del paciente
     */
    public function readPatientByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, email, name, surname1, surname2, init_date, end_date, phone FROM patients_login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($patient_id, $email, $name, $surname1, $surname2, $init_date, $end_date, $phone);
        if ($stmt->fetch()) {
            $patient = array();
            $patient['patient_id'] = $patient_id;
            $patient['email'] = $email;
            $patient['name'] = $name;
            $patient['surname1'] = $surname1;
            $patient['surname2'] = $surname2;
            $patient['init_date'] = $init_date;
            $patient['end_date'] = $end_date;
            $patient['phone'] = $phone;
            return $patient;
        } else {
            return null;
        }
    }

    public function readPatientById($patient_id)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, email, name, surname1, surname2, init_date, end_date, phone FROM patients_login WHERE patient_id = ?");
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $stmt->bind_result($patient_id, $email, $name, $surname1, $surname2, $init_date, $end_date, $phone);
        if ($stmt->fetch()) {
            $patient = array();
            $patient['patient_id'] = $patient_id;
            $patient['email'] = $email;
            $patient['name'] = $name;
            $patient['surname1'] = $surname1;
            $patient['surname2'] = $surname2;
            $patient['init_date'] = $init_date;
            $patient['end_date'] = $end_date;
            $patient['phone'] = $phone;
            return $patient;
        } else {
            return null;
        }
    }

    /********** UPDATE **********/

    /**
     * Actualiza la contraseña de un paciente
     *
     * @param string $email el email del paciente
     * @param string $old_password la contraseña antigua
     * @param string $new_password la contraseña nueva
     *
     * @return int Representa el resultado de la operación
     */
    public function updatePatientPassword($email, $old_password, $new_password)
    {
        $dbPassword = $this->readPasswordByEmail($email);
        if ($dbPassword == $old_password) {
            $stmt = $this->conn->prepare("UPDATE patients_login SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $new_password, $email);
            if ($stmt->execute()) {
                return PASSWORD_CHANGED;
            } else {
                return PASSWORD_NOT_CHANGED;
            }
        } else {
            return PASSWORD_DONT_MATCH;
        }
    }

    /********** DELETE **********/

    /**
     * Lee la contraseña de un paciente dado el email
     *
     * @param string $email el email del paciente
     *
     * @return mixed Contraseña del paciente
     */
    public function readPasswordByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT password FROM patients_login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($password);
        if ($stmt->fetch()) {
            return $password;
        } else {
            return null;
        }
    }


    /********** OTRAS **********/

    // Funciones públicas

    /**
     * Elimina a un paciente de la base de datos dado su id
     *
     * @param string $patient_id el id del paciente
     *
     * @return bool True si se elimina correctamente al paciente, False en caso contrario
     */
    public function deletePatient($patient_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM patients_login WHERE patient_id = ?");
        $stmt->bind_param("s", $patient_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Funciones privadas

    /**
     * Logea un paciente si el email existe y la contraseña es correcta
     *
     * @param string $email el email del paciente a logear
     * @param string $password la contraseña del paciente hasheada con SHA512
     *
     * @return int Representa el resultado de la operación
     */
    public function patientLogin($email, $password)
    {
        if ($this->isEmailValid($email)) {
            $dbPassword = $this->readPasswordByEmail($email);
            if ($password == $dbPassword) {
                return PATIENT_AUTHENTICATED;
            } else {
                return PATIENT_PASSWORD_DONT_MATCH;
            }
        } else {
            return PATIENT_NOT_FOUND;
        }
    }

}