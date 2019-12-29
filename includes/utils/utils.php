<?php

class utils
{

    /**
     * Funcion que comprueba que el email introducido a la hora de insertar un nuevo paciente en la tabla no existe
     */
    private function isEmailValid($userEmail)
    {
        // Preparamos la consulta, en este caso quiero seleccionar el email para comprobar que existe en la base de datos
        $stmt = $this->conn->prepare("SELECT email FROM patients_login WHERE email = ?");
        // Bindeamos los parametros, en este caso solo es el email
        $stmt->bind_param("s", $userEmail);
        // Ejecutamos la consulta
        $stmt->execute();
        // Guardamos el resultado
        $stmt->store_result();
        // Devolvemos cuando el numero de filas que da como resultado es mayor que 0
        return $stmt->num_rows > 0;
    }

}