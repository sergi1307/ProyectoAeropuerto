# 🌍 API RESTful para la Gestión de Conexiones Aéreas en Europa ✈️

## 📄 Descripción General
Este proyecto tiene como objetivo crear una **API RESTful** utilizando **PHP** para gestionar y consultar información sobre **ciudades y aeropuertos en Europa**.  
La aplicación permite conocer la disponibilidad de **conexiones directas e indirectas (con escalas)** entre ciudades, con persistencia de datos en **MySQL**.

---

## 🚀 Fases del Proyecto

### **Fase 1: API con Persistencia en Base de Datos**
- **Objetivo:** Crear una API básica para consultar ciudades y aeropuertos.  
- **Endpoints:**
  - `GET /cities` : Listado de todas las ciudades.  
  - `GET /city/:id` : Detalles de una ciudad, incluyendo sus aeropuertos.  
  - `GET /airports` : Listado de todos los aeropuertos.  
  - `GET /airport/:id` : Detalles de un aeropuerto específico.  
- **Persistencia:** MySQL, con tablas para ciudades y aeropuertos.  
- **Tecnologías:** PHP + MySQL  

---

### **Fase 2: Conexiones Directas entre Aeropuertos**
- **Objetivo:** Permitir consultar las **conexiones directas** entre aeropuertos.  
- **Endpoints:**
  - `GET /connections` : Todas las conexiones directas.  
  - `GET /connections/:from/:to` : Conexión directa entre dos ciudades y aeropuertos involucrados.  
  - `GET /airport/:id/connections` : Todas las conexiones directas desde un aeropuerto.  
- **Persistencia:** MySQL, con tabla de **conexiones directas**.  

---

### **Fase 3: Conexiones con Escalas (Indirectas)**
- **Objetivo:** Identificar rutas que involucren **una o más escalas**.  
- **Endpoints:**
  - `GET /connections/with-stops/:from/:to` : Rutas entre ciudades con escalas.  
  - `GET /airport/:id/connections/with-stops` : Conexiones con escalas desde un aeropuerto.  
- **Lógica:** Algoritmo de **Búsqueda en Anchura (BFS)** o **Búsqueda en Profundidad (DFS)** para identificar rutas indirectas.  
- **Persistencia:** MySQL, extendiendo la tabla de conexiones para soportar rutas con escalas.  

---

## 🗂 Estructura de la Base de Datos

**Tabla: cities**
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id*    | INT  | Identificador único |
| nombre  | VARCHAR | Nombre de la ciudad |
| pais | VARCHAR | País de la ciudad |
| poblacion | INT | Número total de habitantes |
| zonaHoraria | VARCHAR | Zona horaria a la que pertenece |
| latitud | INT | Latitud geográfica |
| longitud | INT | Longitud geográfica |
| elevación | INT | Altura sobre el nivel del mar |
| anyoFundacion | INT | Año en que se fundó la ciudad |

**Tabla: airports**
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id*    | INT  | Identificador único |
| name  | VARCHAR | Nombre del aeropuerto |
| iata | VARCHAR | Código IATA del aeropuerto |
| city_id | INT | Relación con la ciudad |
| tipo | ENUM | Regional, Nacional e Internacional |
| latitud | INT | Latitud geográfica |
| longitud | INT | Longitud geográfica |
| elevacion | INT | Altura sobre el nivel del mar |
| terminales | INT | Número total de terminales |
| anyoApertura | INT | Año en que se inauguró el aeropuerto |

**Tabla: connections**
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id    | INT  | Identificador único |
| origin_airport_id | INT | Aeropuerto de origen |
| destination_airport_id | INT | Aeropuerto de destino |
| type  | ENUM('direct','indirect') | Tipo de conexión |

---

## 🛠 Tecnologías Usadas
- **Lenguaje Backend:** PHP  
- **Base de Datos:** MySQL  
- **Documentación y Pruebas:** Postman  
- **Algoritmos:** DFS/BFS para rutas con escalas  

---

## 👥 Trabajo en Grupo
El proyecto se puede dividir en áreas:  
1. **Backend (API):** Desarrollo de endpoints y lógica de negocio.  
2. **Persistencia de Datos:** Diseño de la base de datos y consultas SQL.  
3. **Pruebas y Documentación:** Postman para probar y documentar la API.  

---

## ⚡ Próximos Pasos
- Optimizar consultas para rutas con escalas.  
- Implementar paginación y filtros por país o ciudad.  
- Posible integración con servicios externos de aeropuertos.  

---

## 📌 Autor
**Equipo de Desarrollo:** Sergi Molina Barberà

