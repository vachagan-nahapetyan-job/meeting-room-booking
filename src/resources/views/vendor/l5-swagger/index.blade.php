<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Meeting Room Booking API</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui.min.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; background: #fafafa; }
        .swagger-ui .topbar { background-color: #1b1b1b; }
        .swagger-ui .topbar .download-url-wrapper { display: none; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui-standalone-preset.min.js"></script>
    <script>
    window.onload = () => {
        SwaggerUIBundle({
            // url: window.location.protocol + "//" + window.location.host + "/api-spec/api_swagger.json",
            // dom_id: '#swagger-ui',
            // validatorUrl: null,
            // presets: [
            //     SwaggerUIBundle.presets.apis,
            //     SwaggerUIBundle.SwaggerUIStandalonePreset
            // ]
            url: window.location.protocol + "//" + window.location.host + "/docs/api-docs.json",
            dom_id: '#swagger-ui',
            validatorUrl: null,
            displayRequestDuration: true,
            docExpansion: "list",
            showExtensions: true,
            showCommonExtensions: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ]
        });
    };
    </script>
</body>
</html>
