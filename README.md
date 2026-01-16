<h1 align="center">Solitario ♠️♥️♦️</h1>

<p align="center">
  <a href="http://localhost:3000"><img alt="App" src="https://img.shields.io/badge/App-Localhost%3A3000-36c?logo=google-chrome&logoColor=white"></a>
  <img alt="Docker" src="https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white">
  <img alt="PostgreSQL" src="https://img.shields.io/badge/PostgreSQL-Ready-4169E1?logo=postgresql&logoColor=white">
</p>

<br/>

## Collaborators ✨
<a href="https://github.com/raulgamallo/solitario/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=raulgamallo/solitario" />
</a>

---

## Environment
### Dependencies
- Docker

<details>
<summary><strong>Variables de entorno (.env)</strong></summary>

Has de crear un .env en la raíz del proyecto con las siguientes claves = "valor".
>Si no creas el .env, al iniciar el devcontainer te dará error.

```ini
POSTGRES_USER = "your_username"
POSTGRES_PASSWORD = "your_password"
POSTGRES_DB = "your_database"
JWT_SECRET = "d57267fd698391c096c16b6c3cbb401cb8f5e3d616ce881ee5725c45fa0aba04"
```

</details>

Una vez creado el .env has de ejecutar el siguiente comando en la raíz del repositorio:
```powershell
docker compose up
```

Podrás acceder a la app desde [http://localhost:3000](http://localhost:3000)

### Postgres
Si estás creando un entorno nuevo, el container de Postgres se encargará de obtener los valores del .env y crear la base de datos y el usuario y contraseña correspondientes.

https://www.figma.com/proto/Jez37MseIWTv0H4kK3Eaj7/solitario?page-id=0%3A1&node-id=3-130872&viewport=298%2C-1072%2C0.59&t=Tz0Yhln0DRCQvLDE-1&scaling=min-zoom&content-scaling=fixed&starting-point-node-id=3%3A209&show-proto-sidebar=1