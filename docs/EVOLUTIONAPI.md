n8n	5678	http://localhost:5678	Automatizaciones y flujos.
Evolution API	8080	http://localhost:8080	El servidor de WhatsApp.
Dashboard	8080	http://localhost:8080/manager	Panel para escanear el QR.
Postgres	5432	(Interno)	Base de datos (no accesible desde fuera de Docker).
Redis	6379	(Interno)	Caché de sesiones (no accesible desde fuera de Docker).

PostgreSQL	localhost	5432	evo_user / evo_password
Redis	localhost	6379

# Apagar todo lo anterior (si hay algo)
docker-compose -f docker-compose-n8n-evolutionapi.yml down

# Levantar la nueva arquitectura con Postgres
docker-compose -f docker-compose-n8n-evolutionapi.yml up -d

# Ver los contenedores
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

# Ver los logs de evolution api
docker logs evolution_api

# Crear instancia manual o desde evolution api
curl --request POST 'http://localhost:8080/instance/create' \
--header 'apikey: aybar_secure_token_2025' \
--header 'Content-Type: application/json' \
--data '{
    "instanceName": "Aybar",
    "token": "aybar_2025",
    "qrcode": true
}'

# Levantar ngrok para n8n
C:\laragon\bin\ngrok\ngrok.exe http 5678

# Levantar ngrok para laravel
C:\laragon\bin\ngrok\ngrok.exe http 8000 --region=sa

https://darcie-semitropical-todd.ngrok-free.dev/webhook-test/enviar-invitaciones

http://host.docker.internal:8000/api/entrega-fest/marcar-enviado