## Instalar el proyecto por primera vez
## Notas
Si no se muestran las imagenes usa este comando: php artisan storage:link
Comando para rellenar las amenidades:  php artisan db:seed
Añadir en env. esta linea: GOOGLE_MAPS_API_KEY=AIzaSyA8dA4hXXBwE_U0-ogf29ABgzuALtN7ORw
## Primer paso
En la linea de comandos ejecutar: git clone -b develop git@github.com:Nexer03/BienesYRaices.git
## Segundo paso
en la misma linea de comando ejecutar para entrar en la carpeta: cd .\BienesYRaices\
## Tercer paso
Ejecutar en la misma linea: cp .env.example .env
## Cuarto paso
Ejecutar este comando en la misma linea para preparar el composer: composer install
## Quinto paso
Generamos la llave con el comando: php artisan key:generate
## Sexto paso 
Modificamos el archivo .env y ajustamos la base de datos según nuestros ajustes(Se debe crear una db vacia y esa misma se pone en el nombre)
Ejemplo:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bienes_raices_local # Tu nombre de BD local
DB_USERNAME=root                # Tu usuario de BD
DB_PASSWORD=                    # Tu contraseña de BD
