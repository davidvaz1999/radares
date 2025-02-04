CHANGELOG

Inicio del desarrollo, 25/01/2025

Añadidos
Conexión a Firebase:

Configuración inicial de Firebase para usar su base de datos en tiempo real.
Implementación de una estructura de datos para almacenar y gestionar información de radares.
Autenticación básica de usuarios con Firebase Authentication.

Mapa interactivo:

Visualización de radares en un mapa interactivo utilizando coordenadas almacenadas en Firebase.
Capacidad de realizar zoom y desplazamiento en el mapa para explorar áreas específicas.

Búsqueda avanzada:

Filtro de radares por tipo, ubicación y otros criterios directamente desde los datos en Firebase.
Actualización dinámica de resultados en el mapa a medida que se realizan búsquedas.

Cambios:

Optimización de consultas a Firebase:

Uso de consultas específicas para minimizar los datos descargados y mejorar el rendimiento.
Configuración de índices en Firebase para optimizar las búsquedas.

Interfaz de usuario mejorada:

Rediseño de componentes visuales para ofrecer una experiencia más intuitiva al usuario.
Mejoras en la visualización de los detalles de cada radar.
Correcciones menores.

26/01/2025

Corrección de errores en la integración Firebase-Mapa:

Solución de problemas relacionados con la sincronización entre la base de datos y los marcadores en el mapa.
Manejo adecuado de errores al cargar datos desde Firebase, evitando fallos por conexiones inestables.
Validación de datos:

Implementación de validaciones al escribir datos en Firebase para evitar inconsistencias.
Corrección de errores en las funciones de búsqueda cuando los datos no estaban completos.

27/01/2025

Funciones antisabotaje, sistema de autenticación reforzada:

Verificación de identidad a través de Firebase Authentication con medidas adicionales como detección de accesos sospechosos.

Control de acceso:
Implementación de permisos basados en roles para garantizar que solo usuarios autorizados puedan activar o desactivar radares.

Monitoreo de cambios:
Registro de todas las acciones realizadas en la base de datos (creación, edición y eliminación) con timestamps y detalles del usuario responsable.
Integración con Firestore para almacenar logs de auditoría.

Detección de comportamientos anómalos:

Identificación automática de accesos masivos o intentos de sabotaje (por ejemplo, cambios repetitivos o datos inconsistentes).
Alertas automáticas por correo o notificaciones push en caso de actividades sospechosas.

Reversión de datos:
Función para deshacer cambios recientes en caso de sabotaje, restaurando los datos desde una copia segura en Firebase.

Mejoras en la seguridad de la base de datos:

Reglas de seguridad de Firebase más estrictas, limitando operaciones según roles y condiciones predefinidas.
Encriptación de datos sensibles para proteger información crítica.

Corrección de errores en validaciones de seguridad:
Solución de problemas que permitían omitir reglas de seguridad en ciertas operaciones.
Mejora en la validación de entradas para evitar la inyección de datos maliciosos.

Desarrollo del panel de administración:

Gestión de usuarios:
Visualización de una lista de usuarios registrados, incluyendo sus roles, accesos y últimas actividades.
Funcionalidades para editar roles de usuarios, bloquear cuentas sospechosas y restablecer contraseñas.

Gestión de radares:
Interfaz intuitiva para activar o desactivar radares directamente desde el panel.
Vista detallada de cada radar, incluyendo su ubicación, tipo y datos adicionales.

Monitoreo en tiempo real:
Visualización dinámica de la actividad de la base de datos en tiempo real, con actualizaciones instantáneas desde Firebase.
Gráficos y estadísticas para analizar el uso del sistema y la distribución geográfica de los radares.

Registro de auditoría:
Historial completo de acciones realizadas en el sistema, filtrado por usuarios, fecha y tipo de acción.

28/01/2025

Mejoras en la interfaz del panel:
Uso de un diseño responsivo que adapta el panel a dispositivos móviles y de escritorio.
Incorporación de un sistema de navegación intuitivo para acceder rápidamente a las diferentes secciones (usuarios, radares, auditoría, configuración).

Corrección de errores iniciales:
Solución de fallos relacionados con la visualización de datos en tablas del panel.
Ajuste en las consultas al cargar grandes volúmenes de datos para evitar tiempos de espera elevados.

Visualización:
Añadida vista satélite

29/01/2025

Añadida función para ocultar/mostrar radares inactivos.	
Añadida funcionalidad para asignar el PK de los radares.	
Añadida opción para editar los campos de dirección, velocidad máxima, vía y PK desde el panel de administración.	
Corregidos varios errores.	

30/01/2025

Añadida opción para votar los radares. Si un radar recibe una proporción elevada de votos negativos, se inhabilita automáticamente.
Creación de un script para restablecer los votos de los radares.
Añadida opción para exportar los radares (panel de administración)
Correcciones de errores.

04/02/2025

Añadida función para poder desactivar la actualización automática del mapa en el punto de la ubicación.
Correcciones de errores.