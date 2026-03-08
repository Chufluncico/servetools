# Estado Actual Git – Servetools

## 🖥 Entornos implicados

### 1️⃣ Windows (trabajo)

- Repositorio inicializado correctamente.
- Conectado a GitHub.
- Rama `main` subida.
- Conflicto inicial de `.gitignore` resuelto.
- Upstream configurado correctamente.
- GitHub contiene esta versión (más antigua).

---

### 2️⃣ Linux (casa)

- Proyecto más avanzado que el de Windows.
- Inicialmente no era un repositorio Git.
- Git configurado globalmente (`user.name`, `user.email`).
- Autenticación configurada mediante HTTPS + Personal Access Token.
- Remoto `origin` configurado correctamente: https://github.com/Chufluncico/servetools.git
- Al ejecutar `git pull origin main` aparece aviso:
> Hay archivos locales que serían sobreescritos al fusionar.

---

## 📌 Situación Actual

Existen dos historiales distintos:

- GitHub → versión Windows (antigua)
- Linux → versión más avanzada (la buena)

Se ha decidido que la versión válida y oficial es la de Linux.

---

## 🔍 Estado Técnico

✔ Git configurado en ambos equipos  
✔ Autenticación funcionando  
✔ Remotos correctamente definidos  
✔ No hay corrupción del repositorio  
✔ No hay pérdida de datos  
✔ Solo falta unificar historiales  

---

## 🚀 Plan Pendiente (Unificación Definitiva)

En Linux:

1. Crear copia de seguridad:

 ```bash
 cp -r servetools servetools_backup
 ```


---

Si mañana quiere, podemos añadir también:

- Estrategia definitiva de ramas (`main` / `develop`)
- Procedimiento estándar multi-equipo
- Checklist de despliegue a servidor hospitalario

Por hoy queda documentado correctamente.