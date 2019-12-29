<?php


class DbDoctors
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
     * Función que crea e inserta un paciente en la tabla de patients_login
     * CreatePatient
     */
    public function createDoctor()
    {

    }

    /********** READ, READBY & READALL **********/

    /**
     * Función que lee todos los pacientes de la tabla patients login
     * ReadAllPatients
     */
    public function readAllDoctors()
    {
        $stmt = $this->conn->prepare("SELECT patient_id, email, name, surname1, surname2, init_date, end_date, phone FROM patients_login WHERE patient_id LIKE 'NP%'");
        $stmt->execute();
        $stmt->bind_result($patient_id, $email, $name, $surname1, $surname2, $init_date, $end_date, $phone);
        $doctors = array();
        while ($stmt->fetch()) {
            $doctor = array();
            $doctor['patient_id'] = $patient_id;
            $doctor['email'] = $email;
            $doctor['name'] = $name;
            $doctor['surname1'] = $surname1;
            $doctor['surname2'] = $surname2;
            $doctor['init_date'] = $init_date;
            $doctor['end_date'] = $end_date;
            $doctor['phone'] = $phone;
            array_push($doctors, $doctor);
        }
        return $doctors;
    }

    /**
     * Función que lee y devuelve un usuario dado un email
     * ReadPatientByEmail
     *
     * @param $email
     *
     * @return array
     */
    public function readDoctorByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT patient_id, email, name, surname1, surname2, phone FROM patients_login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($patient_id, $email, $name, $surname1, $surname2, $phone);
        $stmt->fetch();
        $doctor = array();
        $doctor['patient_id'] = $patient_id;
        $doctor['email'] = $email;
        $doctor['name'] = $name;
        $doctor['surname1'] = $surname1;
        $doctor['surname2'] = $surname2;
        $doctor['phone'] = $phone;
        return $doctor;
    }

    /**
     * Función que devuelve la contraseña dado un email
     * ReadPasswordByEmail
     */
    public function readPasswordByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT password FROM patients_login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($password);
        $stmt->fetch();
        return $password;
    }

    /********** UPDATE **********/

    /**
     * Funcion de actualizacion de la contraseña del paciente
     * UpdatePatientPassword
     */
    public function updateDoctorPassword($email, $old_password, $new_password)
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
     * Funcion que elimina el perfil del paciente -> Esta no se usara, pero sirve de plantilla para operaciones similares
     * DeletePatient
     */
    public function deleteDoctor($patient_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM patients_login WHERE patient_id = ?");
        $stmt->bind_param("s", $patient_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /********** OTRAS **********/

    // Funciones públicas

    /**
     * Función de login de un usuario dado un email y una contraseña
     * PatientLogin
     */
    public function doctorLogin($email, $password)
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

    // Funciones privadas

    /**
     * Funcion que comprueba que el email introducido a la hora de insertar un nuevo paciente en la tabla no existe
     */
    private function isEmailValid($userEmail)
    {
        $stmt = $this->conn->prepare("SELECT email FROM patients_login WHERE email = ?");
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

}