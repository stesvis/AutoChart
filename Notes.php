HOW TO GET A TOKEN
==================

1. Allow access to the app by generating client_id and client_secret
--------------------------------------------------------------------

>>> bin/console acme:oauth-server:client:create --redirect-uri="http://stesvis.com" --grant-type="authorization_code" --grant-type="password" --grant-type="refresh_token" --grant-type="token" --grant-type="client_credentials"

Use these:
>>> client_id: 21_57cfrz5um60ww40w8gkcok4s4soowgk4g00wo0w8scows44880
>>> client_secret: 2evhnip4178ko8gsk800k4kskckgko4kc40goowsw0o880ckgw


2. Generate a token using the client_id and client_secret
---------------------------------------------------------

http://localhost:8000/app_dev.php/oauth/v2/token?client_id=21_57cfrz5um60ww40w8gkcok4s4soowgk4g00wo0w8scows44880&client_secret=2evhnip4178ko8gsk800k4kskckgko4kc40goowsw0o880ckgw&grant_type=client_credentials
