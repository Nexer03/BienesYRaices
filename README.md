<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Crear usuario admin
php artisan tinker
## todo el comando de abajo junto
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'admin',
    'email' => 'admin@gmail.com',
    'password' => Hash::make('admin'),
    'role' => 'admin',
]);


## Instalar el proyecto por primera vez
## Notas
Si no se muestran las imagenes usa este comando: php artisan storage:link
Comando para rellenar las amenidades:  php artisan db:seed
Añadir en env. esta linea: GOOGLE_MAPS_API_KEY=esta en discord
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

## Séptimo paso
ya configurado el archivo .env y la base de datos creada, ejecutamos este comando: php artisan migrate

y con eso quedaría la base de datos
psd. si se usara JavaScript se usara este comando: npm install


## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


& "D:\mysql\mysql-8.0.43-winx64\bin\mysql.exe" -h 127.0.0.1 -P 3306 -u root -p laravel

dd($request->all());
