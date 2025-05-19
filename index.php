<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Radares de tráfico en España | Localizador para evitar multas | AHORRA UNA MULTA</title>

  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

  <meta name="description" content="Ubicación de radares de tráfico en Cataluña: evita multas con información actualizada sobre radares fijos, móviles y de tramo en un mapa interactivo.">

  <meta name="keywords" content="radares Cataluña, radares Barcelona, radares Tarragona, radares Girona, radares Lleida, localizador radares tráfico, radares fijos móviles, radares de tramo, radares remolque, evitar multas Cataluña, mapa radares tiempo real">

  <link rel="canonical" href="https://ahorraunamulta.com/">

  <meta property="og:title" content="Localizador de Radares en Cataluña | Ahorra Multas de Tráfico">
  <meta property="og:description" content="Consulta en tiempo real los radares de tráfico en Cataluña y evita multas innecesarias.">
  <meta property="og:image" content="https://ahorraunamulta.com/imagen-radares-cataluna.jpg">
  <meta property="og:url" content="https://ahorraunamulta.com">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="es_ES">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Localizador de Radares en Cataluña">
  <meta name="twitter:description" content="Descubre dónde están los radares en Cataluña y ahorra en multas. Información actualizada de radares fijos, móviles y de tramo.">
  <meta name="twitter:image" content="https://ahorraunamulta.com/imagen-radares-cataluna.jpg">

  <link rel="icon" type="image/png" href="https://ahorraunamulta.com/favicon.png" />

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "AhorraUnaMulta",
    "url": "https://ahorraunamulta.com",
    "description": "Mapa interactivo con los radares de tráfico en Cataluña. Información actualizada para evitar multas.",
    "applicationCategory": "TravelApplication",
    "operatingSystem": "Web Browser",
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "EUR"
    },
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
      overflow: hidden;
  }

  body.modal-open {
    overflow: hidden;
  }

  #preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #007bff, #00bfff);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 3000;
    flex-direction: column;
    transition: opacity 0.5s ease, visibility 0.5s ease;
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

  .hidden {
    opacity: 0;
    visibility: hidden;
  }

  .leaflet-container {
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
  }

  #map {
    width: 100%;
    height: 100vh;
  }

  /* Grupo de botones de acción - Escritorio */
  @media (min-width: 769px) {
    .action-buttons {
      position: fixed;
      left: 50%;
      bottom: 30px;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
      align-items: center;
      z-index: 1000;
      background-color: rgba(255, 255, 255, 0.9);
      padding: 10px 20px;
      border-radius: 50px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      border: 1px solid #e0e0e0;
      transition: all 0.3s ease;
    }

    .action-buttons.list-visible {
      left: calc(50% - 150px);
    }
  }

  /* Grupo de botones de acción - Móvil */
  @media (max-width: 768px) {
    .action-buttons {
      position: fixed;
      bottom: 20px;
      right: 20px;
      display: flex;
      gap: 8px;
      align-items: center;
      z-index: 1000;
      flex-direction: column;
    }
  }

  /* Estilo común para todos los botones de acción */
  .action-button {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .action-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
  }

  .action-button::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 5px;
    height: 5px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 0;
    border-radius: 100%;
    transform: scale(1, 1) translate(-50%);
    transform-origin: 50% 50%;
  }

  .action-button:focus:not(:active)::after {
    animation: ripple 1s ease-out;
  }

  @keyframes ripple {
    0% {
      transform: scale(0, 0);
      opacity: 0.5;
    }
    100% {
      transform: scale(20, 20);
      opacity: 0;
    }
  }

  .action-button::before {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 14px;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
    white-space: nowrap;
  }

  .action-button:hover::before {
    opacity: 1;
  }

  /* Botón de listado */
  #toggle-button {
    background-color: #007bff;
    color: white;
    position: relative;
  }

  #toggle-button::after {
    content: '';
    position: absolute;
    top: -5px;
    right: -5px;
    width: 12px;
    height: 12px;
    background-color: #ff4d4d;
    border-radius: 50%;
    display: none;
  }

  /* Botón de añadir radar */
  #addRadarButton {
    background-color: #ffe800;
    color: white;
  }

  #addRadarButton.active {
    background-color: #28a745;
  }

  /* Botón de ayuda */
  #helpButton2 {
    background-color: #ff4d4d;
    color: white;
  }

  /* Botón de admin */
  .buttonAdmin {
    background-color: #6c757d;
    color: white;
    width: 50px;
    height: 50px;
    font-size: 14px;
    text-decoration: none;
  }

  /* Botón de login */
  .buttonLogin {
    background-color: #17a2b8;
    color: white;
    width: 50px;
    height: 50px;
    font-size: 14px;
    text-decoration: none;
  }

  /* Botón de estadísticas */
  #statsButton {
    background-color: #6f42c1;
    color: white;
    display: none;
  }

  /* Botón de añadir en ubicación actual */
  #addCurrentLocationButton {
    background-color: #ff9800;
    color: white;
  }

  /* Botón de modo conducción */
  #driveModeButton {
    background-color: #17a2b8;
    color: white;
    position: relative;
  }

  #driveModeButton.active {
    background-color: #138496;
    animation: pulse 2s infinite;
  }

  #driveModeButton span {
    font-size: 10px;
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #ff9800;
    color: white;
    border-radius: 10px;
    padding: 2px 5px;
    pointer-events: none;
    z-index: 1001;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    font-weight: bold;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
  }

  @media (max-width: 768px) {
    #driveModeButton span {
      font-size: 8px;
      padding: 1px 3px;
      top: -3px;
      right: -3px;
    }
  }

  @keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(19, 132, 150, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(19, 132, 150, 0); }
    100% { box-shadow: 0 0 0 0 rgba(19, 132, 150, 0); }
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
    z-index: 2002;
    width: 90%;
    max-width: 400px;
    font-size: 16px;
    display: none;
    border: 1px solid #007bff;
  }

  #radarForm label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
    color: #2c3e50;
  }

  #radarForm input,
  #radarForm select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: border-color 0.3s;
  }

  #radarForm input:focus,
  #radarForm select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
  }

  #radarForm button {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s;
  }

  #radarForm .cancel {
    background-color: #ff4d4d;
    color: white;
  }

  #radarForm .cancel:hover {
    background-color: #e53935;
  }

  #radarForm .save {
    background-color: #007bff;
    color: white;
  }

  #radarForm .save:hover {
    background-color: #0069d9;
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

  /* Modales - Estilos actualizados */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    z-index: 2000;
    pointer-events: auto;
  }

  .modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    text-align: center;
    border: 2px solid #007bff;
    position: relative;
    z-index: 2001;
    max-height: 90vh;
    overflow-y: auto;
  }

  .modal-content h2 {
    margin: 0 0 10px;
    color: #007bff;
  }

  .modal-content p {
    margin: 10px 0;
    line-height: 1.6;
  }

  .close {
    background: #ff4d4d;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 50%;
    font-size: 16px;
    cursor: pointer;
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2002;
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
    background-color: #f8f9fa;
    border-left: 2px solid #ccc;
    padding: 15px;
    overflow-y: auto;
    display: none;
    flex-direction: column;
    z-index: 1500;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    transform: translateX(100%);
  }

  .list-container:not([style*="display: none"]) {
    transform: translateX(0);
  }

  .list-container[style*="display: none"] {
    transform: translateX(100%);
  }

  /* Nuevos estilos mejorados para el listado */
  .list-item {
    padding: 12px 15px;
    margin: 8px 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    border-left: 5px solid #007bff;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid #e0e0e0;
  }

  .list-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background-color: #f5f9ff;
    border-left-color: #0056b3;
  }

  .list-item h3 {
    margin: 0 0 8px 0;
    font-size: 1.1rem;
    color: #2c3e50;
    display: flex;
    align-items: center;
  }

  .list-item p {
    margin: 6px 0;
    font-size: 0.9rem;
    color: #555;
    display: flex;
    align-items: center;
  }

  .list-item p strong {
    min-width: 120px;
    display: inline-block;
    color: #333;
  }

  .list-item .status-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    margin-left: 8px;
  }

  .status-active {
    background-color: #28a745;
    color: white;
  }

  .status-inactive {
    background-color: #dc3545;
    color: white;
  }

  .status-pending {
    background-color: #ffc107;
    color: #212529;
  }

  .radar-section {
    margin-top: 10px;
    display: block;
  }

  .toggle-button-section {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    margin-bottom: 15px;
    transition: all 0.2s;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .toggle-button-section:hover {
    background-color: #0056b3;
    transform: translateY(-1px);
  }

  .toggle-button-section:after {
    content: '▼';
    font-size: 0.8rem;
    transition: transform 0.2s;
  }

  .toggle-button-section.collapsed:after {
    transform: rotate(-90deg);
  }

  .list-container h2, .list-modal-content h2 {
    color: #2c3e50;
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 2000;
  }

  .popup {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 400px;
    padding: 20px;
    text-align: center;
    z-index: 2001;
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

  /* Mejoras para el sistema de filtrado */
  .filter-container {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 15px;
    margin-bottom: 1.5rem;
    background-color: #e9f2ff;
    border-radius: 10px;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 10px;
  }

  .filter-group label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #2c3e50;
  }

  .filter-group select, .filter-group input {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
  }

  .filter-message {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(255, 255, 255, 0.95);
    padding: 10px 20px;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    font-size: 14px;
    display: none;
    max-width: 90%;
    text-align: center;
  }

  .filter-message strong {
    color: #007bff;
  }

  .clear-filters {
    background: none;
    border: none;
    color: #ff4d4d;
    cursor: pointer;
    margin-left: 5px;
    font-weight: bold;
  }

  /* Botón de leyenda mejorado */
  #legend-button {
    position: fixed;
    bottom: 20px;
    left: 20px;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s, box-shadow 0.2s;
    z-index: 1000;
  }

  #legend-button:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
  }

  /* Control de capas reposicionado */
  .leaflet-control-layers {
    bottom: 180px !important;
    left: 10px !important;
    right: auto !important;
    z-index: 800 !important;
  }

  /* Botón de centrado mejorado */
  .boton-centrado {
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    font-size: 20px;
    z-index: 800;
  }

  .boton-centrado.on {
    background-color: #28a745;
  }

  .boton-centrado.off {
    background-color: #ff4d4d;
  }

  .boton-centrado:hover {
    transform: scale(1.1);
  }

  /* Botón de inactivos */
  #toggleInactiveRadarsButton {
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    width: 100%;
    margin-top: 10px;
  }

  #toggleInactiveRadarsButton:hover {
    background-color: #45a049;
  }

  #toggleInactiveRadarsButton:active {
    transform: scale(0.98);
  }

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
    min-width: 80px;
  }

  .button-voto:disabled {
    opacity: 0.7;
    transform: none !important;
  }

  .button-voto-positivo {
    background-color: #4CAF50;
    color: white;
  }

  .button-voto-positivo:hover:not(:disabled) {
    background-color: #45a049;
    transform: translateY(-2px);
  }

  .button-voto-negativo {
    background-color: #f44336;
    color: white;
  }

  .button-voto-negativo:hover:not(:disabled) {
    background-color: #e53935;
    transform: translateY(-2px);
  }

  .leaflet-marker-icon {
    z-index: 999 !important;
    transition: transform 0.2s ease;
  }

  .leaflet-marker-icon:hover {
    transform: scale(1.2);
    z-index: 1000 !important;
  }

  /* Nuevos estilos para el modal de listado */
  .list-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.8);
    z-index: 2000;
    overflow-y: auto;
  }

  .list-modal-content {
    background-color: #f8f9fa;
    margin: 20px;
    padding: 15px;
    border-radius: 10px;
    max-height: 85vh;
    overflow-y: auto;
    z-index: 2001;
  }

  .close-list {
    position: absolute;
    top: 15px;
    right: 15px;
    color: #007bff;
    font-size: 36px;
    cursor: pointer;
    font-weight: bold;
    background: none;
    border: none;
    z-index: 2002;
  }

  /* Indicador de filtros activos */
  .filter-active-indicator {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 15px;
    height: 15px;
    background-color: #ff4d4d;
    border-radius: 50%;
    display: none;
  }

  .has-filters .filter-active-indicator {
    display: block;
  }

  /* Estilos para el panel de leyenda dentro del modal */
  .legend-panel {
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 8px;
    margin-top: 15px;
  }

  .legend-panel ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .legend-panel li {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    padding: 5px;
    border-bottom: 1px solid #eee;
  }

  .legend-icon {
    display: inline-block;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    margin-right: 10px;
    vertical-align: middle;
  }

  /* Estilos para el campo de búsqueda */
  .search-container {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: 90%;
    max-width: 400px;
    z-index: 1000;
    background-color: rgba(255, 255, 255, 0.9);
    padding: 8px 12px;
    border-radius: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    border: 1px solid #e0e0e0;
  }

  .search-input {
    width: 94%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 14px;
    outline: none;
  }

  .search-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
  }

  /* Efecto de pulso para el marcador de ubicación */
  @keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
  }

  .leaflet-interactive.pulse {
    animation: pulse 2s infinite;
  }

  /* Modo conducción - ESTILOS ACTUALIZADOS */
  #drive-mode-container {
    display: none;
    position: fixed;
    bottom: 70px;
    left: 0;
    width: 100%;
    z-index: 1001;
    pointer-events: none;
  }

  #drive-mode-bar {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 10px 15px;
    margin: 0 auto;
    width: 95%;
    max-width: 500px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
    pointer-events: auto;
  }

  .drive-mode-info {
    display: flex;
    gap: 10px;
    align-items: center;
    width: 100%;
  }

  .speed-display {
    position: relative;
    text-align: center;
    flex: 1;
    padding: 0 5px;
  }

  .speed-progress-container {
    width: 100%;
    height: 4px;
    background: #f0f0f0;
    border-radius: 2px;
    margin-bottom: 3px;
    overflow: hidden;
  }

  .speed-progress-bar {
    height: 100%;
    width: 0%;
    background: #4CAF50;
    transition: width 0.5s ease, background-color 0.5s ease;
  }

  #current-speed, #speed-limit, #next-radar-distance {
    font-size: 1.4rem;
    font-weight: bold;
    display: block;
    transition: color 0.3s ease;
    color: #333;
    line-height: 1.2;
  }

  #current-speed.exceeding {
    animation: pulse 0.5s infinite;
  }

  .speed-limit-display, .next-radar-info {
    text-align: center;
    flex: 1;
  }

  .drive-mode-info small {
    font-size: 0.7rem;
    color: #666;
  }

  #exit-drive-mode {
    width: 40px;
    height: 40px;
    font-size: 16px;
    margin-left: 10px;
  }

  /* Animación de radar */
  @keyframes radarPulse {
    0% { transform: scale(1); opacity: 1; }
    100% { transform: scale(1.5); opacity: 0; }
  }

  .radar-alert {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255, 59, 48, 0.2);
    display: flex;
    justify-content: center;
    align-items: center;
    pointer-events: none;
    z-index: 2000;
    animation: radarPulse 2s infinite;
  }

  .radar-alert span {
    color: white;
    font-size: 24px;
    font-weight: bold;
    text-shadow: 0 0 5px rgba(0,0,0,0.5);
  }

  /* Ajustes para cuando el listado está visible */
  .list-visible + #drive-mode-container {
    left: 150px;
  }

  /* Autocompletado de búsqueda */
  .search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 0 0 8px 8px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1001;
    display: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  .search-suggestion {
    padding: 8px 12px;
    cursor: pointer;
  }

  .search-suggestion:hover {
    background-color: #f0f0f0;
  }

  .search-suggestion.highlighted {
    background-color: #e6f2ff;
  }

  /* Estilos para el modal de login */
  #loginForm {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  #loginForm label {
    font-weight: bold;
    margin-bottom: 5px;
  }

  #loginForm input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }

  #loginStatus {
    min-height: 20px;
    text-align: center;
    font-size: 14px;
  }

  @media (max-width: 768px) {
    #drive-mode-container {
      bottom: 80px;
    }

    #drive-mode-bar {
      flex-direction: row;
      gap: 5px;
      padding: 8px 10px;
    }

    .drive-mode-info {
      flex-direction: row;
      gap: 5px;
    }

    #current-speed, #speed-limit, #next-radar-distance {
      font-size: 1.2rem;
    }

    .speed-progress-container {
      height: 3px;
    }

    #exit-drive-mode {
      width: 35px;
      height: 35px;
    }

    .list-container {
      display: none;
    }

    .action-buttons {
      bottom: 10px;
      right: 10px;
      gap: 8px;
    }

    .action-button, .buttonAdmin, .buttonLogin {
      width: 45px;
      height: 45px;
      font-size: 18px;
    }

    .buttonAdmin, .buttonLogin {
      font-size: 12px;
    }

    .filter-group {
      min-width: 100%;
    }

    #legend-button {
      left: 20px;
      bottom: 10px;
    }

    .search-container {
      top: 10px;
      width: calc(100% - 40px);
    }
  }

  /* Mejoras de accesibilidad */
  :focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
  }
  </style>
</head>
<body>

  <!-- Barra de búsqueda -->
  <div class="search-container">
    <input type="text" class="search-input" placeholder="Buscar radares por carretera o ubicación..." id="searchInput" autocomplete="off">
    <div id="search-suggestions" class="search-suggestions"></div>
  </div>

  <!-- Modo conducción - ACTUALIZADO -->
  <div id="drive-mode-container">
    <div id="drive-mode-bar">
      <div class="drive-mode-info">
        <div class="speed-display">
          <div class="speed-progress-container">
            <div class="speed-progress-bar" id="speed-progress-bar"></div>
          </div>
          <span id="current-speed">--</span>
          <small>km/h</small>
        </div>
        <div class="speed-limit-display">
          <span id="speed-limit">--</span>
          <small>Límite</small>
        </div>
        <div class="next-radar-info">
          <span id="next-radar-distance">--</span>
          <small>Próximo</small>
        </div>
      </div>
      <button id="exit-drive-mode" class="action-button" title="Salir del modo conducción">🚗</button>
    </div>
  </div>

  <!-- Listado normal (para desktop) -->
  <div class="list-container" id="list-container">
    <h2>LISTADO DE RADARES <button class="minimize-list" id="minimize-list">×</button></h2>
    <div>
      <div class="filter-container">
        <div class="filter-group">
          <label for="radar-type-filter">Tipo de radar:</label>
          <select id="radar-type-filter" multiple size="4">
            <option value="Móvil">Móvil</option>
            <option value="Fijo">Fijo</option>
            <option value="Tramo">Tramo</option>
            <option value="Remolque">Remolque</option>
          </select>
          <small>Mantén Ctrl para seleccionar múltiples filtros.<br><b>NUEVO: </b>Activando filtros, solo se mostrarán en el listado y en el mapa los radares que cumplan las condiciones seleccionadas.</small>
        </div>

        <div class="filter-group">
          <label for="radar-status-filter">Estado:</label>
          <select id="radar-status-filter" multiple size="3">
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
            <option value="pending_review">Pendientes</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="radar-speed-filter">Velocidad límite:</label>
          <select id="radar-speed-filter">
            <option value="">Todas</option>
            <option value="30">30 km/h</option>
            <option value="40">40 km/h</option>
            <option value="50">50 km/h</option>
            <option value="60">60 km/h</option>
            <option value="70">70 km/h</option>
            <option value="80">80 km/h</option>
            <option value="90">90 km/h</option>
            <option value="100">100 km/h</option>
            <option value="110">110 km/h</option>
            <option value="120">120 km/h</option>
          </select>
        </div>
      </div>

      <button class="toggle-button-section" id="toggle-active">Mostrar/Ocultar Radares Activos</button>
      <div id="active-list" class="radar-section">
        <h3>Radares Activos</h3>
      </div>
    </div>
    <div>
      <button class="toggle-button-section" id="toggle-inactive">Mostrar/Ocultar Radares Inactivos</button>
      <div id="inactive-list" class="radar-section" style="display: none;">
        <h3>Radares Inactivos</h3>
      </div>
    </div>
  </div>

  <!-- Modal para listado en móviles -->
  <div class="list-modal" id="list-modal">
    <button class="close-list" id="close-list">&times;</button>
    <div class="list-modal-content">
      <h2>LISTADO DE RADARES</h2>
      <div>
        <div class="filter-container">
          <div class="filter-group">
            <label for="radar-type-filter-mobile">Tipo de radar:</label>
            <select id="radar-type-filter-mobile" multiple size="4">
              <option value="Móvil">Móvil</option>
              <option value="Fijo">Fijo</option>
              <option value="Tramo">Tramo</option>
              <option value="Remolque">Remolque</option>
            </select>
            <small>Mantén Ctrl para seleccionar múltiples filtros.<br><b>NUEVO: </b>Activando filtros, solo se mostrarán en el listado y en el mapa los radares que cumplan las condiciones seleccionadas.</small>
          </div>

          <div class="filter-group">
            <label for="radar-status-filter-mobile">Estado:</label>
            <select id="radar-status-filter-mobile" multiple size="3">
              <option value="active">Activos</option>
              <option value="inactive">Inactivos</option>
              <option value="pending_review">Pendientes</option>
            </select>
          </div>

          <div class="filter-group">
            <label for="radar-speed-filter-mobile">Velocidad límite:</label>
            <select id="radar-speed-filter-mobile">
              <option value="">Todas</option>
              <option value="30">30 km/h</option>
              <option value="40">40 km/h</option>
              <option value="50">50 km/h</option>
              <option value="60">60 km/h</option>
              <option value="70">70 km/h</option>
              <option value="80">80 km/h</option>
              <option value="90">90 km/h</option>
              <option value="100">100 km/h</option>
              <option value="110">110 km/h</option>
              <option value="120">120 km/h</option>
            </select>
          </div>
        </div>

        <button class="toggle-button-section" id="toggle-active-mobile">Mostrar/Ocultar Radares Activos</button>
        <div id="active-list-mobile" class="radar-section">
          <h3>Radares Activos</h3>
        </div>
      </div>
      <div>
        <button class="toggle-button-section" id="toggle-inactive-mobile">Mostrar/Ocultar Radares Inactivos</button>
        <div id="inactive-list-mobile" class="radar-section" style="display: none;">
          <h3>Radares Inactivos</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de leyenda -->
  <div class="modal" id="legendModal">
    <div class="modal-content">
      <button class="close" id="closeLegendModal">&times;</button>
      <h2>Leyenda de Radares</h2>
      <div class="legend-panel">
        <h4>Tipos de Radares</h4>
        <ul>
          <li><span class="legend-icon" style="background-color: #12e408;"></span> Radar Fijo</li>
          <li><span class="legend-icon" style="background-color: #ffb400;"></span> Radar de Tramo</li>
          <li><span class="legend-icon" style="background-color: #150aec;"></span> Radar Móvil</li>
          <li><span class="legend-icon" style="background-color: red;"></span> Radar Remolque</li>
          <li><span class="legend-icon" style="background-color: white; border: 1px solid #000;"></span> Radar Inactivo</li>
          <li><span class="legend-icon" style="background-color: #ff9800;"></span> Radar Pendiente</li>
        </ul>
        <button id="toggleInactiveRadarsButton" onclick="toggleInactiveRadars()">Mostrar/Ocultar radares inactivos</button>
      </div>
    </div>
  </div>

  <!-- Modal de Login -->
  <div class="modal" id="loginModal">
    <div class="modal-content">
      <button class="close" id="closeLoginModal">&times;</button>
      <h2>Iniciar Sesión</h2>
      <div id="loginForm">
        <label for="loginEmail">Email:</label>
        <input type="email" id="loginEmail" placeholder="tu@email.com" required>

        <label for="loginPassword">Contraseña:</label>
        <input type="password" id="loginPassword" placeholder="Tu contraseña" required>

        <button id="loginButton" class="save">Iniciar Sesión</button>
        <button id="loginCancel" class="cancel">Cancelar</button>

        <div id="loginStatus" style="margin-top: 10px;"></div>
      </div>
    </div>
  </div>

  <div class="popup-overlay" id="popup-overlay" style="display: none;">
    <div class="popup">
      <h2>Descargo de responsabilidad</h2>
      <p><b>Ahorra una multa</b> no se hace responsable de posibles errores en la información presentada en esta página.</p>
      <button onclick="closePopup()">Entendido</button>
    </div>
  </div>

  <!-- Mensaje de filtrado mejorado -->
  <div class="filter-message" id="filter-message">
    Mostrando solo <strong id="filter-criteria">todos los radares</strong>
    <button class="clear-filters" id="clear-filters">[Limpiar filtros]</button>
  </div>

  <!-- Mapa -->
  <div id="map"></div>

  <!-- Botones de acción agrupados -->
  <div class="action-buttons">
    <a href="/login" class="action-button buttonAdmin" title="Panel de administración">ADMIN</a>
    <button id="toggleLoginButton" class="action-button buttonLogin" title="Iniciar sesión">🔑</button>
    <button id="statsButton" class="action-button" title="Estadísticas">📊</button>
    <button id="toggle-button" class="action-button" title="Mostrar listado">📋</button>
    <button id="driveModeButton" class="action-button" title="Modo conducción (BETA)">🚗<span>BETA</span></button>
    <button id="addCurrentLocationButton" class="action-button" title="Añadir radar en mi ubicación">⚠️</button>
    <button id="addRadarButton" class="action-button" title="Añadir radar manualmente">➕</button>
    <button id="helpButton2" class="action-button" title="Ayuda">❔</button>
  </div>

  <!-- Botón de leyenda -->
  <button id="legend-button">Leyenda</button>

  <!-- Formulario de radar -->
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
    <label for="status">Estado</label>
    <select id="status">
      <option value="active">Activo</option>
      <option value="inactive">Inactivo</option>
      <option value="pending_review">Pendiente</option>
    </select>
    <button id="saveRadarButton" class="save">Guardar Radar</button>
    <button id="cancelRadarButton" class="cancel">Cancelar</button>
  </div>

  <!-- Modal de ayuda -->
  <div class="modal" id="helpModal">
    <div class="modal-content">
      <button class="close" id="closeModal">&times;</button>
      <h2>Ayuda</h2>
      <p>AhorraUnaMulta.com es una herramienta intuitiva que te ayuda a identificar radares de tráfico en tu zona y conocer las velocidades permitidas, permitiéndote evitar multas de tránsito de manera eficaz.</p>
      <p>Presionando el botón +, puedes añadir nuevos radares manualmente.</p>
      <p>Presionando el botón ⚠️, puedes añadir un radar en tu ubicación actual (un administrador lo revisará y completará los detalles).</p>
      <p>Presionando el botón 🚗, puedes activar el modo conducción (BETA) para una experiencia más segura al volante.</p>
      <p>Presionando sobre los radares, tienes la opción de votarlos.</p>
      <p>Si permites la ubicación, el mapa se irá actualizando en tiempo real según conduces.</p>
      <p>Si no quieres que la ubicación te centre todo el rato el mapa, tienes un botón para desactivarlo arriba a la izquierda.</p>
      <p>Los radares que no aparecen en el mapa, se encuentran averiados o desplazados de su ubicación. Tienes la opción de mostrarlos dentro del botón "Leyenda".</p>
      <p>Puedes filtrar los radares por tipo, estado y velocidad límite usando los controles del listado.</p>
      <p>Si necesitas ayuda, puedes contactarnos en <a href="mailto:soporte@ahorraunamulta.com">soporte@ahorraunamulta.com</a></p>
      <br>
      <p><strong>Última actualización:</strong>8 de mayo de 2025</p>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-database-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-auth-compat.js"></script>

  <script>
    // Configuración de Firebase principal
    const firebaseConfig = {
      apiKey: "AIzaSyDNCBnqAAcdV3kqx8hN-uMyqSkzIzV4DXc",
      authDomain: "radares-bcn.firebaseapp.com",
      databaseURL: "https://radares-bcn-default-rtdb.europe-west1.firebasedatabase.app",
      projectId: "radares-bcn",
      storageBucket: "radares-bcn.appspot.com",
      messagingSenderId: "892778900332",
      appId: "1:892778900332:web:f3c5353d981dda7b4ba149",
      measurementId: "G-C1GT8Q96ZJ"
    };

    // Configuración de Firebase para autenticación
    const firebaseConfigLogin = {
      apiKey: "AIzaSyAX4uy3ON91cwK3Tt9r5Eqpucyf4sfv0No",
      authDomain: "login-radares.firebaseapp.com",
      projectId: "login-radares",
      storageBucket: "login-radares.appspot.com",
      messagingSenderId: "661760692554",
      appId: "1:661760692554:web:2da6e767592800380eb1b3",
      measurementId: "G-S2ZCB85HX1"
    };

    // Inicializar Firebase principal
    const app = firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    // Inicializar Firebase para autenticación
    const authApp = firebase.initializeApp(firebaseConfigLogin, "authApp");
    const auth = firebase.auth(authApp);

    // Variables globales
    let map;
    let userMarker;
    let accuracyCircle;
    let tempMarker = null;
    let showInactiveRadars = false;
    let centrarMapa = false;
    let radaresMarkers = {};
    let isDriveModeActive = false;
    let driveModeCheckInterval;
    let lastPositionForDriveMode = null;
    let lastAlertedRadar = null;
    let lastRadarCheckTime = 0;
    let currentVoice = null;
    let currentUser = null;

    // Variables para los filtros
    let currentFilters = {
      types: [],
      statuses: [],
      speed: ''
    };

    // Inicialización del mapa
    function initMap() {
      // Verificar si hay parámetros para centrar el mapa
      const urlParams = new URLSearchParams(window.location.search);
      const radarId = urlParams.get('radar');
      const centerLat = parseFloat(urlParams.get('lat'));
      const centerLng = parseFloat(urlParams.get('lng'));

      // Configurar vista inicial
      let initialView = [41.3784, 2.1927];
      let initialZoom = 10;

      if (centerLat && centerLng) {
        initialView = [centerLat, centerLng];
        initialZoom = 16;
      }

      map = L.map('map').setView(initialView, initialZoom);

      // Capas base
      const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      });

      const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
      });

      osmLayer.addTo(map);

      // Control de capas
      L.control.layers({
        "Mapa estándar": osmLayer,
        "Vista satélite": satelliteLayer
      }, {}, { position: 'bottomleft' }).addTo(map);

      // Iniciar funcionalidades
      initUI();
      initGeolocation();
      initFilters();
      loadRadars();
      setupEventListeners();
      setupSearch();

      // Si hay un radar específico para centrar
      if (radarId && centerLat && centerLng) {
        // Esperar a que se carguen los radares
        db.ref("radares").once('value', snapshot => {
          const radares = snapshot.val();
          if (radares && radares[radarId]) {
            const radar = radares[radarId];
            const marker = radaresMarkers[radarId];

            if (marker) {
              // Centrar y abrir popup
              map.setView([radar.lat, radar.lng], 16);
              marker.openPopup();

              // Destacar el marcador
              marker.setIcon(L.icon({
                iconUrl: marker.options.icon.options.iconUrl,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
              }));

              setTimeout(() => {
                marker.setIcon(getIconByRadar(radar));
              }, 2000);
            }
          }
        });
      }
    }

    // Interfaz de usuario
    function initUI() {
      const toggleButton = document.getElementById('toggle-button');
      const minimizeButton = document.getElementById('minimize-list');
      const listModal = document.getElementById('list-modal');
      const closeList = document.getElementById('close-list');
      const listContainer = document.getElementById('list-container');
      const actionButtons = document.querySelector('.action-buttons');

      // Botón para minimizar el listado en desktop
      minimizeButton.addEventListener('click', () => {
        listContainer.style.transform = 'translateX(100%)';
        setTimeout(() => {
          listContainer.style.display = 'none';
        }, 300);
        document.querySelector('.action-buttons').classList.remove('list-visible');
        toggleButton.textContent = '📋';
      });

      // Añadir indicador de listado visible
      toggleButton.addEventListener('click', () => {
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
          openModal('list-modal');
          updateMobileList();
        } else {
          const isHidden = listContainer.style.display === 'none' ||
                          listContainer.style.display === '' ||
                          listContainer.style.transform === 'translateX(100%)';

          if (isHidden) {
            listContainer.style.display = 'flex';
            listContainer.style.transform = 'translateX(0)';
            actionButtons.classList.add('list-visible');
            toggleButton.textContent = '📋';
          } else {
            listContainer.style.transform = 'translateX(100%)';
            setTimeout(() => {
              listContainer.style.display = 'none';
            }, 300);
            actionButtons.classList.remove('list-visible');
            toggleButton.textContent = '📋';
          }
        }
      });

      closeList.addEventListener('click', () => {
        closeModal('list-modal');
      });

      listModal.addEventListener('click', (e) => {
        if (e.target === listModal) {
          closeModal('list-modal');
        }
      });

      // Botón de leyenda
      const legendButton = document.getElementById('legend-button');
      legendButton.addEventListener('click', () => {
        openModal('legendModal');
      });

      document.getElementById('closeLegendModal').addEventListener('click', () => {
        closeModal('legendModal');
      });

      // Botón para añadir radar en ubicación actual
      document.getElementById('addCurrentLocationButton').addEventListener('click', addRadarAtCurrentLocation);

      // Secciones
      document.getElementById('toggle-active').addEventListener('click', function() {
        this.classList.toggle('collapsed');
        toggleSection('active-list');
      });

      document.getElementById('toggle-inactive').addEventListener('click', function() {
        this.classList.toggle('collapsed');
        toggleSection('inactive-list');
      });

      document.getElementById('toggle-active-mobile').addEventListener('click', function() {
        this.classList.toggle('collapsed');
        toggleSection('active-list-mobile');
      });

      document.getElementById('toggle-inactive-mobile').addEventListener('click', function() {
        this.classList.toggle('collapsed');
        toggleSection('inactive-list-mobile');
      });

      // Modal de ayuda
      document.getElementById('helpButton2').addEventListener('click', () => {
        openModal('helpModal');
      });

      document.getElementById('closeModal').addEventListener('click', () => {
        closeModal('helpModal');
      });

      // Botón de estadísticas
      document.getElementById('statsButton').addEventListener('click', showStats);

      // Botón de modo conducción
      document.getElementById('driveModeButton').addEventListener('click', () => {
        if (isDriveModeActive) {
          disableDriveMode();
        } else {
          enableDriveMode();
        }
      });

      // Evento para salir del modo conducción
      document.getElementById('exit-drive-mode').addEventListener('click', disableDriveMode);

      // Configurar botón de login
      document.getElementById('toggleLoginButton').addEventListener('click', (e) => {
        e.preventDefault();
        if (currentUser) {
          // Si ya está logueado, mostrar opción de logout
          if (confirm('¿Deseas cerrar sesión?')) {
            auth.signOut();
          }
        } else {
          openModal('loginModal');
        }
      });

      // Configurar formulario de login
      document.getElementById('loginButton').addEventListener('click', () => {
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        const status = document.getElementById('loginStatus');

        status.textContent = "Iniciando sesión...";
        status.style.color = "#007bff";

        auth.signInWithEmailAndPassword(email, password)
          .then((userCredential) => {
            status.textContent = "Sesión iniciada correctamente";
            status.style.color = "#28a745";
            setTimeout(() => {
              closeModal('loginModal');
              // Actualizar todos los popups abiertos para mostrar botón de edición
              updateAllOpenPopups();
            }, 1000);
          })
          .catch((error) => {
            status.textContent = "Error: " + error.message;
            status.style.color = "#dc3545";
          });
      });

      document.getElementById('loginCancel').addEventListener('click', () => {
        closeModal('loginModal');
      });

      document.getElementById('closeLoginModal').addEventListener('click', () => {
        closeModal('loginModal');
      });

      // Observador de estado de autenticación
      auth.onAuthStateChanged((user) => {
        currentUser = user;
        const loginButton = document.getElementById('toggleLoginButton');
        if (user) {
          loginButton.textContent = '🔓';
          loginButton.title = 'Cerrar sesión';
          loginButton.style.backgroundColor = '#28a745';
          // Actualizar todos los popups abiertos para mostrar botón de edición
          updateAllOpenPopups();
        } else {
          loginButton.textContent = '🔑';
          loginButton.title = 'Iniciar sesión';
          loginButton.style.backgroundColor = '#17a2b8';
          // Actualizar todos los popups abiertos para ocultar botón de edición
          updateAllOpenPopups();
        }
      });

      // Mejorar accesibilidad de botones
      document.querySelectorAll('button').forEach(button => {
        button.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            this.click();
          }
        });
      });
    }

    // Función para actualizar todos los popups abiertos
    function updateAllOpenPopups() {
      Object.values(radaresMarkers).forEach(marker => {
        if (marker.isPopupOpen()) {
          const radarId = marker.radarData.key;
          marker.setPopupContent(createPopupContent(marker.radarData, radarId));
        }
      });
    }

    // Abrir modal
    function openModal(modalId) {
      document.getElementById(modalId).style.display = 'flex';
      document.body.classList.add('modal-open');
    }

    // Cerrar modal
    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
      document.body.classList.remove('modal-open');
    }

    // Geolocalización
    function initGeolocation() {
      if (!navigator.geolocation) {
        // Mostrar mensaje amigable al usuario
        const geoWarning = L.popup()
          .setLatLng(map.getCenter())
          .setContent('<div style="padding: 10px;"><b>Geolocalización no disponible</b><br>Tu navegador no soporta geolocalización o está desactivada.</div>')
          .openOn(map);

        setTimeout(() => {
          map.closePopup(geoWarning);
        }, 5000);
        return;
      }

      // Crear marcador de usuario (punto azul al estilo Google Maps)
      userMarker = L.circleMarker([0, 0], {
        radius: 8,
        fillColor: "#4285F4",
        color: "#FFFFFF",
        weight: 2,
        opacity: 1,
        fillOpacity: 1,
        zIndexOffset: 1000
      }).addTo(map);

      // Círculo de precisión
      accuracyCircle = L.circle([0, 0], {
        stroke: false,
        fillColor: "#4285F4",
        fillOpacity: 0.2,
        interactive: false
      }).addTo(map);

      // Variables para controlar la posición anterior
      let lastPosition = null;
      let lastUpdateTime = 0;

      // Botón de centrado mejorado
      var toggleButton = L.control({ position: 'topleft' });
      toggleButton.onAdd = function(map) {
        var container = L.DomUtil.create("div", "leaflet-bar");
        var button = L.DomUtil.create("a", "boton-centrado off");
        button.innerHTML = '📍';
        button.href = '#';
        button.title = 'Centrar en mi ubicación';

        button.onclick = function(e) {
          L.DomEvent.stopPropagation(e);
          L.DomEvent.preventDefault(e);
          centrarMapa = !centrarMapa;

          if (centrarMapa) {
            button.classList.add('on');
            button.classList.remove('off');

            // Centrar inmediatamente si ya tenemos posición
            if (userMarker.getLatLng().lat !== 0) {
              map.setView(userMarker.getLatLng(), map.getZoom(), {
                animate: true,
                duration: 1
              });
            }
          } else {
            button.classList.remove('on');
            button.classList.add('off');
          }
        };

        container.appendChild(button);
        return container;
      };
      toggleButton.addTo(map);

      // Seguimiento de la posición con mejor control
      navigator.geolocation.watchPosition(
        position => {
          // Guardar posición para el modo conducción
          lastPositionForDriveMode = position;

          const now = Date.now();
          // Limitar actualizaciones a 1 por segundo como máximo
          if (now - lastUpdateTime < 1000) return;

          const { latitude, longitude, accuracy, speed, heading } = position.coords;
          const newPos = [latitude, longitude];

          // Solo actualizar si hay un cambio significativo (más de 50 metros)
          if (!lastPosition || distanceBetween(lastPosition, newPos) > 5) {
            userMarker.setLatLng(newPos);
            accuracyCircle.setLatLng(newPos);
            accuracyCircle.setRadius(accuracy);

            // Solo centrar en modo conductor o si el usuario lo ha activado manualmente
            if (isDriveModeActive || centrarMapa) {
              map.setView(newPos);
            }

            lastPosition = newPos;
            lastUpdateTime = now;
          }
        },
        error => {
          console.error("Error en geolocalización:", error);
        },
        {
          enableHighAccuracy: true,
          maximumAge: 30000,
          timeout: 10000
        }
      );

      // Función auxiliar para calcular distancia entre coordenadas
      function distanceBetween(pos1, pos2) {
        const R = 6371000; // Radio de la Tierra en metros
        const φ1 = pos1[0] * Math.PI/180;
        const φ2 = pos2[0] * Math.PI/180;
        const Δφ = (pos2[0]-pos1[0]) * Math.PI/180;
        const Δλ = (pos2[1]-pos1[1]) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
      }
    }

    // Función para actualizar la velocidad en modo conducción
    function updateSpeedInDriveMode(speed) {
      const speedKmh = (speed * 3.6).toFixed(0);
      document.getElementById('current-speed').textContent = speedKmh;

      // Comprobar exceso de velocidad
      const speedLimit = document.getElementById('speed-limit').textContent;
      if (speedLimit !== '--') {
        updateSpeedProgress(speedKmh, speedLimit);
      }
    }

    // Función para actualizar la barra de progreso de velocidad
    function updateSpeedProgress(currentSpeed, speedLimit) {
      if (!currentSpeed || !speedLimit) return;

      const progressBar = document.getElementById('speed-progress-bar');
      const speedRatio = Math.min(currentSpeed / speedLimit, 1.5); // Máximo 150% del límite

      // Calcular porcentaje y color
      let percentage = (speedRatio / 1.5) * 100;
      let color;

      if (speedRatio < 0.8) {
        color = '#4CAF50'; // Verde
      } else if (speedRatio < 1.0) {
        color = '#FFC107'; // Amarillo
      } else {
        color = '#F44336'; // Rojo
      }

      progressBar.style.width = `${percentage}%`;
      progressBar.style.backgroundColor = color;
    }

    // Función para activar el modo conducción
    function enableDriveMode() {
      if (isDriveModeActive) return;

      // Mostrar advertencia
      const betaWarning = L.popup()
        .setLatLng(map.getCenter())
        .setContent(`
          <div style="padding: 15px; max-width: 280px; text-align: center; background: #fff3cd; border: 2px solid #ffc107; border-radius: 10px;">
            <h3 style="margin-top: 0; color: #856404;">⚠️ MODO CONDUCCIÓN</h3>
            <p style="margin-bottom: 0;">El sistema alertará con voz femenina sobre radares a 600 y 300 metros.</p>
            <p style="font-size: 0.9em; margin-top: 10px;"><b>Mantenga la atención en la carretera.</b></p>
          </div>
        `)
        .openOn(map);

      setTimeout(() => {
        map.closePopup(betaWarning);
      }, 8000);

      isDriveModeActive = true;
      document.getElementById('drive-mode-container').style.display = 'block';
      document.getElementById('driveModeButton').classList.add('active');
      centrarMapa = true; // Forzar centrado en modo conductor

      // Ocultar elementos no esenciales
      document.querySelector('.search-container').style.display = 'none';
      document.getElementById('legend-button').style.display = 'none';

      // Ocultar todos los botones excepto los necesarios
      const actionButtons = document.querySelectorAll('.action-button');
      actionButtons.forEach(button => {
        button.style.display = 'none';
      });

      // Mostrar solo los botones necesarios
      document.getElementById('exit-drive-mode').style.display = 'flex';
      document.getElementById('addCurrentLocationButton').style.display = 'flex';

      // Configurar voz femenina si está disponible
      if ('speechSynthesis' in window) {
        const voices = window.speechSynthesis.getVoices();
        const femaleVoice = voices.find(v => v.name.includes('female') && v.lang.includes('es'));
        if (femaleVoice) {
          currentVoice = femaleVoice;
        }
      }

      // Ajustar el mapa para modo conducción
      if (userMarker && userMarker.getLatLng().lat !== 0) {
        map.setView(userMarker.getLatLng(), 15);
      }

      // Iniciar chequeo de radares
      driveModeCheckInterval = setInterval(checkNearbyRadars, 500);
    }

    // Función para desactivar el modo conducción
    function disableDriveMode() {
      if (!isDriveModeActive) return;

      isDriveModeActive = false;
      document.getElementById('drive-mode-container').style.display = 'none';
      document.getElementById('driveModeButton').classList.remove('active');
      centrarMapa = false; // Desactivar centrado automático al salir del modo conductor

      // Mostrar elementos nuevamente
      document.querySelector('.search-container').style.display = 'block';
      document.getElementById('legend-button').style.display = 'block';

      // Restaurar todos los botones
      const actionButtons = document.querySelectorAll('.action-button');
      actionButtons.forEach(button => {
        button.style.display = 'flex';
      });

      // Detener chequeo de radares
      clearInterval(driveModeCheckInterval);

      // Restablecer valores
      document.getElementById('current-speed').textContent = '--';
      document.getElementById('current-speed').classList.remove('exceeding');
      document.getElementById('speed-limit').textContent = '--';
      document.getElementById('next-radar-distance').textContent = '--';
      document.getElementById('speed-progress-bar').style.width = '0%';

      // Resetear radar alertado
      lastAlertedRadar = null;
    }

    // Comprobar radares cercanos y actualizar UI
    function checkNearbyRadars() {
      if (!userMarker || !userMarker.getLatLng() || !isDriveModeActive) return;

      const userPos = userMarker.getLatLng();
      const userHeading = lastPositionForDriveMode?.coords?.heading || null;
      const userSpeed = lastPositionForDriveMode?.coords?.speed || 0;
      const userSpeedKmh = userSpeed ? (userSpeed * 3.6).toFixed(0) : 0;
      const nearbyRadars = getNearbyRadars(userPos.lat, userPos.lng, 1); // 1km radius

      // Filtrar radares en la misma dirección
      const filteredRadars = nearbyRadars.filter(marker => {
        if (userHeading === null) return true;

        const angleToRadar = Math.atan2(
          marker.getLatLng().lng - userPos.lng,
          marker.getLatLng().lat - userPos.lat
        ) * (180 / Math.PI);

        const normalizedUserHeading = (userHeading + 360) % 360;
        const normalizedAngleToRadar = (angleToRadar + 360) % 360;
        const angleDiff = Math.abs(normalizedUserHeading - normalizedAngleToRadar);
        return angleDiff < 45 || angleDiff > 315;
      });

      // Ordenar por distancia
      filteredRadars.sort((a, b) => {
        const distA = map.distance(userPos, a.getLatLng());
        const distB = map.distance(userPos, b.getLatLng());
        return distA - distB;
      });

      // Actualizar UI con el radar más cercano
      if (filteredRadars.length > 0) {
        const closestRadar = filteredRadars[0];
        const distance = map.distance(userPos, closestRadar.getLatLng());
        const distanceM = Math.round(distance);

        document.getElementById('next-radar-distance').textContent = `${distanceM} m`;

        // Actualizar límite de velocidad en el panel
        if (closestRadar.radarData.speed) {
          const speedLimit = closestRadar.radarData.speed;
          document.getElementById('speed-limit').textContent = speedLimit;
          updateSpeedProgress(userSpeedKmh, speedLimit);
        }

        // Alertar si está muy cerca (solo si velocidad > 30 km/h)
        if (userSpeed > 8.33) { // 8.33 m/s ≈ 30 km/h
          // Alerta a 600 metros
          if (distance < 620 && distance > 580 && lastAlertedRadar !== closestRadar.radarData.key) {
            playRadarAlert(closestRadar, 600);
          }
          // Alerta a 300 metros
          else if (distance < 320 && distance > 280 && lastAlertedRadar === closestRadar.radarData.key) {
            playRadarAlert(closestRadar, 300);
          }
          // Alerta por exceso de velocidad cuando estamos cerca
          else if (distance < 200 && userSpeedKmh > closestRadar.radarData.speed) {
            const excess = userSpeedKmh - closestRadar.radarData.speed;
            playAlert(`¡Exceso de velocidad! Límite: ${closestRadar.radarData.speed} km/h. Tu velocidad: ${userSpeedKmh} km/h. Reduzca ${excess} km/h`);
          }
        }

        // Si nos alejamos del radar, resetear la alerta
        if (distance > 650) {
          lastAlertedRadar = null;
        }
      } else {
        document.getElementById('next-radar-distance').textContent = '--';
        document.getElementById('speed-limit').textContent = '--';
        document.getElementById('speed-progress-bar').style.width = '0%';
        lastAlertedRadar = null;
      }
    }

    // Función para alertas de radar con voz femenina
    function playRadarAlert(radar, distance) {
      const radarType = getRadarTypeDescription(radar.radarData.radarType);
      const speedLimit = radar.radarData.speed || 'velocidad desconocida';

      let alertMessage = `Radar ${radarType} a ${distance} metros. Límite ${speedLimit} km/h.`;

      // Mostrar alerta visual
      const alertDiv = document.createElement('div');
      alertDiv.className = 'radar-alert';
      alertDiv.innerHTML = `<span>Radar ${radarType}<br>${distance}m<br>Límite: ${speedLimit} km/h</span>`;
      document.body.appendChild(alertDiv);

      setTimeout(() => {
        alertDiv.remove();
      }, 3000);

      // Alertar por voz
      playAlert(alertMessage);

      // Marcar como alertado
      if (distance === 600) {
        lastAlertedRadar = radar.radarData.key;
      }
    }

    // Función auxiliar para descripciones de tipos de radar
    function getRadarTypeDescription(type) {
      const descriptions = {
        'Fijo': 'fijo',
        'Móvil': 'móvil',
        'Tramo': 'de tramo',
        'Remolque': 'en remolque',
        'default': ''
      };
      return descriptions[type] || descriptions['default'];
    }

    // Notificación de voz mejorada
    function playAlert(message) {
      if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(message);
        utterance.lang = 'es-ES';
        utterance.rate = 0.9;
        utterance.volume = 1.5;

        // Usar voz femenina si está configurada
        if (currentVoice) {
          utterance.voice = currentVoice;
        }

        window.speechSynthesis.speak(utterance);
      }

      // Mostrar notificación visual
      const notification = L.popup()
        .setLatLng(userMarker.getLatLng())
        .setContent(`
          <div style="padding: 10px; background: #ffeb3b; border-radius: 5px; max-width: 250px;">
            <b>⚠️ Alerta</b><br>
            ${message}
          </div>
        `)
        .openOn(map);

      setTimeout(() => {
        map.closePopup(notification);
      }, 5000);
    }

    // Obtener radares cercanos
    function getNearbyRadars(lat, lng, radiusKm) {
      return Object.values(radaresMarkers).filter(marker => {
        const distance = map.distance([lat, lng], marker.getLatLng());
        return distance <= radiusKm * 1000;
      });
    }

    // Función para añadir radar en la ubicación actual
    function addRadarAtCurrentLocation() {
      if (!userMarker || !userMarker.getLatLng()) {
        alert('No se ha podido obtener tu ubicación actual. Por favor, activa la geolocalización.');
        return;
      }

      // Obtener velocidad actual si está disponible
      const currentSpeed = lastPositionForDriveMode?.coords?.speed
        ? Math.round(lastPositionForDriveMode.coords.speed * 3.6)
        : null;

      // Mostrar confirmación con información adicional
      const confirmMessage = currentSpeed
        ? `¿Quieres añadir un radar genérico en tu ubicación actual? Velocidad detectada: ${currentSpeed} km/h. Un administrador revisará y completará los detalles.`
        : `¿Quieres añadir un radar genérico en tu ubicación actual? Un administrador lo revisará y completará los detalles.`;

      if (!confirm(confirmMessage)) {
        return;
      }

      const userPos = userMarker.getLatLng();

      // Crear un radar genérico con la velocidad detectada si está disponible
      const newRadar = {
        radarType: "Pendiente de completar",
        road: "Por determinar",
        direction: "Por determinar",
        speed: currentSpeed || 0,
        lat: userPos.lat,
        lng: userPos.lng,
        votos_positivos: 0,
        votos_negativos: 0,
        status: "pending_review",
        last_updated: new Date().toISOString(),
        is_temp: true,
        detected_speed: currentSpeed || null
      };

      // Mostrar feedback de carga
      const button = document.getElementById('addCurrentLocationButton');
      const originalText = button.title;
      button.title = "Guardando...";
      button.disabled = true;

      db.ref("radares").push(newRadar)
        .then(() => {
          alert('Radar temporal añadido. Un administrador lo revisará y completará la información.' +
                (currentSpeed ? ` Velocidad detectada: ${currentSpeed} km/h.` : ''));
        })
        .catch(error => {
          console.error("Error al guardar radar:", error);
          alert('Error al guardar el radar');
        })
        .finally(() => {
          button.title = originalText;
          button.disabled = false;
        });
    }

    // Mostrar estadísticas
    function showStats() {
      let activeCount = 0, inactiveCount = 0, pendingCount = 0;
      let totalRadars = 0;

      // Contar todos los radares, incluso los ocultos
      db.ref("radares").once('value', snapshot => {
        const radares = snapshot.val();
        if (radares) {
          Object.values(radares).forEach(radar => {
            totalRadars++;
            if (radar.status === 'active') activeCount++;
            else if (radar.status === 'inactive') inactiveCount++;
            else if (radar.status === 'pending_review') pendingCount++;
          });
        }

        const statsHtml = `
          <div style="padding: 10px; min-width: 200px;">
            <h3 style="margin-top: 0; color: #007bff;">Estadísticas de Radares</h3>
            <p><strong>Total:</strong> ${totalRadars}</p>
            <p><strong>Activos:</strong> ${activeCount}</p>
            <p><strong>Inactivos:</strong> ${inactiveCount} ${!showInactiveRadars ? '(ocultos)' : ''}</p>
            <p><strong>Pendientes:</strong> ${pendingCount}</p>
            <p><strong>Última actualización:</strong> ${new Date().toLocaleString()}</p>
          </div>
        `;

        L.popup()
          .setLatLng(map.getCenter())
          .setContent(statsHtml)
          .openOn(map);
      });
    }

    // Inicializar filtros
    function initFilters() {
      const typeFilter = document.getElementById('radar-type-filter');
      const statusFilter = document.getElementById('radar-status-filter');
      const speedFilter = document.getElementById('radar-speed-filter');
      const typeFilterMobile = document.getElementById('radar-type-filter-mobile');
      const statusFilterMobile = document.getElementById('radar-status-filter-mobile');
      const speedFilterMobile = document.getElementById('radar-speed-filter-mobile');
      const clearFiltersBtn = document.getElementById('clear-filters');
      const filterContainer = document.querySelector('.filter-container');

      // Event listeners para los filtros
      typeFilter.addEventListener('change', updateFilters);
      statusFilter.addEventListener('change', updateFilters);
      speedFilter.addEventListener('change', updateFilters);
      typeFilterMobile.addEventListener('change', updateFilters);
      statusFilterMobile.addEventListener('change', updateFilters);
      speedFilterMobile.addEventListener('change', updateFilters);
      clearFiltersBtn.addEventListener('click', resetFilters);

      // Mostrar mensaje inicial
      updateFilterMessage();
    }

    // Actualizar filtros
    function updateFilters() {
      const typeFilter = document.getElementById('radar-type-filter');
      const statusFilter = document.getElementById('radar-status-filter');
      const speedFilter = document.getElementById('radar-speed-filter');
      const typeFilterMobile = document.getElementById('radar-type-filter-mobile');
      const statusFilterMobile = document.getElementById('radar-status-filter-mobile');
      const speedFilterMobile = document.getElementById('radar-speed-filter-mobile');
      const filterContainer = document.querySelector('.filter-container');

      // Sincronizar filtros móviles y de escritorio
      if (this === typeFilter || this === typeFilterMobile) {
        const source = this === typeFilter ? typeFilter : typeFilterMobile;
        const target = this === typeFilter ? typeFilterMobile : typeFilter;

        Array.from(target.options).forEach(option => {
          option.selected = Array.from(source.selectedOptions).some(
            selected => selected.value === option.value
          );
        });
      }

      if (this === statusFilter || this === statusFilterMobile) {
        const source = this === statusFilter ? statusFilter : statusFilterMobile;
        const target = this === statusFilter ? statusFilterMobile : statusFilter;

        Array.from(target.options).forEach(option => {
          option.selected = Array.from(source.selectedOptions).some(
            selected => selected.value === option.value
          );
        });
      }

      if (this === speedFilter || this === speedFilterMobile) {
        const source = this === speedFilter ? speedFilter : speedFilterMobile;
        const target = this === speedFilter ? speedFilterMobile : speedFilter;
        target.value = source.value;
      }

      // Obtener valores seleccionados
      currentFilters.types = Array.from(typeFilter.selectedOptions).map(opt => opt.value);
      currentFilters.statuses = Array.from(statusFilter.selectedOptions).map(opt => opt.value);
      currentFilters.speed = speedFilter.value;

      // Añadir clase cuando hay filtros activos
      if (currentFilters.types.length > 0 || currentFilters.statuses.length > 0 || currentFilters.speed) {
        filterContainer.classList.add('has-filters');
      } else {
        filterContainer.classList.remove('has-filters');
      }

      // Aplicar filtros
      applyFilters();
      updateFilterMessage();
    }

    // Aplicar filtros al mapa y listado
    function applyFilters() {
      const filterMessage = document.getElementById('filter-message');
      let visibleCount = 0;

      // Mostrar u ocultar marcadores según los filtros
      Object.entries(radaresMarkers).forEach(([id, marker]) => {
        const radar = marker.radarData;
        const matchesType = currentFilters.types.length === 0 ||
                          currentFilters.types.includes(radar.radarType);
        const matchesStatus = currentFilters.statuses.length === 0 ||
                            currentFilters.statuses.includes(radar.status);
        const matchesSpeed = !currentFilters.speed ||
                           (radar.speed && radar.speed.toString() === currentFilters.speed);

        if (matchesType && matchesStatus && matchesSpeed) {
          marker.addTo(map);
          visibleCount++;
        } else {
          map.removeLayer(marker);
        }
      });

      // Actualizar listados
      updateFilteredLists();

      // Mostrar mensaje si hay filtros activos
      if (currentFilters.types.length > 0 || currentFilters.statuses.length > 0 || currentFilters.speed) {
        filterMessage.style.display = 'block';
      } else {
        filterMessage.style.display = 'none';
      }
    }

    // Actualizar mensaje de filtrado
    function updateFilterMessage() {
      const filterCriteria = document.getElementById('filter-criteria');
      const parts = [];

      if (currentFilters.types.length > 0) {
        parts.push(`tipo: ${currentFilters.types.join(', ')}`);
      }

      if (currentFilters.statuses.length > 0) {
        parts.push(`estado: ${currentFilters.statuses.map(s => {
          if (s === 'active') return 'activos';
          if (s === 'inactive') return 'inactivos';
          if (s === 'pending_review') return 'pendientes';
          return s;
        }).join(', ')}`);
      }

      if (currentFilters.speed) {
        parts.push(`velocidad: ${currentFilters.speed} km/h`);
      }

      if (parts.length > 0) {
        filterCriteria.textContent = `radares con ${parts.join('; ')}`;
      } else {
        filterCriteria.textContent = 'todos los radares';
      }
    }

    // Reiniciar filtros
    function resetFilters() {
      document.getElementById('radar-type-filter').selectedIndex = -1;
      document.getElementById('radar-status-filter').selectedIndex = -1;
      document.getElementById('radar-speed-filter').value = '';
      document.getElementById('radar-type-filter-mobile').selectedIndex = -1;
      document.getElementById('radar-status-filter-mobile').selectedIndex = -1;
      document.getElementById('radar-speed-filter-mobile').value = '';

      currentFilters = {
        types: [],
        statuses: [],
        speed: ''
      };

      applyFilters();
      updateFilterMessage();
      document.querySelector('.filter-container').classList.remove('has-filters');
    }

    // Carga de radares
    function loadRadars() {
      db.ref("radares").on("value", snapshot => {
        console.log("Datos recibidos de Firebase:", snapshot.val());

        Object.values(radaresMarkers).forEach(marker => {
          map.removeLayer(marker);
        });
        radaresMarkers = {};

        const radares = snapshot.val();

        if (radares) {
          Object.entries(radares).forEach(([id, radar]) => {
            if (radar.status === "active" || showInactiveRadars || radar.status === "pending_review") {
              addRadarMarker(radar, id);
            }
          });
        }

        updateRadarList(radares);
        applyFilters();
      });
    }

    // Añadir marcador de radar
    function addRadarMarker(radar, radarId) {
      const icon = getIconByRadar(radar);
      const marker = L.marker([radar.lat, radar.lng], {
        icon,
        riseOnHover: true
      }).addTo(map);

      // Añadir efecto al hacer clic
      marker.on('click', function() {
        this.setIcon(L.icon({
          iconUrl: this.options.icon.options.iconUrl,
          iconSize: [35, 35],
          iconAnchor: [17, 17]
        }));

        setTimeout(() => {
          this.setIcon(icon);
        }, 300);
      });

      marker.radarData = radar;
      marker.radarData.key = radarId;
      radaresMarkers[radarId] = marker;

      const popupContent = createPopupContent(radar, radarId);
      marker.bindPopup(popupContent);
    }

    // Crear contenido del popup
    function createPopupContent(radar, radarId) {
      const votosPositivos = radar.votos_positivos || 0;
      const votosNegativos = radar.votos_negativos || 0;
      const userVoted = localStorage.getItem(`voto_${radarId}`);
      const lastUpdated = radar.last_updated ? new Date(radar.last_updated).toLocaleString() : "No disponible";

      const popup = document.createElement('div');
      popup.innerHTML = `
        <b>${radar.radarType || "Radar"}</b><br>
        <small>${radar.road || "Carretera no especificada"}</small><br>
        ${radar.pk ? `PK: ${radar.pk}<br>` : ''}
        Dirección: ${radar.direction || "No especificada"}<br>
        Velocidad: ${radar.speed || "N/A"} km/h<br>
        Estado: <b>${radar.status === "active" ? "Activo" : radar.status === "pending_review" ? "Pendiente" : "Inactivo"}</b><br>
        Actualizado: ${lastUpdated}<br>
        <button onclick="copyToClipboard('${radarId}')"
                style="margin-top: 5px; padding: 2px 5px; font-size: 10px; background: #f0f0f0; border: 1px solid #ccc; border-radius: 3px; cursor: pointer;">
          Copiar ID
        </button>
        ${currentUser ? `<button onclick="editRadar('${radarId}')"
                style="margin-top: 5px; padding: 2px 5px; font-size: 10px; background: #007bff; color: white; border: 1px solid #007bff; border-radius: 3px; cursor: pointer;">
          Editar
        </button>` : ''}
        <div style="margin-top: 10px; display: flex; justify-content: center; gap: 10px;">
          <button id="boton_positivo_${radarId}" class="button-voto button-voto-positivo"
            onclick="votar('${radarId}', 'positivo')" ${userVoted ? 'disabled' : ''}>
            👍 ${votosPositivos}
          </button>
          <button id="boton_negativo_${radarId}" class="button-voto button-voto-negativo"
            onclick="votar('${radarId}', 'negativo')" ${userVoted ? 'disabled' : ''}>
            👎 ${votosNegativos}
          </button>
        </div>
      `;

      return popup;
    }

    // Función para editar radar
    window.editRadar = function(radarId) {
      const radar = radaresMarkers[radarId]?.radarData;
      if (!radar) return;

      // Mostrar formulario de edición con los datos actuales
      document.getElementById('radarType').value = radar.radarType || '';
      document.getElementById('road').value = radar.road || '';
      document.getElementById('pk').value = radar.pk || '';
      document.getElementById('direction').value = radar.direction || '';
      document.getElementById('speed').value = radar.speed || '';
      document.getElementById('status').value = radar.status || 'active';

      // Posicionar marcador temporal en la ubicación actual
      if (tempMarker) map.removeLayer(tempMarker);
      tempMarker = L.marker([radar.lat, radar.lng]).addTo(map);

      // Mostrar formulario
      document.getElementById('radarForm').style.display = 'block';
      document.getElementById('addRadarButton').classList.add('active');
      document.getElementById('addRadarButton').textContent = '🚨';

      // Modificar el botón de guardar para actualizar en lugar de crear nuevo
      const saveButton = document.getElementById('saveRadarButton');
      saveButton.textContent = 'Actualizar Radar';
      saveButton.onclick = function() {
        updateRadar(radarId);
      };
    }

    // Función para actualizar radar
    function updateRadar(radarId) {
      const radarType = document.getElementById('radarType').value;
      const road = document.getElementById('road').value;
      const pk = document.getElementById('pk').value;
      const direction = document.getElementById('direction').value;
      const speed = document.getElementById('speed').value;
      const status = document.getElementById('status').value;

      // Validación de campos
      const requiredFields = ['radarType', 'road', 'direction', 'speed'];
      let isValid = true;

      requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (!element.value) {
          element.style.borderColor = 'red';
          isValid = false;
        } else {
          element.style.borderColor = '';
        }
      });

      if (!isValid || !tempMarker) {
        if (!tempMarker) {
          alert('Por favor, selecciona una ubicación en el mapa haciendo clic');
        }
        return;
      }

      const { lat, lng } = tempMarker.getLatLng();
      const updatedRadar = {
        radarType,
        road,
        pk: pk || null,
        direction,
        speed,
        status,
        lat,
        lng,
        last_updated: new Date().toISOString()
      };

      // Mostrar feedback de carga
      const saveButton = document.getElementById('saveRadarButton');
      const originalText = saveButton.textContent;
      saveButton.textContent = 'Guardando...';
      saveButton.disabled = true;

      db.ref(`radares/${radarId}`).update(updatedRadar)
        .then(() => {
          alert('Radar actualizado con éxito');
          resetRadarForm();
          // Actualizar el marcador en el mapa
          if (radaresMarkers[radarId]) {
            radaresMarkers[radarId].setIcon(getIconByRadar(updatedRadar));
            radaresMarkers[radarId].radarData = updatedRadar;
            if (radaresMarkers[radarId].isPopupOpen()) {
              radaresMarkers[radarId].setPopupContent(createPopupContent(updatedRadar, radarId));
            }
          }
        })
        .catch(error => {
          console.error("Error al actualizar radar:", error);
          alert('Error al actualizar el radar');
        })
        .finally(() => {
          saveButton.textContent = originalText;
          saveButton.disabled = false;
          // Restaurar función original del botón
          saveButton.onclick = function() {
            saveNewRadar();
          };
        });
    }

    // Función para copiar ID al portapapeles
    window.copyToClipboard = function(text) {
      navigator.clipboard.writeText(text).then(() => {
        // Mostrar feedback visual
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = "¡Copiado!";
        button.style.backgroundColor = "#4CAF50";
        button.style.color = "white";

        setTimeout(() => {
          button.textContent = originalText;
          button.style.backgroundColor = "#f0f0f0";
          button.style.color = "black";
        }, 2000);
      }).catch(err => {
        console.error('Error al copiar: ', err);
        alert('No se pudo copiar el ID');
      });
    };

    // Obtener icono según tipo de radar
    function getIconByRadar(radar) {
      if (radar.status === "pending_review") {
        return L.icon({
          iconUrl: 'https://ahorraunamulta.com/velocidades/default/pendiente.png',
          iconSize: [30, 30],
          iconAnchor: [15, 15]
        });
      }

      const validTypes = ["Fijo", "Móvil", "Tramo", "Remolque"];
      const type = validTypes.includes(radar.radarType) ? radar.radarType : "default";
      const speed = (radar.speed >= 10 && radar.speed <= 140) ? radar.speed : "default";

      const iconUrl = radar.status === "active"
        ? `https://ahorraunamulta.com/velocidades/${type}/${speed}.png`
        : `https://ahorraunamulta.com/velocidades/${type}/no_activo.png`;

      return L.icon({
        iconUrl,
        iconSize: radar.status === "active" ? [30, 30] : [25, 25],
        iconAnchor: [15, 15]
      });
    }

    // Actualizar listado de radares
    function updateRadarList(radares) {
      const activeList = document.getElementById('active-list');
      const inactiveList = document.getElementById('inactive-list');
      const activeListMobile = document.getElementById('active-list-mobile');
      const inactiveListMobile = document.getElementById('inactive-list-mobile');

      activeList.innerHTML = '<h3>Radares Activos</h3>';
      inactiveList.innerHTML = '<h3>Radares Inactivos</h3>';
      activeListMobile.innerHTML = '<h3>Radares Activos</h3>';
      inactiveListMobile.innerHTML = '<h3>Radares Inactivos</h3>';

      if (!radares) {
          activeList.innerHTML += '<p>No hay radares activos</p>';
          inactiveList.innerHTML += '<p>No hay radares inactivos</p>';
          activeListMobile.innerHTML += '<p>No hay radares activos</p>';
          inactiveListMobile.innerHTML += '<p>No hay radares inactivos</p>';
          return;
      }

      const radaresArray = Object.entries(radares).map(([key, value]) => ({ ...value, key }));
      radaresArray.sort((a, b) => {
          const dateA = a.last_updated ? new Date(a.last_updated) : 0;
          const dateB = b.last_updated ? new Date(b.last_updated) : 0;
          return dateB - dateA;
      });

      let hasActive = false;
      let hasInactive = false;

      radaresArray.forEach(radar => {
          const item = createRadarListItem(radar);

          if (radar.status === "active") {
              activeList.appendChild(item.cloneNode(true));
              activeListMobile.appendChild(item.cloneNode(true));
              hasActive = true;
          } else if (radar.status === "pending_review") {
              activeList.appendChild(item.cloneNode(true));
              activeListMobile.appendChild(item.cloneNode(true));
              hasActive = true;
          } else {
              inactiveList.appendChild(item.cloneNode(true));
              inactiveListMobile.appendChild(item.cloneNode(true));
              hasInactive = true;
          }
      });

      if (!hasActive) {
        activeList.innerHTML += '<p>No hay radares activos</p>';
        activeListMobile.innerHTML += '<p>No hay radares activos</p>';
      }
      if (!hasInactive) {
        inactiveList.innerHTML += '<p>No hay radares inactivos</p>';
        inactiveListMobile.innerHTML += '<p>No hay radares inactivos</p>';
      }

      // Aplicar filtros a los listados
      updateFilteredLists();
    }

    // Crear elemento de listado mejorado
    function createRadarListItem(radar) {
      const item = document.createElement('div');
      item.className = 'list-item';
      item.dataset.radarId = radar.key;

      const lastUpdated = radar.last_updated ? new Date(radar.last_updated).toLocaleString() : "No disponible";
      const statusClass = radar.status === "active" ? "status-active" :
                         radar.status === "pending_review" ? "status-pending" : "status-inactive";
      const statusText = radar.status === "active" ? "Activo" :
                        radar.status === "pending_review" ? "Pendiente" : "Inactivo";

      item.innerHTML = `
        <h3>${radar.radarType || "Radar"} - ${radar.road || ""}
          <span class="status-badge ${statusClass}">${statusText}</span>
        </h3>
        <p><strong>Ubicación:</strong> ${radar.lat?.toFixed(4)}, ${radar.lng?.toFixed(4)}</p>
        ${radar.pk ? `<p><strong>PK:</strong> ${radar.pk}</p>` : ''}
        <p><strong>Dirección:</strong> ${radar.direction || "N/A"}</p>
        <p><strong>Velocidad:</strong> ${radar.speed || "N/A"} km/h</p>
        <p><strong>Última modificación:</strong> ${lastUpdated}</p>
        <p><strong>Votos:</strong>
          <span style="color: #28a745;">👍 ${radar.votos_positivos || 0}</span> |
          <span style="color: #dc3545;">👎 ${radar.votos_negativos || 0}</span>
        </p>
      `;

      item.addEventListener('click', () => {
        centerOnRadar(radar.key);
      });

      return item;
    }

    // Actualizar listados filtrados
    function updateFilteredLists() {
      const activeLists = [
        document.getElementById('active-list'),
        document.getElementById('active-list-mobile')
      ];

      const inactiveLists = [
        document.getElementById('inactive-list'),
        document.getElementById('inactive-list-mobile')
      ];

      // Contadores
      let activeCount = 0, filteredActiveCount = 0;
      let inactiveCount = 0, filteredInactiveCount = 0;

      // Contar radares
      Object.values(radaresMarkers).forEach(marker => {
        const radar = marker.radarData;
        if (radar.status === 'active' || radar.status === 'pending_review') activeCount++;
        else inactiveCount++;
      });

      // Actualizar listados
      activeLists.forEach(list => {
        const items = list.querySelectorAll('.list-item');
        let visibleItems = 0;

        items.forEach(item => {
          const radarId = item.dataset.radarId;
          const radar = radaresMarkers[radarId]?.radarData;

          if (radar) {
            const matchesType = currentFilters.types.length === 0 ||
                              currentFilters.types.includes(radar.radarType);
            const matchesStatus = currentFilters.statuses.length === 0 ||
                                currentFilters.statuses.includes(radar.status);
            const matchesSpeed = !currentFilters.speed ||
                               (radar.speed && radar.speed.toString() === currentFilters.speed);

            if (matchesType && matchesStatus && matchesSpeed) {
              item.style.display = 'block';
              visibleItems++;
              if (radar.status === 'active' || radar.status === 'pending_review') filteredActiveCount++;
            } else {
              item.style.display = 'none';
            }
          }
        });

        const title = list.querySelector('h3');
        if (title) {
          title.innerHTML = currentFilters.types.length > 0 || currentFilters.statuses.length > 0 || currentFilters.speed ?
            `Radares Activos (${visibleItems}/${activeCount})` :
            `Radares Activos (${activeCount})`;
        }
      });

      inactiveLists.forEach(list => {
        const items = list.querySelectorAll('.list-item');
        let visibleItems = 0;

        items.forEach(item => {
          const radarId = item.dataset.radarId;
          const radar = radaresMarkers[radarId]?.radarData;

          if (radar) {
            const matchesType = currentFilters.types.length === 0 ||
                              currentFilters.types.includes(radar.radarType);
            const matchesStatus = currentFilters.statuses.length === 0 ||
                                currentFilters.statuses.includes(radar.status);
            const matchesSpeed = !currentFilters.speed ||
                               (radar.speed && radar.speed.toString() === currentFilters.speed);

            if (matchesType && matchesStatus && matchesSpeed) {
              item.style.display = 'block';
              visibleItems++;
              if (radar.status === 'inactive') filteredInactiveCount++;
            } else {
              item.style.display = 'none';
            }
          }
        });

        const title = list.querySelector('h3');
        if (title) {
          title.innerHTML = currentFilters.types.length > 0 || currentFilters.statuses.length > 0 || currentFilters.speed ?
            `Radares Inactivos (${visibleItems}/${inactiveCount})` :
            `Radares Inactivos (${inactiveCount})`;
        }
      });
    }

    // Actualizar listado móvil
    function updateMobileList() {
      updateFilteredLists();
    }

    // Centrar mapa en un radar específico
    function centerOnRadar(radarId) {
      const marker = radaresMarkers[radarId];
      if (marker) {
        map.setView(marker.getLatLng(), 16);
        marker.openPopup();

        // Destacar el elemento en el listado
        const listItems = document.querySelectorAll(`.list-item[data-radar-id="${radarId}"]`);
        listItems.forEach(item => {
          item.style.backgroundColor = '#e6f2ff';
          setTimeout(() => {
            item.style.backgroundColor = '';
          }, 2000);
        });
      }
    }

    // Alternar secciones
    function toggleSection(sectionId) {
      const section = document.getElementById(sectionId);
      section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }

    // Alternar radares inactivos
    window.toggleInactiveRadars = function() {
      showInactiveRadars = !showInactiveRadars;
      loadRadars();

      const button = document.getElementById('toggleInactiveRadarsButton');
      button.textContent = showInactiveRadars ? 'Ocultar inactivos' : 'Mostrar inactivos';
    }

    // Votar radar
    window.votar = function(radarId, tipo) {
      const today = new Date().toISOString().split('T')[0];
      const votoKey = `voto_${radarId}_${today}`;

      if (localStorage.getItem(votoKey)) {
        alert('Ya has votado en este radar hoy.');
        return;
      }

      localStorage.setItem(votoKey, 'true');

      const radarRef = db.ref(`radares/${radarId}`);
      const votosPosRef = db.ref(`radares/${radarId}/votos_positivos`);
      const votosNegRef = db.ref(`radares/${radarId}/votos_negativos`);

      // Animación al votar
      const buttonId = `boton_${tipo === 'positivo' ? 'positivo' : 'negativo'}_${radarId}`;
      const button = document.getElementById(buttonId);

      if (button) {
        button.style.transform = 'scale(1.2)';
        setTimeout(() => {
          button.style.transform = '';
        }, 300);
      }

      if (tipo === 'positivo') {
        votosPosRef.transaction(current => (current || 0) + 1);
        votosNegRef.transaction(current => (current > 0 ? current - 1 : 0));
      } else {
        votosNegRef.transaction(current => (current || 0) + 1);
        votosPosRef.transaction(current => (current > 0 ? current - 1 : 0));
      }

      radarRef.update({
        last_updated: new Date().toISOString()
      });

      votosNegRef.once('value', snap => {
        if (snap.val() > 10) {
          radarRef.update({ status: 'inactive' });
        }
      });
    }

    // Configurar event listeners
    function setupEventListeners() {
      document.getElementById('addRadarButton').addEventListener('click', () => {
        const button = document.getElementById('addRadarButton');
        button.classList.toggle('active');
        button.textContent = button.classList.contains('active') ? '🚨' : '➕';

        if (!button.classList.contains('active')) {
          document.getElementById('radarForm').style.display = 'none';
          if (tempMarker) map.removeLayer(tempMarker);
          tempMarker = null;
        }
      });

      map.on('click', e => {
        if (document.getElementById('addRadarButton').classList.contains('active')) {
          document.getElementById('radarForm').style.display = 'block';
          if (tempMarker) map.removeLayer(tempMarker);
          tempMarker = L.marker(e.latlng).addTo(map);
        }
      });

      document.getElementById('saveRadarButton').addEventListener('click', saveNewRadar);

      document.getElementById('cancelRadarButton').addEventListener('click', resetRadarForm);
    }

    // Función para guardar nuevo radar
    function saveNewRadar() {
      const radarType = document.getElementById('radarType').value;
      const road = document.getElementById('road').value;
      const pk = document.getElementById('pk').value;
      const direction = document.getElementById('direction').value;
      const speed = document.getElementById('speed').value;
      const status = document.getElementById('status').value;

      // Validación de campos
      const requiredFields = ['radarType', 'road', 'direction', 'speed'];
      let isValid = true;

      requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (!element.value) {
          element.style.borderColor = 'red';
          isValid = false;
        } else {
          element.style.borderColor = '';
        }
      });

      if (!isValid || !tempMarker) {
        if (!tempMarker) {
          alert('Por favor, selecciona una ubicación en el mapa haciendo clic');
        }
        return;
      }

      // Mostrar feedback de carga
      const saveButton = document.getElementById('saveRadarButton');
      const originalText = saveButton.textContent;
      saveButton.textContent = 'Guardando...';
      saveButton.disabled = true;

      const { lat, lng } = tempMarker.getLatLng();
      const newRadar = {
        radarType,
        road,
        pk: pk || null,
        direction,
        speed,
        status,
        lat,
        lng,
        votos_positivos: 0,
        votos_negativos: 0,
        last_updated: new Date().toISOString()
      };

      db.ref("radares").push(newRadar)
        .then(() => {
          alert('Radar guardado con éxito');
          resetRadarForm();
        })
        .catch(error => {
          console.error("Error al guardar radar:", error);
          alert('Error al guardar el radar');
        })
        .finally(() => {
          saveButton.textContent = originalText;
          saveButton.disabled = false;
        });
    }

    // Resetear formulario de radar
    function resetRadarForm() {
      document.getElementById('radarForm').style.display = 'none';
      if (tempMarker) {
        map.removeLayer(tempMarker);
        tempMarker = null;
      }
      document.getElementById('addRadarButton').classList.remove('active');
      document.getElementById('addRadarButton').textContent = '➕';

      // Limpiar campos y validaciones
      document.getElementById('radarType').value = '';
      document.getElementById('road').value = '';
      document.getElementById('pk').value = '';
      document.getElementById('direction').value = '';
      document.getElementById('speed').value = '';
      document.getElementById('status').value = 'active';

      document.querySelectorAll('#radarForm input, #radarForm select').forEach(el => {
        el.style.borderColor = '';
      });

      // Restaurar función original del botón de guardar
      document.getElementById('saveRadarButton').textContent = 'Guardar Radar';
      document.getElementById('saveRadarButton').onclick = saveNewRadar;
    }

    // Cerrar popup de descargo
    function closePopup() {
      const popupOverlay = document.getElementById('popup-overlay');
      popupOverlay.style.display = 'none';
      localStorage.setItem('popupSeen', 'true');
    }

    // Función para búsqueda de radares con autocompletado
    function setupSearch() {
      const searchInput = document.getElementById('searchInput');
      const suggestionsContainer = document.getElementById('search-suggestions');
      let currentSuggestions = [];
      let selectedSuggestion = -1;

      searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        suggestionsContainer.innerHTML = '';
        suggestionsContainer.style.display = 'none';
        currentSuggestions = [];
        selectedSuggestion = -1;

        if (searchTerm.length > 2) {
          // Buscar coincidencias en los radares
          Object.entries(radaresMarkers).forEach(([id, marker]) => {
            const radar = marker.radarData;
            const roadMatch = radar.road?.toLowerCase().includes(searchTerm);
            const directionMatch = radar.direction?.toLowerCase().includes(searchTerm);
            const pkMatch = radar.pk?.toString().includes(searchTerm);

            if (roadMatch || directionMatch || pkMatch) {
              currentSuggestions.push({
                id,
                text: `${radar.road} ${radar.pk ? 'PK ' + radar.pk : ''} - ${radar.direction}`
              });
            }
          });

          // Mostrar sugerencias
          if (currentSuggestions.length > 0) {
            currentSuggestions.slice(0, 5).forEach((suggestion, index) => {
              const suggestionElement = document.createElement('div');
              suggestionElement.className = 'search-suggestion';
              suggestionElement.textContent = suggestion.text;
              suggestionElement.addEventListener('click', () => {
                centerOnRadar(suggestion.id);
                searchInput.value = suggestion.text;
                suggestionsContainer.style.display = 'none';
              });
              suggestionsContainer.appendChild(suggestionElement);
            });
            suggestionsContainer.style.display = 'block';
          }

          // Resaltar marcadores coincidentes
          Object.values(radaresMarkers).forEach(marker => {
            const radar = marker.radarData;
            const matches = radar.road?.toLowerCase().includes(searchTerm) ||
                          radar.direction?.toLowerCase().includes(searchTerm) ||
                          radar.pk?.toString().includes(searchTerm);

            if (matches) {
              marker.addTo(map);
              marker.setOpacity(1);
              marker.setZIndexOffset(10000);
            } else {
              marker.setOpacity(0.5);
              marker.setZIndexOffset(0);
            }
          });
        } else {
          // Restablecer todos los marcadores
          Object.values(radaresMarkers).forEach(marker => {
            marker.setOpacity(1);
            marker.setZIndexOffset(0);
          });
        }
      });

      // Manejar teclado para navegación en sugerencias
      searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          if (selectedSuggestion < currentSuggestions.length - 1) {
            selectedSuggestion++;
            updateSelectedSuggestion();
          }
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          if (selectedSuggestion > 0) {
            selectedSuggestion--;
            updateSelectedSuggestion();
          }
        } else if (e.key === 'Enter' && selectedSuggestion >= 0) {
          e.preventDefault();
          const suggestion = currentSuggestions[selectedSuggestion];
          centerOnRadar(suggestion.id);
          searchInput.value = suggestion.text;
          suggestionsContainer.style.display = 'none';
        } else if (e.key === 'Escape') {
          suggestionsContainer.style.display = 'none';
        }
      });

      // Cerrar sugerencias al hacer clic fuera
      document.addEventListener('click', (e) => {
        if (e.target !== searchInput && e.target.closest('.search-suggestions') === null) {
          suggestionsContainer.style.display = 'none';
        }
      });

      function updateSelectedSuggestion() {
        document.querySelectorAll('.search-suggestion').forEach((el, index) => {
          el.classList.toggle('highlighted', index === selectedSuggestion);
          if (index === selectedSuggestion) {
            el.scrollIntoView({ block: 'nearest' });
          }
        });
      }
    }

    // Inicializar la aplicación
    document.addEventListener('DOMContentLoaded', () => {
      initMap();

      // Cargar voces disponibles cuando estén listas
      if ('speechSynthesis' in window) {
        speechSynthesis.onvoiceschanged = function() {
          const voices = window.speechSynthesis.getVoices();
          const femaleVoice = voices.find(v => v.name.includes('female') && v.lang.includes('es'));
          if (femaleVoice) {
            currentVoice = femaleVoice;
          }
        };
      }

      if (!localStorage.getItem('popupSeen')) {
        setTimeout(() => {
          const popup = document.getElementById('popup-overlay');
          popup.style.display = 'flex';

          // Guardar solo si el usuario cierra el popup
          document.querySelector('.popup button').addEventListener('click', () => {
            localStorage.setItem('popupSeen', 'true');
          });
        }, 3000);
      }
    });
  </script>

  <?php
  include 'log_ip.php';
  ?>
</body>
</html>
