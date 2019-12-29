<?php

/**
 * Las llamadas a la API o APIcalls tienen la siguiente estructura:
 *        $app->method(GET, POST, PUT, DELETE)('URL', function(Request $request, Response $response){
 *
 *        });
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use Slim\Factory\AppFactory;

// Check the directory
require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../includes/DbPatients.php';

require __DIR__ . '/../includes/DbDiaries.php';

require __DIR__ . '/../includes/DbDoctors.php';

require __DIR__ . '/../includes/DbHeadaches.php';


// Create a new SlimApp
$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

// FUNCTIONS

/**
 * Funcion que valida si los parametros que necesita cualquier funcion de DbPatients están vacios o no
 *
 * @param $parametrosEntrada : Parametros a validar
 * @param $request
 * @param $response : Respuesta
 *
 * @return bool
 */
function emptyParameters($parametrosEntrada, $request, $response)
{
    // Asumimos que no hay ningún error, esto cambiara si los parametros no son correctos pasando a ser true
    $error = false;
    // Aqui almacenaremos los parametros que dan error
    $error_params = '';
    // $_REQUEST son las variables HTTP que se precisan
    $request_params = $request->getParsedBody();

    //Recorremos todos los parametros que se pasan por entrada usando un foreach
    foreach ($parametrosEntrada as $param) {
        // Comprobamos que los parametros requeridos no son NULL y tienen una longitud menor o igual a 0
        // isset determina si una variable esta definida o si por el contrario es NULL
        // strlen comprueba la longitud de un String
        if (!isset($request_params[$param]) || strlen($request_params[$param]) <= 0) {
            $error = true;
            // Con .= concatenamos un string
            $error_params .= $param . ' ';
        }
    }

    // Si hay error, es decir $error es true creamos un array con el estado del error y un mensaje de error que devolveremos como un JSON en la $response
    if ($error) {
        $error_detail = array();
        $error_detail['Estado del error'] = true;
        $error_detail['Mensaje'] = 'Parametros vacios: ' . $error_params;
        // Escribimos el array de los detalles del error com JSON a traves del objeto $response
        $response->getBody()->write(json_encode($error_detail));
    }

    // Devolvemos el error
    return $error;
}

// APIcalls

/********** GET **********/

// PATIENTS

/**
 * endpoint: readAllPatients
 * parameters: none
 * method: GET
 */
$app->get('/CharmAppAPI/public/readAllPatients', function (Request $request, Response $response) {
    // Creamos un objeto nuevo de tipo DbPatients
    $db = new DbPatients;

    // Ejecutamos la funcion readAllPatients()
    $patients = $db->readAllPatients();

    // Creamos un array objeto patient_data para devolver el JSON de todos los pacientes
    $patients_data = array();
    $patients_data['Error'] = false;
    $patients_data['Pacientes'] = $patients;

    // Escribimos el $message como JSON usando $response
    $response->getBody()->write(json_encode($patients_data));
    // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
    // Los Status se pueden comprobar en el fichero Response.php
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

// DOCTORS

/**
 * endpoint: readAllDoctors
 * parameters: none
 * method: GET
 */
$app->get('/CharmAppAPI/public/readAllDoctors', function (Request $request, Response $response) {
    // Creamos un objeto nuevo de tipo DbPatients
    $db = new DbDoctors;

    // Ejecutamos la funcion readAllPatients()
    $doctors = $db->readAllDoctors();

    // Creamos un array objeto patient_data para devolver el JSON de todos los pacientes
    $doctor_data = array();
    $doctor_data['Error'] = false;
    $doctor_data['Pacientes'] = $doctors;

    // Escribimos el $message como JSON usando $response
    $response->getBody()->write(json_encode($doctor_data));
    // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
    // Los Status se pueden comprobar en el fichero Response.php
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

// DIARIES

/**
 *
 */
$app->get('/CharmAppAPI/public/readAllDiaries', function (Request $request, Response $response) {
    $db = new DbDiaries;
    $diaries = $db->readAllDiaries();
    $diary_data = array();
    $diary_data['Error'] = false;
    $diary_data['Diarios'] = $diaries;
    $response->getBody()->write(json_encode($diary_data));
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

// HEADACHES

$app->get('/CharmAppAPI/public/readAllCrisis', function (Request $request, Response $response) {
    $db = new DbHeadaches;
    $headaches = $db->readAllCrisis();
    $headache_data = array();
    $headache_data['Error'] = false;
    $headache_data['Crisis'] = $headaches;
    $response->getBody()->write(json_encode($headache_data));
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

$app->get('/CharmAppAPI/public/readAllActiveCrisis', function(Request $request, Response $response){
    $db = new DbHeadaches;
    $crisis = $db->readAllActiveCrisis();
    $crisis_data = array();
    $crisis_data['Error'] = false;
    $crisis_data['Crisis activas'] = $crisis;
    $response->getBody()->write(json_encode($crisis_data));
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

/********** POST **********/

// PATIENTS

/**
 * endpoint: createPatient_login
 * parameters: patient_id, email, password, name, surname1, surname2, init_date, end_date, phone
 * method: POST
 */
// ¡¡EN ESTE CASO PASAMOS EL patient_id COMO PARAMETRO YA QUE NO ESTA CONFIGURADO EN LOCALHOST PARA QUE SIGA LA MISMA ESTRUCTURA QUE LA BASE DE DATOS, LUEGO HABRA QUE QUITARLO!!
// El method es POST ya que es una operacion CREATE
$app->post('/CharmAppAPI/public/createPatient_login', function (Request $request, Response $response) {
    // Comprobamos que no hay parametros vacios, para ello usamos la funcion parametrosVacios y le pasamos un array con los parametros que se necesitan
    if (!emptyParameters(array('patient_id', 'email', 'password', 'name', 'surname1', 'surname2', 'init_date', 'end_date', 'phone'), $request, $response)) {
        // Usamos $request->getParsedBody() para obtener un array con los parametros parseados de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y la asignamos al campo del array $request_data correspondiente
        $patient_id = $request_data['patient_id'];
        $email = $request_data['email'];
        $password = $request_data['password'];
        $name = $request_data['name'];
        $surname1 = $request_data['surname1'];
        $surname2 = $request_data['surname2'];
        $init_date = $request_data['init_date'];
        $end_date = $request_data['end_date'];
        $phone = $request_data['phone'];

        // Si queremos encriptar la contraseña usamos la funcion password_hash($password, PASSWORD_DEFAULT | PASSWORD_BCRYPT); y lo guardamos en una nueva variable
        $hased_password = password_hash($password, PASSWORD_DEFAULT);

        // Creamos un objeto nuevo de tipo DbPatients
        $db = new DbPatients;

        // Ejecutamos la funcion createPatient_login. Devuelve un codigo que usaremos para comprobar cual ha sido el resultado de la ejecucion de la operacion
        $result = $db->createPatient_login($patient_id, $email, $password, $name, $surname1, $surname2, $init_date, $end_date, $phone);

        // Dependiendo del codigo devuelto en $result creamos un array con el resultado que devolveremos como un JSON a traves del objeto $response y devolveremos el objeto $response
        if ($result == USER_CREATED) {
            // Hacer lo de mandar un correo al paciente con su usuario y contraseña
            $message = array();
            $message['Estado del error'] = false;
            $message['Mensaje'] = 'Paciente creado correctamente';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } elseif ($result == USER_FAILURE) {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'El paciente no se ha creado debido a un error';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        } elseif ($result == USER_EXISTS) {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'El paciente ya existe';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/createPatient', function (Request $request, Response $response) {
    // Comprobamos que no hay parametros vacios, para ello usamos la funcion parametrosVacios y le pasamos un array con los parametros que se necesitan
    if (!emptyParameters(array('email', 'password', 'name', 'surname1', 'surname2', 'init_date', 'end_date', 'phone'), $request, $response)) {
        // Usamos $request->getParsedBody() para obtener un array con los parametros parseados de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y la asignamos al campo del array $request_data correspondiente
        $email = $request_data['email'];
        $password = $request_data['password'];
        $name = $request_data['name'];
        $surname1 = $request_data['surname1'];
        $surname2 = $request_data['surname2'];
        $init_date = $request_data['init_date'];
        $end_date = $request_data['end_date'];
        $phone = $request_data['phone'];

        // Si queremos encriptar la contraseña usamos la funcion password_hash($password, PASSWORD_DEFAULT | PASSWORD_BCRYPT); y lo guardamos en una nueva variable
        $hased_password = password_hash($password, PASSWORD_DEFAULT);

        // Creamos un objeto nuevo de tipo DbPatients
        $db = new DbPatients;

        // Ejecutamos la funcion createPatient_login. Devuelve un codigo que usaremos para comprobar cual ha sido el resultado de la ejecucion de la operacion
        $result = $db->createPatient($email, $password, $name, $surname1, $surname2, $init_date, $end_date, $phone);

        // Dependiendo del codigo devuelto en $result creamos un array con el resultado que devolveremos como un JSON a traves del objeto $response y devolveremos el objeto $response
        if ($result == USER_CREATED) {
            $message = array();
            $message['Estado del error'] = false;
            $message['Mensaje'] = 'Paciente creado correctamente';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } elseif ($result == USER_FAILURE) {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'El paciente no se ha creado debido a un error';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        } elseif ($result == USER_EXISTS) {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'El paciente ya existe';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

/**
 * endpoint: readPatientByEmail
 * parameters: email
 * method: POST
 */
$app->post('/CharmAppAPI/public/readPatientByEmail', function (Request $request, Response $response) {
    // Comprobamos que no hay parametros vacios
    if (!emptyParameters(array('email'), $request, $response)) {
        // Usamos $request->getParsedBody() para obtener un array con los parametros parseados de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y la asignamos al campo del array $request_data correspondiente
        $email = $request_data['email'];

        // Creamos un objeto nuevo de tipo DbPatients
        $db = new DbPatients;

        // Ejecutamos la operacion readPatientByEmail
        $result = $db->readPatientByEmail($email);

        $patient_data['Paciente'] = $result;

        // Escribimos el $message como JSON usando $response
        $response->getBody()->write(json_encode($patient_data));
        // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
        // Los Status se pueden comprobar en el fichero Response.php
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

/**
 * endpoint: readPasswordByEmail
 * parameters: email
 * method: POST
 */
$app->post('/CharmAppAPI/public/readPasswordByEmail', function (Request $request, Response $response) {
    // Comprobamos que no hay parametros vacios
    if (!emptyParameters(array('email'), $request, $response)) {
        // Usamos $request->getParsedBody() para obtener un array con los parametros parseados de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y la asignamos al campo del array $request_data correspondiente
        $email = $request_data['email'];

        // Creamos un objeto nuevo de tipo DbPatients
        $db = new DbPatients;

        // Ejecutamos la operacion readPatientByEmail
        $result = $db->readPasswordByEmail($email);

        $patient_data['Contraseña'] = $result;

        // Escribimos el $message como JSON usando $response
        $response->getBody()->write(json_encode($patient_data));
        // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
        // Los Status se pueden comprobar en el fichero Response.php
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

/**
 * endpoint: patientLogin
 * parameters: email, password
 * method: POST
 */
$app->post('/CharmAppAPI/public/patientLogin', function (Request $request, Response $response) {
    // Comprobamos que no hay parametros vacios
    if (!emptyParameters(array('email', 'password'), $request, $response)) {
        // Usamos $request->getParsedBody() para obtener un array con los parametros parseados de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y la asignamos al campo del array $request_data correspondiente
        $email = $request_data['email'];
        $password = $request_data['password'];

        // Creamos un objeto nuevo de tipo DbPatients
        $db = new DbPatients;

        // Ejecutamos la operacion patientLogin
        $result = $db->patientLogin($email, $password);

        // Dependiendo del codigo devuelto en $result creamos un array con el resultado que devolveremos como un JSON a traves del objeto $response y devolveremos el objeto $response
        if ($result == PATIENT_AUTHENTICATED) {
            // Cuando el paciente se logea correctamente devolvemos un objeto paciente
            $patient = $db->readPatientByEmail($email);
            // Creamos un array para devolver el JSON del paciente
            $patient_data = array();
            $patient_data['Estado del error'] = false;
            $patient_data['Mensaje'] = "Inicio de sesion correcto";
            $patient_data['Paciente'] = $patient;

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($patient_data));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);

        } elseif ($result == PATIENT_PASSWORD_DONT_MATCH) {
            // Cuando las credenciales no son correctas
            // Creamos un array para devolver el JSON del paciente
            $patient_data = array();
            $patient_data['Estado del error'] = true;
            $patient_data['Mensaje'] = "Inicio de sesion fallido, usuario o contraseña incorrectos";

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($patient_data));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == PATIENT_NOT_FOUND) {
            // Cuando el paciente no existe
            // Creamos un array para devolver el JSON del paciente
            $patient_data = array();
            $patient_data['Estado del error'] = true;
            $patient_data['Mensaje'] = "Inicio de sesion fallido, el paciente no existe";

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($patient_data));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

/**
 * endpoint: updatePatientPassword
 * parameters: email -> Se pasa como el resto de los parametros, patient_id -> Se pasa a través del array $args
 * method: PUT
 */
$app->post('/CharmAppAPI/public/updatePatientPassword', function (Request $request, Response $response): Response {
    // Comprobamos que no haya ningun parametro vacio
    if (!emptyParameters(array('email', 'old_password', 'new_password'), $request, $response)) {
        // Si todos los parametros son correctos los almacenamos en $patient_data
        $request_data = $request->getParsedBody();
        // Asignamos cada parametro a una variable
        $email = $request_data["email"];
        $old_password = $request_data['old_password'];
        $new_password = $request_data['new_password'];

        // Creamos un nuevo objeto DbPatients
        $db = new DbPatients;
        // Guardamos el resultado de la operacion updatePatientPassword en una variable
        $result = $db->updatePatientPassword($email, $old_password, $new_password);
        // Comprobamos los tres posibles resultados
        if ($result == PASSWORD_CHANGED) {
            // Creamos un array para devolver el JSON del resultado
            $message = array();
            $message['Estado del error'] = false;
            $message['Mensaje'] = 'Contraseña cambiada correctamente';

            // Escribimos el mensaje
            $response->getBody()->write(json_encode($message));

            // Devolvemos la respuesta
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == PASSWORD_NOT_CHANGED) {
            // Creamos un array para devolver el JSON del resultado
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'Contraseña no cambiada, intentelo de nuevo mas tarde';

            // Escribimos el mensaje
            $response->getBody()->write(json_encode($message));

            // Devolvemos la respuesta
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == PASSWORD_DONT_MATCH) {
            // Creamos un array para devolver el JSON del resultado
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'La contraseña actual no es correcta';

            // Escribimos el mensaje
            $response->getBody()->write(json_encode($message));

            // Devolvemos la respuesta
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});


// DOCTORS

/**
 * endpoint: readDoctorByEmail
 * parameters: email
 * method: POST
 */
$app->post('/CharmAppAPI/public/readDoctorByEmail', function (Request $request, Response $response) {
    // Comprobamos que no hay parametros vacios
    if (!emptyParameters(array('email'), $request, $response)) {
        // Usamos $request->getParsedBody() para obtener un array con los parametros parseados de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y la asignamos al campo del array $request_data correspondiente
        $email = $request_data['email'];

        // Creamos un objeto nuevo de tipo DbPatients
        $db = new DbDoctors;

        // Ejecutamos la operacion readPatientByEmail
        $result = $db->readDoctorByEmail($email);

        $doctor_data['Doctor'] = $result;
        // Escribimos el $message como JSON usando $response
        $response->getBody()->write(json_encode($doctor_data));
        // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
        // Los Status se pueden comprobar en el fichero Response.php
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});


/**
 * endpoint: doctorLogin
 * parameters: email, password
 * method: POST
 */
$app->post('/CharmAppAPI/public/doctorLogin', function (Request $request, Response $response) {
    // Comprobamos que no hay parametros vacios
    if (!emptyParameters(array('email', 'password'), $request, $response)) {
        // Usamos $request->getParsedBody() para obtener un array con los parametros parseados de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y la asignamos al campo del array $request_data correspondiente
        $email = $request_data['email'];
        $password = $request_data['password'];

        // Creamos un objeto nuevo de tipo DbPatients
        $db = new DbDoctors;

        // Ejecutamos la operacion patientLogin
        $result = $db->doctorLogin($email, $password);

        // Dependiendo del codigo devuelto en $result creamos un array con el resultado que devolveremos como un JSON a traves del objeto $response y devolveremos el objeto $response
        if ($result == PATIENT_AUTHENTICATED) {
            // Cuando el paciente se logea correctamente devolvemos un objeto paciente
            $doctor = $db->readDoctorByEmail($email);
            // Creamos un array para devolver el JSON del paciente
            $doctor_data = array();
            $doctor_data['Estado del error'] = false;
            $doctor_data['Mensaje'] = "Inicio de sesion correcto";
            $doctor_data['Doctor'] = $doctor;

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($doctor_data, JSON_UNESCAPED_UNICODE));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);

        } elseif ($result == PATIENT_PASSWORD_DONT_MATCH) {
            // Cuando las credenciales no son correctas
            // Creamos un array para devolver el JSON del paciente
            $doctor_data = array();
            $doctor_data['Estado del error'] = true;
            $doctor_data['Mensaje'] = "Inicio de sesion fallido, usuario o contraseña incorrectos";

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($doctor_data));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == PATIENT_NOT_FOUND) {
            // Cuando el paciente no existe
            // Creamos un array para devolver el JSON del paciente
            $doctor_data = array();
            $doctor_data['Estado del error'] = true;
            $doctor_data['Mensaje'] = "Inicio de sesion fallido, el doctor no existe";

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($doctor_data));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

// DIARIES

$app->post('/CharmAppAPI/public/createDiary', function (Request $request, Response $response) {
    // Comprobamos que no hay parametros vacios, para ello usamos la funcion parametrosVacios y le pasamos un array con los parametros que se necesitan
    if (!emptyParameters(array('patient_id', 'date', 'sleep_time', 'change_residence', 'sport_time', 'alcohol', 'smoke', 'feeling'), $request, $response)) {
        // Usamos $request->getParsedBody() para obtener un array con los parametros parseados de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y la asignamos al campo del array $request_data correspondiente
        $patient_id = $request_data['patient_id'];
        $date = $request_data['date'];
        $sleep_time = $request_data['sleep_time'];
        $change_residence = $request_data['change_residence'];
        $sport_time = $request_data['sport_time'];
        $alcohol = $request_data['alcohol'];
        $smoke = $request_data['smoke'];
        $feeling = $request_data['feeling'];

        // Creamos un objeto nuevo de tipo DbPatients
        $db = new DbDiaries;

        // Ejecutamos la funcion createPatient_login. Devuelve un codigo que usaremos para comprobar cual ha sido el resultado de la ejecucion de la operacion
        $result = $db->createDiary($patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling);

        // Dependiendo del codigo devuelto en $result creamos un array con el resultado que devolveremos como un JSON a traves del objeto $response y devolveremos el objeto $response
        if ($result == DIARY_CREATED) {
            $message = array();
            $message['Estado del error'] = false;
            $message['Mensaje'] = 'Formulario diario insertado correctamente';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } elseif ($result == DIARY_FAILURE) {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'El formulario de diario no se ha insertado debido a un error';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        } elseif ($result == DIARY_ALREADY_INSERTED) {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'Ya ha rellenado el formulario para el dia de hoy';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readAllPatientDiaries', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbDiaries;
        $diaries = $db->readAllPatientDiaries($patient_id);
        $diary_data = array();
        $diary_data['Error'] = false;
        $diary_data['Diarios'] = $diaries;

        $response->getBody()->write(json_encode($diary_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readLastDiary', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbDiaries;
        $diary = $db->readLastDiary($patient_id);
        $diary_data = array();
        $diary_data['Error'] = false;
        $diary_data['Diario'] = $diary;

        $response->getBody()->write(json_encode($diary_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readDiaryByDate', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id', 'date'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $date = $request_data['date'];

        $db = new DbDiaries;
        $diary = $db->readDiaryByDate($patient_id, $date);
        $diary_data = array();
        $diary_data['Error'] = false;
        $diary_data['Diario'] = $diary;

        $response->getBody()->write(json_encode($diary_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});


// HEADACHES

/**
 *
 */
$app->post('/CharmAppAPI/public/activeCrisis', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbHeadaches;
        $result = $db->activeCrisis($patient_id);
        if ($result == ONE_ACTIVE_HEADACHE) {
            $message = array();
            $message['Estado del error'] = false;
            $message['Crisis activa'] = true;

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } elseif ($result == MORE_THAN_ONE_ACTIVE_HEADACHE) {
            $message = array();
            $message['Estado del error'] = true;
            $message['Crisis activa'] = false;

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } else {
            $message = array();
            $message['Estado del error'] = false;
            $message['Crisis activa'] = false;

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }

});

$app->post('/CharmAppAPI/public/readPatientActiveCrisis', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbHeadaches;
        $crisis = $db->readPatientActiveCrisis($patient_id);
        $crisis_data = array();
        $crisis_data['Error'] = false;
        $crisis_data['Crisis'] = $crisis;

        $response->getBody()->write(json_encode($crisis_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readPatientCrisiByDate', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id', 'date'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbHeadaches;
        $crisis = $db->readPatientActiveCrisis($patient_id);
        $crisis_data = array();
        $crisis_data['Error'] = false;
        $crisis_data['Crisis'] = $crisis;

        $response->getBody()->write(json_encode($crisis_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readAllPatientCrisis', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbHeadaches;
        $crisis = $db->readAllPatientCrisis($patient_id);
        $crisis_data = array();
        $crisis_data['Error'] = false;
        $crisis_data['Crisis'] = $crisis;
        $response->getBody()->write(json_encode($crisis_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readAllPatientCrisisByDate', function (Request $request, Response $response){
    if(!emptyParameters(array('patient_id', 'date'), $request, $response)){
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $date = $request_data['date'];

        $db = new DbHeadaches;
        $crisis = $db->readCrisisByDate($patient_id, $date);
        $crisis_data = array();
        $crisis_data['Error'] = false;
        $crisis_data['Crisis'] = $crisis;
        $response->getBody()->write(json_encode($crisis_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    }
    else{
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readAllPatientActiveCrisis', function(Request $request, Response $response){
    if(!emptyParameters(array('patient_id'), $request, $response)){
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbHeadaches;
        $crisis = $db->readAllPatientActiveCrisis($patient_id);
        $crisis_data = array();
        $crisis_data['Error'] = false;
        $crisis_data['Crisis'] = $crisis;
        $response->getBody()->write(json_encode($crisis_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    }
    else{
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

/**
 * endpoint: createHeadache
 * parameters: patient_id, ch_id, start_datetime, end_datetime, sport, alcohol, smoke, medication, feeling, pain_scale
 * method: POST
 */
$app->post('/CharmAppAPI/public/createHeadache', function (Request $request, Response $response) {
    // Comprobamos que no hay parámetros vacios
    if (!emptyParameters(array('patient_id', 'ch_id', 'start_datetime', 'end_datetime', 'sport', 'alcohol', 'smoke', 'medication', 'feeling', 'pain_scale'), $request, $response)) {
        // Usamos getParsedBody() para obtener el array con los parametros de la URL
        $request_data = $request->getParsedBody();
        // Creamos una variable por cada parametro y le asignamos el campo del array $request_data correspondiente
        $patient_id = $request_data['patient_id'];
        $ch_id = $request_data['ch_id'];
        $start_datetime = $request_data['start_datetime'];
        $end_datetime = $request_data['end_datetime'];
        $sport = $request_data['sport'];
        $alcohol = $request_data['alcohol'];
        $smoke = $request_data['smoke'];
        $medication = $request_data['medication'];
        $feeling = $request_data['feeling'];
        $pain_scale = $request_data['pain_scale'];

        // Creamos un nuebo objeto de tipo DbHeadaches
        $db = new DbHeadaches;

        // Ejecutamos la funcion createHeadache
        $result = $db->createHeadache($patient_id, $ch_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);

        // Dependiendo del codigo devuelto creamos un array con el resultado que devolveremos como un JSON
        if ($result == HEADACHE_CREATED) {
            $message = array();
            $message['Estado del error'] = false;
            $message['Mensaje'] = 'Headache creado correctamente';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 201 que indica OK
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } elseif ($result == HEADACHE_FAILURE) {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'El headache no se ha creado debido a un error';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        } else {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'El patient_id no existe';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

/********** PUT **********/

// PATIENTS

// DOCTORS

// DIARIES

$app->post('/CharmAppAPI/public/updateDiary', function (Request $request, Response $response) {
    //var_dump($request->getParsedBody());
    if (!emptyParameters(array('patient_id', 'date', 'sleep_time', 'sport_time', 'alcohol', 'smoke', 'feeling'), $request, $response)) {
        $request_data = $request->getParsedBody();

        $patient_id = $request_data['patient_id'];
        $date = $request_data['date'];
        $sleep_time = $request_data['sleep_time'];
        $sport_time = $request_data['sport_time'];
        $alcohol = $request_data['alcohol'];
        $smoke = $request_data['smoke'];
        $feeling = $request_data['feeling'];

        $db = new DbDiaries;
        $result = $db->updateDiary($patient_id, $date, $sleep_time, $sport_time, $alcohol, $smoke, $feeling);

        if ($result) {
            $message = array();
            $message['Estado del error'] = false;
            $message['Mensaje'] = 'Datos actualizados correctamente';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'No se ha podido actualizar la información';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

// HEADACHES

$app->post('/CharmAppAPI/public/updateCrisis', function (Request $request, Response $response) {
    //var_dump($request->getParsedBody());
    if (!emptyParameters(array('patient_id', 'start_datetime', 'end_datetime', 'sport', 'alcohol', 'smoke', 'medication', 'feeling', 'pain_scale'), $request, $response)) {
        $request_data = $request->getParsedBody();

        $patient_id = $request_data['patient_id'];
        $start_datetime = $request_data['start_datetime'];
        $end_datetime = $request_data['end_datetime'];
        $sport = $request_data['sport'];
        $alcohol = $request_data['alcohol'];
        $smoke = $request_data['smoke'];
        $medication = $request_data['medication'];
        $feeling = $request_data['feeling'];
        $pain_scale = $request_data['pain_scale'];

        $db = new DbHeadaches;
        $result = $db->updateCrisis($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);

        if ($result) {
            $message = array();
            $message['Estado del error'] = false;
            $message['Mensaje'] = 'Datos actualizados correctamente';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message = array();
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'No se ha podido actualizar la información';

            // Escribimos el $message como JSON usando $response
            $response->getBody()->write(json_encode($message));
            // Devolvemos la respuesta y ponemos el Header 'Content-type', 'application/json' y el Status a 422 que indica Unprocessable Entity
            // Los Status se pueden comprobar en el fichero Response.php
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

/********** DELETE **********/

// PATIENTS

/**
 * endpoint: deletePatient
 * parameters: patient_id
 * method: DELETE
 */
$app->delete('/CharmAppAPI/public/deletePatient/{patient_id}', function (Request $request, Response $response, array $args): Response {
    // Obtenemos el id de los parametros $args
    $patient_id = $args['patient_id'];

    // Creamos un objeto DbPatients
    $db = new DbPatients;
    // Creamos un array donde guardaremos el resultado de la operacion
    $message = array();
    // Comprobamos si se ejecuta la operacion DELETE llamandola dentro de un if
    if ($db->deletePatient($patient_id)) {
        // Si se ejecuta correctamente
        $message['Estado del error'] = false;
        $message['Mensaje'] = 'Paciente eliminado correctamente';
    } else {
        // Si no se ejecuta
        $message['Estado del error'] = true;
        $message['Mensaje'] = 'Paciente no eliminado, inténtelo de nuevo más tarde';
    }

    // Escribimos la respuesta
    $response->getBody()->write(json_encode($message));
    // Devolvemos la respuesta
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

// DOCTORS

// DIARIES

// HEADACHES


$app->run();
