<?php

    class DbConnect{

        // Variable que almacena la conexión
        private $conn;

        function connect(){
            // Incluimos el fichero con las constantes para conectar a la Base de Datos
            include_once dirname(__FILE__) . '/constants/PatientsConstants.php';
            include_once dirname(__FILE__) . '/constants/HeadachesConstants.php';
            include_once dirname(__FILE__) . '/constants/UsersConstants.php';
            include_once dirname(__FILE__) . '/constants/DiariesConstants.php';

            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            if(mysqli_connect_errno()){
                echo "Error al conectarse a la base de datos: " . mysqli_connect_errno();
                return null;
            }
            mysqli_set_charset($this->conn, "utf8");
            // Si la conexíon se realiza correctamente devolvemos el objeto conn
            return $this->conn;
        }

    }