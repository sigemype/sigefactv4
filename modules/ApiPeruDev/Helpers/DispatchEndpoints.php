<?php

namespace Modules\ApiPeruDev\Helpers;

/**
 * Class SunatEndpoints.
 */
final class DispatchEndpoints
{
    const TOKEN = 'https://api-seguridad.sunat.gob.pe/v1/clientessol/{client_id}/oauth2/token';
    const SEND = 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/{filename}';
    const TICKET = 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/{numTicket}';

    const TOKEN_BETA = 'https://gre-test.nubefact.com/v1/clientessol/{client_id}/oauth2/token';
    const SEND_BETA = 'https://gre-test.nubefact.com/v1/contribuyente/gem/comprobantes/{filename}';
    const TICKET_BETA = 'https://gre-test.nubefact.com/v1/contribuyente/gem/comprobantes/envios/{numTicket}';

    const CLIENT_ID_BETA = 'test-85e5b0ae-255c-4891-a595-0b98c65c9854';
    const CLIENT_SECRET_BETA = 'test-Hty/M6QshYvPgItX2P0+Kw==';
}
