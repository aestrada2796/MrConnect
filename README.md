<p align="center">
    <a href="https://www.multirumbo.com" target="_blank">
        <img src="https://business.multirumbo.com/image/logo/marca_horizontal_morado.svg" width="300" alt="Logo Empresa DOBLECLICK.COM">
    </a>
</p>

# MultiRumbo Connect

[<img alt="Deployed with FTP Deploy Action" src="https://img.shields.io/badge/Version-0.0.1-brightgreen?style=flat">](https://plenus.tecsodevs.com)
[<img alt="Deployed with FTP Deploy Action" src="https://img.shields.io/badge/PHP-8.*-brightgreen?style=flat&logo=php">](https://www.php.net)
[<img alt="Deployed with FTP Deploy Action" src="https://img.shields.io/badge/GraphQL--brightgreen?style=flat&logo=GraphQL">](https://www.php.net)

## Instalación

### Vendor

```bash
composer require aestrada2796/mrconnect
```

### Servers

| Entorno    | Servidor                        |
|------------|---------------------------------|
| Test       | https://www.test.multirumbo.com |
| Production | https://www.multirumbo.com      |

### Endpoints

- login
- topup
- topup-card
- parcel-service
- clapzi

### Uso

- La function del login no es necesario usarla pues el resto de las funciones verifican el token antes ejecutar la
  función.

```php
Query::make("login")->login();
```

- En `make` ponemos el endpoint que se va a consultar Ej: `Query::make("topup")`
- Tenemos dos funciones para la consulta a los endpoint de GraphQL

1. Si tenemos un dominio avanzado de esta tecnología simplemente en la función `->query` le pasamos la consulta, se pueden agregar tanta consultas como sean necesarias.

```php
Query::make("")
    ->query('users(id: "5677f026-b5c6-474b-a927-6e90afd12d16"){ id,name }')
    ->send();
```

2. Este es más sencillo donde pasamos a la función `->function` los paramentros necesarios, se pueden agregar tanta funciones como sean necesarias


| Parámetro | Significado                       | Requerido |
|-----------|-----------------------------------|-----------|
| name      | Nombre de la query a consultar    | Si        |
| fields    | Campos a devolver por la consulta | Si        |
| filters   | Filtros de la consulta            | No        |

```php
Query::make("")
    ->function(
        'users',
        'id,name,roles{name}',
        'id: "15e2c1c9-ba99-468e-a008-547d0dc634c8"'
    )
    ->send();
```