<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Azure Entra ID (Service Principal)
    |--------------------------------------------------------------------------
    |
    | Credenciales de la App Registration en Azure Entra ID.
    | Se usan para autenticarse con OAuth2 client_credentials
    | y luego generar Embed Tokens de Power BI.
    |
    */

    'tenant_id'     => env('POWERBI_TENANT_ID'),
    'client_id'     => env('POWERBI_CLIENT_ID'),
    'client_secret' => env('POWERBI_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Power BI Workspace
    |--------------------------------------------------------------------------
    |
    | ID del workspace de Power BI Service donde están publicados
    | los reportes. Se obtiene de la URL del workspace:
    | https://app.powerbi.com/groups/{workspace_id}/...
    |
    */

    'workspace_id' => env('POWERBI_WORKSPACE_ID'),

    /*
    |--------------------------------------------------------------------------
    | Azure Endpoints
    |--------------------------------------------------------------------------
    */

    'authority_url' => 'https://login.microsoftonline.com/',
    'resource_url'  => 'https://analysis.windows.net/powerbi/api/.default',
    'api_url'       => 'https://api.powerbi.com/v1.0/myorg/',

    /*
    |--------------------------------------------------------------------------
    | Report IDs
    |--------------------------------------------------------------------------
    |
    | Mapeo de la clave del reporte (usada internamente) al Report ID
    | de Power BI Service. Se obtiene al publicar cada reporte:
    | Power BI Service → Workspace → Reporte → Settings → Report ID
    |
    | Nota: Un archivo .pbix puede tener múltiples páginas.
    | Se puede embeber una página específica usando el pageName
    | en la configuración JS del frontend.
    |
    */

    'reports' => [
        'cliente'                  => env('POWERBI_REPORT_CLIENTE'),
        'admin'                    => env('POWERBI_REPORT_ADMIN'),
        'direccion'                => env('POWERBI_REPORT_DIRECCION'),
        'solicitud-evidencia-pago' => env('POWERBI_REPORT_SOLICITUD_EVIDENCIA_PAGO'),
        'evidencia-pago'           => env('POWERBI_REPORT_EVIDENCIA_PAGO'),
        'evidencia-pago-antiguo'   => env('POWERBI_REPORT_EVIDENCIA_PAGO_ANTIGUO'),
        'ticket'                   => env('POWERBI_REPORT_TICKET'),
        'cita'                     => env('POWERBI_REPORT_CITA'),
        'letra'                    => env('POWERBI_REPORT_LETRA'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Names (opcional)
    |--------------------------------------------------------------------------
    |
    | Si un solo archivo .pbix contiene múltiples páginas/dashboards,
    | puedes especificar el pageName de Power BI para embeber una
    | página específica. Dejar null para cargar la página por defecto.
    |
    */

    'pages' => [
        'cliente'                  => env('POWERBI_PAGE_CLIENTE'),
        'admin'                    => env('POWERBI_PAGE_ADMIN'),
        'direccion'                => env('POWERBI_PAGE_DIRECCION'),
        'solicitud-evidencia-pago' => env('POWERBI_PAGE_SOLICITUD_EVIDENCIA_PAGO'),
        'evidencia-pago'           => env('POWERBI_PAGE_EVIDENCIA_PAGO'),
        'evidencia-pago-antiguo'   => env('POWERBI_PAGE_EVIDENCIA_PAGO_ANTIGUO'),
        'ticket'                   => env('POWERBI_PAGE_TICKET'),
        'cita'                     => env('POWERBI_PAGE_CITA'),
        'letra'                    => env('POWERBI_PAGE_LETRA'),
    ],

];
