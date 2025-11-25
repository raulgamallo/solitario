# solitario
## Environment
Has de crear un .env en la raíz del proyecto con las siguientes claves = "valor".

```ini
POSTGRES_USER = your_username
POSTGRES_PASSWORD = your_password
POSTGRES_DB = your_database
```

>Si no creas el .env, al iniciar el devcontainer te dará error.

### Postgres
Si estás creando un entorno nuevo, el container de Postgres se encargará de obtener los valores del .env y crear la base de datos y el usuario y contraseña correspondientes.
