<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Radares de tráfico en España | Localizador para evitar multas | AHORRA UNA MULTA</title>
  
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  
  <!-- Meta descripción -->
  <meta name="description" content="Ubicación de radares de tráfico en Cataluña: evita multas con información actualizada sobre radares fijos, móviles y de tramo en un mapa interactivo.">

  <!-- Meta keywords (menos relevante, pero opcional) -->
  <meta name="keywords" content="radares Cataluña, localizador radares tráfico, radares fijos móviles, radares de tramo, radares remolque, evitar multas Cataluña, mapa radares tiempo real">
  
  <!-- Canonical -->
  <link rel="canonical" href="https://ahorraunamulta.com/">

  <!-- Open Graph (SEO para redes sociales) -->
  <meta property="og:title" content="Localizador de Radares en Cataluña | Ahorra Multas de Tráfico">
  <meta property="og:description" content="Consulta en tiempo real los radares de tráfico en Cataluña y evita multas innecesarias.">
  <meta property="og:image" content="https://ahorraunamulta.com/imagen-radares-cataluna.jpg">
  <meta property="og:url" content="https://ahorraunamulta.com">
  <meta property="og:type" content="website">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Localizador de Radares en Cataluña">
  <meta name="twitter:description" content="Descubre dónde están los radares en Cataluña y ahorra en multas. Información actualizada de radares fijos, móviles y de tramo.">
  <meta name="twitter:image" content="https://ahorraunamulta.com/imagen-radares-cataluna.jpg">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="https://ahorraunamulta.com/favicon.png" />

  <!-- Schema Markup para posicionamiento local -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Website",
    "name": "AhorraUnaMulta",
    "url": "https://ahorraunamulta.com",
    "description": "Encuentra los radares de tráfico en Cataluña y evita multas. Mapa interactivo con información actualizada.",
    "publisher": {
      "@type": "Organization",
      "name": "AhorraUnaMulta",
      "logo": "https://ahorraunamulta.com/logo.png"
    }
  }
  </script>

  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
  
  body {
      margin: 0;
      font-family: Arial, sans-serif;
      overflow: hidden; /* Evita el scroll mientras aparece la pantalla de carga */
    }
    
    /* Estilo de la pantalla de carga */
    #preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: #007bff;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      flex-direction: column;
      transition: opacity 8.0s ease, visibility 8.0s ease;
    }

    #preloader img {
      width: 100px;
      height: auto;
      margin-bottom: 20px;
      animation: fadeIn 1.5s infinite ease-in-out;
    }

    #preloader h1 {
      color: white;
      font-size: 24px;
      margin: 0;
      animation: pulse 1.5s infinite;
    }

    @keyframes fadeIn {
      0%, 100% { opacity: 0.3; }
      50% { opacity: 1; }
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    /* Cuando el preloader esté oculto */
    .hidden {
      opacity: 0;
      visibility: hidden;
    }
  
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f7f7f7;
    }

    #map {
      width: 100%;
      height: 100vh;
    }

    .map-button {
      position: absolute;
      bottom: 20px;
      font-size: 20px;
      width: 60px;
      height: 60px;
      border: none;
      border-radius: 50%;
      cursor: pointer;
      z-index: 1000;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .map-button:hover {
      transform: scale(1.1);
      box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
    }

    #addRadarButton {
      background-color: #ffe800;
      color: white;
      left: 100px;
    }

    #addRadarButton.active {
      background-color: #28a745;
    }

    #radarForm {
      position: absolute;
      top: 10%;
      left: 50%;
      transform: translateX(-50%);
      background-color: #ffffff;
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
      z-index: 1001;
      width: 90%;
      max-width: 400px;
      font-size: 16px;
      display: none;
    }

    #radarForm label {
      font-weight: bold;
      margin-bottom: 5px;
      display: block;
    }

    #radarForm input,
    #radarForm select {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
    }

    #radarForm button {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 10px;
    }

    #radarForm .cancel {
      background-color: #ff4d4d;
      color: white;
    }

    #radarForm .save {
      background-color: #007bff;
      color: white;
    }

    .popup-button {
      margin-top: 10px;
      padding: 5px 10px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .popup-button.active {
      background-color: #28a745;
      color: white;
    }

    .popup-button.inactive {
      background-color: #ff4d4d;
      color: white;
    }
    
    button2 {
      background-color: #ff4d4d;
      color: white;
      border: none;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      position: fixed;
      bottom: 20px;
      left: 20px;
      cursor: pointer;
      font-size: 16px;
      display: flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      z-index: 1000;
    }

    button2:hover {
      background-color: #fc2828;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      text-align: center;
    }

    .modal-content h2 {
      margin: 0 0 10px;
    }

    .modal-content p {
      margin: 10px 0;
    }

    .close {
      background: #ff4d4d;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 50%;
      font-size: 16px;
      cursor: pointer;
      float: right;
    }

    .close:hover {
      background: #cc0000;
    }
    
    .list-container {
      position: fixed;
      right: 0;
      top: 0;
      width: 300px;
      height: 100vh;
      background-color: #f9f9f9;
      border-left: 2px solid #ccc;
      padding: 20px;
      overflow-y: auto;
      display: none; /* Ocultamos el listado por defecto */
      flex-direction: column;
      z-index: 1200;
    }
    .list-container h2 {
      margin-top: 0;
      font-size: 1.5rem;
      text-align: center;
      z-index: 1;
    }
    .list-item {
      padding: 10px;
      margin: 10px 0;
      background: #e6e6e6;
      border-radius: 5px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .list-item h3 {
      margin: 0;
      font-size: 1.2rem;
    }
    .list-item p {
      margin: 5px 0;
      font-size: 0.9rem;
    }
    .toggle-button {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
      z-index: 1500;
    }
    .toggle-button:hover {
      background-color: #0056b3;
    }
    
    .radar-section {
    margin-top: 10px;
    display: block; /* Mostrar por defecto */
  }

  .toggle-button-section {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    margin-bottom: 10px;
    transition: background-color 0.2s;
  }

  .toggle-button-section:hover {
    background-color: #0056b3;
  }
  
  .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: none; /* Oculto inicialmente */
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .popup {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      width: 90%;
      max-width: 400px;
      padding: 20px;
      text-align: center;
    }

    .popup h2 {
      font-size: 1.5rem;
      margin-bottom: 10px;
    }

    .popup p {
      font-size: 1rem;
      margin-bottom: 20px;
    }

    .popup button {
      background-color: #007BFF;
      color: #fff;
      border: none;
      padding: 10px 20px;
      font-size: 1rem;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .popup button:hover {
      background-color: #0056b3;
    }
    
    /* Contenedor general del filtro */
.filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  padding: 1rem;
  margin-bottom: 1.5rem;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Etiquetas y selectores */
.filter-container label {
  font-weight: bold;
  color: #333;
}

.filter-container select {
  padding: 0.5rem 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #fff;
  font-size: 1rem;
  color: #333;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.filter-container select:hover {
  border-color: #007bff;
  box-shadow: 0 0 4px rgba(0, 123, 255, 0.3);
}

.filter-container select:focus {
  border-color: #0056b3;
  outline: none;
  box-shadow: 0 0 4px rgba(0, 86, 179, 0.5);
}

/* Botones o checkboxes (si los añades más tarde) */
.filter-container button,
.filter-container input[type="checkbox"] {
  cursor: pointer;
}

/* Contenedores de listas de radares */
#active-list,
#inactive-list {
  margin-top: 1.5rem;
}

.list-item {
  padding: 1rem;
  margin-bottom: 1rem;
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease, box-shadow 0.3s ease;
}

.list-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Botón para mostrar/ocultar la leyenda */
#legend-button {
  position: absolute;
  bottom: 20px;
  right: 20px;
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  transition: background-color 0.3s ease;
  z-index: 1000;
}

#legend-button:hover {
  background-color: #0056b3;
}

/* Panel de la leyenda */
.legend-panel {
  position: absolute;
  bottom: 70px; /* Asegura que no se superponga con el botón */
  right: 20px;
  background: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  font-size: 14px;
  line-height: 1.5;
  z-index: 1000;
  width: 220px;
}


.legend-panel h4 {
  margin: 0 0 10px;
  font-size: 16px;
  font-weight: bold;
}

.legend-panel ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.legend-panel li {
  display: flex;
  align-items: center;
  margin-bottom: 5px;
}

.legend-icon {
  display: inline-block;
  width: 20px;
  height: 20px;
  margin-right: 10px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

/* BOTON LOGIN */

.buttonLogin {
      display: inline-block;
      width: 80px; /* Ancho del botón */
      height: 50px; /* Alto del botón */
      line-height: 50px; /* Centrado del texto vertical */
      border-radius: 100px; /* Bordes ligeramente redondeados */
      background-color: #007bff; /* Color de fondo */
      color: white; /* Color del texto */
      text-align: center;
      text-decoration: none;
      font-size: 16px; /* Tamaño del texto */
      font-weight: bold; /* Texto en negrita */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Sombra */
      transition: all 0.3s ease;
      position: fixed; /* Fijo en la pantalla */
      top: 50%; /* Centrado vertical */
      right: 20px; /* Margen derecho */
      transform: translateY(-50%); /* Ajuste para centrar perfectamente */
      z-index: 999; /* Siempre por encima de otros elementos */
    }

    .buttonLogin:hover {
      background-color: #0056b3; /* Color al pasar el ratón */
      transform: translateY(-50%) scale(1.1); /* Escalado en hover */
    }

    .buttonLogin:active {
      background-color: #004085; /* Color al hacer clic */
      transform: translateY(-50%) scale(0.95); /* Efecto al pulsar */
    }
    
    .donation-section {
  padding: 20px;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 8px;
  text-align: center;
  max-width: 400px;
  margin: auto;
  font-family: Arial, sans-serif;
}

.donation-section h3 {
  margin-bottom: 15px;
  font-size: 18px;
}

.donation-section p {
  margin: 10px 0;
  font-size: 16px;
}

.bizum-info {
  margin: 15px 0;
}

.bizum-number {
  font-size: 20px;
  font-weight: bold;
  color: #007bff;
  margin: 10px 0;
}  

  /* Ajustar el control de capas un poco más abajo y a la izquierda */
  .leaflet-control-layers {
    bottom: 180px !important; /* Mueve el control 40px hacia arriba desde la parte inferior */
    right: 01px !important;  /* Mueve el control 20px hacia la izquierda desde la parte derecha */
  }
  
  #toggleInactiveRadarsButton {
  background-color: #4CAF50; /* Color de fondo verde */
  color: white; /* Texto en blanco */
  border: none; /* Eliminar el borde */
  border-radius: 5px; /* Bordes redondeados */
  padding: 10px 20px; /* Espaciado interno */
  font-size: 16px; /* Tamaño de texto */
  cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
  transition: background-color 0.3s ease, transform 0.2s ease; /* Animación para cambios de color y al presionar */
}

#toggleInactiveRadarsButton:hover {
  background-color: #45a049; /* Color de fondo más oscuro al pasar el cursor */
}

#toggleInactiveRadarsButton:active {
  transform: scale(0.98); /* Efecto de presionar el botón */
}

/* Estilo general para los botones */
.button-voto {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  padding: 5px 10px;
  font-size: 16px;
  font-weight: bold;
  border-radius: 30px;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
  outline: none;
  text-align: center;
  margin: 5px;
}

/* Estilo para el botón de voto positivo */
.button-voto-positivo {
  background-color: #4CAF50; /* verde */
  color: white;
}

.button-voto-positivo:hover {
  background-color: #45a049;
  transform: translateY(-2px);
}

.button-voto-positivo:active {
  background-color: #3e8e41;
  transform: translateY(0);
}

.button-voto-positivo:disabled {
  background-color: #b2d8b6;
  cursor: not-allowed;
}

/* Estilo para el botón de voto negativo */
.button-voto-negativo {
  background-color: #f44336; /* rojo */
  color: white;
}

.button-voto-negativo:hover {
  background-color: #e53935;
  transform: translateY(-2px);
}

.button-voto-negativo:active {
  background-color: #c62828;
  transform: translateY(0);
}

.button-voto-negativo:disabled {
  background-color: #f1b0b0;
  cursor: not-allowed;
}

/* Añadir sombras suaves */
.button-voto:focus {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


  </style>
</head>
<body>



<div id="preloader">
    <img src="https://ahorraunamulta.com/velocidades/default/120.png" alt="Logotipo">
    <h2>AHORRA UNA MULTA</h2><BR>
    <h1>Cargando...</h1>
  </div>
  
  <button class="toggle-button" id="toggle-button">Mostrar radares en listado</button>
  <div class="list-container" id="list-container">
  <h2>LISTADO DE RADARES</h2>
  <div>
  
  <!-- Filtro por tipo de radar -->
  <div class="filter-container">
  <div>
    <label for="radar-type-filter">Filtrar por tipo de radar:</label>
    <select id="radar-type-filter">
      <option value="">Todos</option>
      <option value="Móvil">Móvil</option>
      <option value="Fijo">Fijo</option>
      <option value="Tramo">Tramo</option>
      <option value="Remolque">Remolque</option>
    </select>
  </div>
</div>
  
    <button class="toggle-button-section" id="toggle-active">Mostrar/Ocultar Radares Activos</button>
    <div id="active-list" class="radar-section">
      <h3>Radares Activos</h3>
      <!-- Contenido dinámico de radares activos -->
    </div>
  </div>
  <div>
    <button class="toggle-button-section" id="toggle-inactive">Mostrar/Ocultar Radares Inactivos</button>
    <div id="inactive-list" class="radar-section" style="display: none;">
      <h3>Radares Inactivos</h3>
      <!-- Contenido dinámico de radares inactivos -->
    </div>
  </div>
</div>
  
   <script>
    // Espera a que la página esté completamente cargada
    window.addEventListener("load", function () {
      const preloader = document.getElementById("preloader");
      const content = document.getElementById("content");

      // Añade la clase "hidden" al preloader para que se desvanezca
      preloader.classList.add("hidden");

      // Después de la transición (0.5s), oculta el preloader completamente
      setTimeout(() => {
        preloader.style.display = "none";
      }, 2000); // Debe coincidir con el tiempo de transición
    });
  </script>
  
 <div class="popup-overlay" id="popup-overlay" style="display: none;">
  <div class="popup">
    <h2>Descargo de responsabilidad</h2>
    <p><b>Ahorra una multa</b> no se hace responsable de posibles errores en la información presentada en esta página.</p>
    <button onclick="closePopup()">Entendido</button>
  </div>
</div>

<script>
  function closePopup() {
    const popupOverlay = document.getElementById('popup-overlay');
    popupOverlay.style.display = 'none';
    // Guardar en localStorage que el popup ya fue visto
    localStorage.setItem('popupSeen', 'true');
  }

  window.addEventListener('load', () => {
    // Mostrar el popup solo si no se ha visto antes
    if (!localStorage.getItem('popupSeen')) {
      setTimeout(() => {
        document.getElementById('popup-overlay').style.display = 'flex';
      }, 2500); // Mostrar después de 2.5 segundos
    }
  });
</script>

  
  <button2 id="helpButton2">❔</button2>

  <!-- Mapa -->
<div id="map"></div>
  
  <button id="addRadarButton" class="map-button">➕</button>
  <a href="/login" class="buttonLogin">ADMIN</a>
  
  <script type="module">
    // Importar las funciones necesarias
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.17.2/firebase-app.js";
    import { getDatabase, ref, get, onValue } from "https://www.gstatic.com/firebasejs/9.17.2/firebase-database.js";
    
    
   // Configuración de Firebase
    const firebaseConfig = {
      apiKey: "AIzaSyDNCBnqAAcdV3kqx8hN-uMyqSkzIzV4DXc",
      authDomain: "radares-bcn.firebaseapp.com",
      databaseURL: "https://radares-bcn-default-rtdb.europe-west1.firebasedatabase.app",
      projectId: "radares-bcn",
      storageBucket: "radares-bcn.appspot.com",
      messagingSenderId: "892778900332",
      appId: "1:892778900332:web:f3c5353d981dda7b4ba149",
      measurementId: "G-C1GT8Q96ZJ",
    };

    // Inicializar Firebase
const app = initializeApp(firebaseConfig);
const database = getDatabase(app);

// Referencia a la base de datos
const dbRef = ref(database, "radares"); // Ruta 'radares' en tu base de datos

// Seleccionar los contenedores y el filtro
const activeListContainer = document.getElementById("active-list");
const inactiveListContainer = document.getElementById("inactive-list");
const typeFilter = document.getElementById("radar-type-filter");

// Estado para el filtro de tipo
let selectedType = ""; // Todos por defecto

// Actualizar el filtro de tipo cuando cambie el select
typeFilter.addEventListener("change", (event) => {
  selectedType = event.target.value;
  renderRadars(); // Renderizar nuevamente los radares
});

// Función para renderizar radares
const renderRadars = () => {
  onValue(dbRef, (snapshot) => {
    const data = snapshot.val();

    activeListContainer.innerHTML = ""; // Limpiar contenido previo de activos
    inactiveListContainer.innerHTML = ""; // Limpiar contenido previo de inactivos

    // Convertir el objeto en un array para ordenarlo
    const radarsArray = Object.keys(data).map((key) => ({
      ...data[key],
      key, // Incluye la clave si la necesitas
    }));

    // Ordenar por fecha de modificación (descendente)
    radarsArray.sort((a, b) => {
      const dateA = a.last_updated ? new Date(a.last_updated) : 0;
      const dateB = b.last_updated ? new Date(b.last_updated) : 0;
      return dateB - dateA; // Más reciente primero
    });

    // Filtrar y renderizar los radares
    radarsArray.forEach((radar) => {
      // Aplicar filtro de tipo si está definido
      if (selectedType && radar.radarType !== selectedType) return;

      // Crear un elemento para cada radar
      const item = document.createElement("div");
      item.className = "list-item";

      // Convertir la fecha a un formato legible
      const lastUpdatedFormatted = radar.last_updated
        ? new Date(radar.last_updated).toLocaleString() // Formato legible
        : "No disponible";

      // Mostrar la información del radar
      item.innerHTML = `
        <h3>Radar: ${radar.radarType || "Desconocido"}</h3>
        <p><strong>Latitud:</strong> ${radar.lat || "N/A"}</p>
        <p><strong>Longitud:</strong> ${radar.lng || "N/A"}</p>
        <p><strong>Carretera:</strong> ${radar.road || "N/A"}</p>
        <p><strong>PK:</strong> ${radar.pk || "N/A"}</p>
        <p><strong>Dirección:</strong> ${radar.direction || "N/A"}</p>
        <p><strong>Velocidad:</strong> ${radar.speed || "N/A"} km/h</p>
        <p><strong>Estado:</strong> ${
          radar.status === "active" ? "Activo" : "Inactivo"
        }</p>
        <p><b>Última modificación:</b> ${lastUpdatedFormatted}</p>
      `;

      // Añadir al contenedor correspondiente
      if (radar.status === "active") {
        activeListContainer.appendChild(item);
      } else {
        inactiveListContainer.appendChild(item);
      }
    });
  });
};

// Inicializar la visualización de los radares
renderRadars();


// Funcionalidad para mostrar/ocultar secciones
const toggleActive = document.getElementById("toggle-active");
const toggleInactive = document.getElementById("toggle-inactive");

toggleActive.addEventListener("click", () => {
  const activeSection = document.getElementById("active-list");
  activeSection.style.display =
    activeSection.style.display === "none" ? "block" : "none";
});

toggleInactive.addEventListener("click", () => {
  const inactiveSection = document.getElementById("inactive-list");
  inactiveSection.style.display =
    inactiveSection.style.display === "none" ? "block" : "none";
});

    // Funcionalidad para mostrar/ocultar el listado
    const toggleButton = document.getElementById('toggle-button');
    const listDiv = document.getElementById('list-container');

    toggleButton.addEventListener('click', () => {
      if (listDiv.style.display === 'none' || listDiv.style.display === '') {
        listDiv.style.display = 'flex';
        toggleButton.textContent = 'Ocultar listado';
      } else {
        listDiv.style.display = 'none';
        toggleButton.textContent = 'Mostrar listado';
      }
    });
  </script>
  
  <script>
  document.addEventListener("DOMContentLoaded", () => {
  const legendButton = document.getElementById("legend-button");
  const legendPanel = document.getElementById("legend-panel");

  // Alternar visibilidad del panel
  legendButton.addEventListener("click", () => {
    if (legendPanel.style.display === "none" || legendPanel.style.display === "") {
      legendPanel.style.display = "block";
    } else {
      legendPanel.style.display = "none";
    }
  });
});
  </script>

  <div id="radarForm">
    <label for="radarType">Tipo de Radar</label>
    <select id="radarType">
      <option value="" disabled selected>Selecciona...</option>
      <option value="Remolque">Remolque</option>
      <option value="Fijo">Fijo</option>
      <option value="Móvil">Móvil</option>
      <option value="Tramo">Tramo</option>
    </select>
    <label for="road">Carretera/Autopista</label>
    <input type="text" id="road" placeholder="Ej. AP-7" />
    <label for="pk">PK</label>
    <input type="text" id="pk" placeholder="Ej. 155" />
    <label for="direction">Dirección</label>
    <input type="text" id="direction" placeholder="Ej. Sentido Tarragona" />
    <label for="speed">Velocidad (km/h)</label>
    <input type="number" id="speed" placeholder="Ej. 120" />
    <button id="saveRadarButton" class="save">Guardar Radar</button>
    <button id="cancelRadarButton" class="cancel">Cancelar</button>
  </div>
  
  <div class="modal" id="helpModal">
    <div class="modal-content">
      <button class="close" id="closeModal">&times;</button>
      <h2>Ayuda</h2>
      <p>AhorraUnaMulta.com es una herramienta intuitiva que te ayuda a identificar radares de tráfico en tu zona y conocer las velocidades permitidas, permitiéndote evitar multas de tránsito de manera eficaz.</p>
      <p>Presionando el botón +, puedes añadir nuevos radares</p>
      <p>Presionando sobre los radares, puedes desactivarlos.</p>
      <p>Esto permite saber que ese radar se encuentra en ese momento averiado o ha sido reubicado en otra ubicación.</p>
      <p>Si necesitas ayuda, puedes contactarnos en <a href="mailto:soporte@ahorraunamulta.com">soporte@ahorraunamulta.com</a></p>
      <br>
      <p><strong>Última actualización:</strong> Enero de 2025</p>
      
   <!--   <div class="donation-section">
  <h3>Ayúdanos con una aportación</h3>
  <p>Puedes realizar tu aportación a través de Bizum utilizando el siguiente número:</p>
  <div class="bizum-info">
    <p class="bizum-number">681 102 388</p>
  </div>
  <p>Nos ayudará a mantener el servidor, el dominio, las base de datos y algunos servicios adicionales</p>
  <p>¡Gracias por tu apoyo!</p>
</div> -->
      
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-database-compat.js"></script>
  
  <script>
    const helpButton = document.getElementById('helpButton2');
    const helpModal = document.getElementById('helpModal');
    const closeModal = document.getElementById('closeModal');

    helpButton.addEventListener('click', () => {
      helpModal.style.display = 'flex';
    });

    closeModal.addEventListener('click', () => {
      helpModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
      if (event.target === helpModal) {
        helpModal.style.display = 'none';
      }
    });
  </script>
  
  <script>
// Configuración de Firebase
const firebaseConfig = {
  apiKey: "AIzaSyDNCBnqAAcdV3kqx8hN-uMyqSkzIzV4DXc",
  authDomain: "radares-bcn.firebaseapp.com",
  databaseURL: "https://radares-bcn-default-rtdb.europe-west1.firebasedatabase.app",
  projectId: "radares-bcn",
  storageBucket: "radares-bcn.appspot.com",
  messagingSenderId: "892778900332",
  appId: "1:892778900332:web:f3c5353d981dda7b4ba149",
  measurementId: "G-C1GT8Q96ZJ",
};

firebase.initializeApp(firebaseConfig);
const db = firebase.database();

const map = L.map("map").setView([41.3784, 2.1927], 10);
const osmLayer = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);
const satelliteLayer = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}");

L.control.layers({ "Mapa estándar": osmLayer, "Vista satélite": satelliteLayer }, {}, { position: 'bottomright' }).addTo(map);

let showInactiveRadars = false;

let tempMarker = null;
addRadarButton.addEventListener("click", () => {
  addRadarButton.classList.toggle("active");
  addRadarButton.textContent = addRadarButton.classList.contains("active") ? "🚨" : "➕";
  if (!addRadarButton.classList.contains("active")) {
    radarForm.style.display = "none";
    if (tempMarker) map.removeLayer(tempMarker);
    tempMarker = null;
  }
});
map.on("click", (e) => {
  if (addRadarButton.classList.contains("active")) {
    radarForm.style.display = "block";
    if (tempMarker) map.removeLayer(tempMarker);
    tempMarker = L.marker(e.latlng).addTo(map);
  }
});
saveRadarButton.addEventListener("click", () => {
  const radarType = document.getElementById("radarType").value;
  const road = document.getElementById("road").value;
  const pk = document.getElementById("pk").value;
  const direction = document.getElementById("direction").value;
  const speed = document.getElementById("speed").value;
  if (radarType && road && direction && speed && tempMarker) {
    const { lat, lng } = tempMarker.getLatLng();
    const newRadar = db.ref("radares").push();
    newRadar.set({
      radarType,
      road,
      pk,
      direction,
      speed,
      lat,
      lng,
      votos_positivos: 0,
      votos_negativos: 0,
      status: "active",
      last_updated: new Date().toISOString(),
    });
    alert("Radar guardado con éxito");
    radarForm.style.display = "none";
    if (tempMarker) map.removeLayer(tempMarker);
    tempMarker = null;
    document.getElementById("radarType").value = "";
    document.getElementById("road").value = "";
    document.getElementById("pk").value = "";
    document.getElementById("direction").value = "";
    document.getElementById("speed").value = "";
  } else {
    alert("Por favor, completa todos los campos y selecciona una ubicación.");
  }
});
cancelRadarButton.addEventListener("click", () => {
  radarForm.style.display = "none";
  if (tempMarker) map.removeLayer(tempMarker);
  tempMarker = null;
});

function toggleInactiveRadars() {
  showInactiveRadars = !showInactiveRadars;
  loadRadars();
}

function loadRadars() {
  db.ref("radares").on("value", (snapshot) => {
    map.eachLayer((layer) => {
      if (layer instanceof L.Marker) {
        map.removeLayer(layer);
      }
    });

    const radars = snapshot.val();
    if (!radars) return;

    Object.entries(radars).forEach(([id, radar]) => {
      if (radar.status === "active" || showInactiveRadars) {
        addRadarMarker(map, radar, id);
      }
    });
  });
}

function addRadarMarker(map, radar, radarId) {
  const votosPositivos = radar.votos_positivos || 0;
  const votosNegativos = radar.votos_negativos || 0;
  const userVoted = localStorage.getItem(`voto_${radarId}`);

  const marker = L.marker([radar.lat, radar.lng], {
    icon: getIconByRadar(radar),
  }).addTo(map);

  marker.bindPopup(`
    <b>${radar.radarType}</b><br>
    Carretera: ${radar.road}<br>
    PK: ${radar.pk || "No disponible"}<br>
    Dirección: ${radar.direction}<br>
    Velocidad: ${radar.speed} km/h<br>
    Estado: <b>${radar.status === "active" ? "Activo" : "Inactivo"}</b><br>
    Última modificación: ${new Date(radar.last_updated).toLocaleString()}<br>
    <button id="boton_positivo_${radarId}" class="button-voto button-voto-positivo" onclick="votar('${radarId}', 'positivo')" ${userVoted ? '#' : ''}>
  👍 <span id="votos_positivo_${radarId}">${votosPositivos}</span>
</button>
<button id="boton_negativo_${radarId}" class="button-voto button-voto-negativo" onclick="votar('${radarId}', 'negativo')" ${userVoted ? '#' : ''}>
  👎 <span id="votos_negativo_${radarId}">${votosNegativos}</span>
</button>
  `);
}

function votar(radarId, tipo) {
  const today = new Date().toISOString().split('T')[0]; // Fecha actual en formato YYYY-MM-DD
  const votoKey = `voto_${radarId}`; // Clave única para cada radar

  // Verificar si el usuario ya votó hoy en este radar
  const votoGuardado = localStorage.getItem(votoKey);
  if (votoGuardado) {
    const { fecha, tipo: votoAnterior } = JSON.parse(votoGuardado);
    if (fecha === today) {
      alert(`Ya has votado en este radar hoy.`);
      return;
    }
  }

  // Registrar el voto en localStorage para evitar más votos hoy
  localStorage.setItem(votoKey, JSON.stringify({ fecha: today, tipo }));

  // Realizar la transacción de votos en Firebase
  const votosRef = db.ref(`radares/${radarId}/${tipo === "positivo" ? "votos_positivos" : "votos_negativos"}`);
  votosRef.transaction((currentVotes) => {
    const newVoteCount = (currentVotes || 0) + 1;

    // Si los votos negativos superan los 10, cambiar el estado del radar a "inactive"
    if (tipo === "negativo" && newVoteCount > 10) {
      const radarRef = db.ref(`radares/${radarId}`);
      radarRef.update({
        status: "inactive",
        last_updated: new Date().toISOString() // Actualización de la fecha de modificación
      }).then(() => {
        console.log(`Radar ${radarId} ha sido marcado como inactivo por exceder los 10 votos negativos.`);
      });
    }

    return newVoteCount;
  }).then(() => {
    // Actualizar el conteo de votos en la UI
    document.getElementById(`votos_${tipo}_${radarId}`).innerText++;

    // Actualizar la fecha de modificación en Firebase, siempre que se haya emitido un voto
    const radarRef = db.ref(`radares/${radarId}`);
    radarRef.update({
      last_updated: new Date().toISOString() // Actualización de la fecha de modificación
    }).then(() => {
      console.log(`La fecha de modificación de ${radarId} ha sido actualizada.`);
    });
  });
}

loadRadars();

// Función para obtener el icono según el estado y tipo de radar
function getIconByRadar(radar) {
  const validRadarTypes = ["Fijo", "Móvil", "Tramo", "Remolque"];
  const validRadarType = validRadarTypes.includes(radar.radarType) ? radar.radarType : "default";
  const validSpeed = radar.speed && radar.speed >= 10 && radar.speed <= 140 ? radar.speed : "default";

  const iconUrl = radar.status === "active"
    ? `https://ahorraunamulta.com/velocidades/${validRadarType}/${validSpeed}.png`
    : `https://ahorraunamulta.com/velocidades/${validRadarType}/no_activo.png`;

  return L.icon({ iconUrl, iconSize: radar.status === "active" ? [30, 30] : [25, 25] });
}

// Botón en la interfaz para alternar radares inactivos
const toggleButton = document.createElement("button");
toggleButton.innerText = "Mostrar/Ocultar Radares Inactivos";
toggleButton.style.position = "absolute";
toggleButton.style.top = "10px";
toggleButton.style.right = "10px";
toggleButton.style.padding = "10px";
toggleButton.style.background = "#007bff";
toggleButton.style.color = "white";
toggleButton.style.border = "none";
toggleButton.style.cursor = "pointer";
toggleButton.addEventListener("click", toggleInactiveRadars);

document.body.appendChild(toggleButton);

// Cargar radares al inicio
loadRadars();
</script>
  
  <!-- Botón para mostrar/ocultar la leyenda -->
<button id="legend-button" style="position: absolute; right: 10px; z-index: 1000;">
  Leyenda
</button>

<!-- Panel de la leyenda -->
<div id="legend-panel" class="legend-panel" style="display: none;">
  <h4>Tipos de Radares</h4>
  <ul>
    <li><span class="legend-icon" style="background-color: #12e408;"></span> Radar Fijo</li>
    <li><span class="legend-icon" style="background-color: #ffb400;"></span> Radar de Tramo</li>
    <li><span class="legend-icon" style="background-color: #150aec;"></span> Radar Móvil</li>
    <li><span class="legend-icon" style="background-color: red;"></span> Radar Remolque</li>
    <li><span class="legend-icon" style="background-color: white; border: 1px solid #000;"></span> Radar Inactivo</li>
    <br>
    <button id="toggleInactiveRadarsButton" onclick="toggleInactiveRadars()">Mostrar/Ocultar radares inactivos</button>
  </ul>
</div>

<?php
include 'log_ip.php';
?>
  
</body>
</html>