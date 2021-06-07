<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

include 'libreria.php';

// Valores proporcionados por Chat-Api:
$GLOBALS['token_secreto'] = '949dsz3lt2uvv4l4';
$GLOBALS['instance'] = '283692';

$GLOBALS['escribirLog'] = true;
$GLOBALS['url_envio'] = 'https://api.chat-api.com/instance' . $GLOBALS['instance'] . '/sendMessage';
$GLOBALS['mensajeRespuesta'] = "";
$GLOBALS['telefonoDestinatario'] = "";
$GLOBALS['mensajeAEnviar'] = "";

controlarLlegadaDeVariables();

$statusJson = FALSE;
if (variablesRecibidasSinProblema()) {
    if (enviarMensajeWhatsapp()) {
        $statusJson = TRUE;
    }
}
echo json_encode(['status' => $statusJson, 'body' => $GLOBALS['mensajeRespuesta']]);

?>