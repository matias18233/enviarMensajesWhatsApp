<?php

function escribirContenidoEnLog($contenido) {
    $archivo = fopen("datos.log", 'a+');
    $contenido = "\n" . $contenido . " | " . date("Y-m-d");
    fwrite($archivo, $contenido);
    fclose($archivo);
}

function controlarLlegadaDeVariables() {
    if ($GLOBALS['escribirLog'] == true) {
        foreach ($_REQUEST as $campo => $valor) {
            escribirContenidoEnLog(" - " . $campo . " = " . $valor);
        }
    }
}

function existeYTieneContenido($variable) {
    if ((trim($variable) != "") && (isset($variable))) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function variablesRecibidasSinProblema() {
    $sinProblemas = TRUE;

    if (existeYTieneContenido($_POST['telefono'])) {
        $GLOBALS['telefonoDestinatario'] = trim($_POST['telefono']);
    } else {
        $sinProblemas = FALSE;
        $GLOBALS['mensajeRespuesta'] = "No se ha especificado un n&uacute;mero de tel&eacute;fono.";
    }

    if ($sinProblemas) {
        if (existeYTieneContenido($_POST['texto'])) {
            $GLOBALS['mensajeAEnviar'] = trim($_POST['texto']);
        } else {
            $sinProblemas = FALSE;
            $GLOBALS['mensajeRespuesta'] = "No se ha especificado un mensaje";
        }
    }

    return $sinProblemas;
}

function parametrosDeEnvio() {
    $jsonContenido = json_encode(
        [
            'phone' => $GLOBALS['telefonoDestinatario'],
            'body' => $GLOBALS['mensajeAEnviar'],
        ]
    );
    $parametrosEnvio = stream_context_create(
        [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $jsonContenido
            ]
        ]
    );
    return $parametrosEnvio;
}

function urlEnvio() {
    $token = "?token=" . $GLOBALS['token_secreto'];
    return $GLOBALS['url_envio'] . $token;
}

function enviarMensajeWhatsapp() {
    $sinProblemas = TRUE;
    
    $respuesta = file_get_contents(urlEnvio(), false, parametrosDeEnvio()); // Envío del mensaje
    $respuesta = json_decode($respuesta);
    if ($respuesta->{'sent'}) {
        $GLOBALS['mensajeRespuesta'] = "El mensaje fue enviado correctamente";
        if ($GLOBALS['escribirLog'] == true) {
            escribirContenidoEnLog("\nMensaje enviado: " . $GLOBALS['telefonoDestinatario'] . " " . $GLOBALS['mensajeAEnviar']);
        }
    } else {
        $sinProblemas = FALSE;
        $GLOBALS['mensajeRespuesta'] = "Hubo un error en el envío del mensaje";
        if ($GLOBALS['escribirLog'] == true) {
            escribirContenidoEnLog("\nError en el envío: " . $GLOBALS['telefonoDestinatario'] . " " . $GLOBALS['mensajeAEnviar']);
        }
    }
    return $sinProblemas;
}
?>