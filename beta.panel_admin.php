<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="https://ahorraunamulta.com/favicon.png" />
  <title>PANEL DE ADMINISTRACIÓN - AHORRA UNA MULTA</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
    import { getAuth, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-auth.js";
    import { getDatabase, ref, onValue, update, remove, get, push, set } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";

    const firebaseConfigLogin = {
      apiKey: "AIzaSyAX4uy3ON91cwK3Tt9r5Eqpucyf4sfv0No",
      authDomain: "login-radares.firebaseapp.com",
      projectId: "login-radares",
      storageBucket: "login-radares.firebasestorage.app",
      messagingSenderId: "661760692554",
      appId: "1:661760692554:web:2da6e767592800380eb1b3",
      measurementId: "G-S2ZCB85HX1"
    };

    const firebaseConfigRadares = {
      apiKey: "AIzaSyDNCBnqAAcdV3kqx8hN-uMyqSkzIzV4DXc",
      authDomain: "radares-bcn.firebaseapp.com",
      databaseURL: "https://radares-bcn-default-rtdb.europe-west1.firebasedatabase.app",
      projectId: "radares-bcn",
      storageBucket: "radares-bcn.appspot.com",
      messagingSenderId: "892778900332",
      appId: "1:892778900332:web:f3c5353d981dda7b4ba149",
      measurementId: "G-C1GT8Q96ZJ"
    };

    const appLogin = initializeApp(firebaseConfigLogin);
    const appRadares = initializeApp(firebaseConfigRadares, "radares");

    const auth = getAuth(appLogin);
    const database = getDatabase(appRadares);

    let allRadares = [];
    let currentPage = 1;
    const itemsPerPage = 20;

    // Variables para el mapa
    let adminMap;
    let editableMarker;
    let newRadarMarker;
    let currentEditingRadarId;
    let addingNewRadar = false;

    onAuthStateChanged(auth, (user) => {
      if (!user) {
        window.location.href = 'login.html';
      } else {
        document.getElementById('userInfo').innerText = `Bienvenido, ${user.email}`;
        document.getElementById('userEmail').textContent = user.email;
        showBetaWarning();
        fetchRadares();
        addNewRadarButton();
      }
    });

    // Función para mostrar aviso beta
    function showBetaWarning() {
      if (!document.getElementById('betaWarningModal')) {
        const modalHTML = `
        <div id="betaWarningModal" class="modal">
          <div class="edit-all-modal-content">
            <h2>AVISO: Versión Beta</h2>
            <p>Esta versión del panel de administración está en fase beta. Algunas funciones pueden no funcionar correctamente.</p>
            <p>Por favor, reporta cualquier problema que encuentres.</p>
            <div style="text-align: center; margin-top: 20px;">
              <button id="acceptBetaBtn" class="confirm-btn">Entendido</button>
            </div>
          </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        document.getElementById('acceptBetaBtn').addEventListener('click', () => {
          document.getElementById('betaWarningModal').style.display = 'none';
        });
      }

      document.getElementById('betaWarningModal').style.display = 'flex';
    }

    // Función para añadir botón de nuevo radar
    function addNewRadarButton() {
      const controlsDiv = document.querySelector('.controls');
      if (document.getElementById('newRadarBtn')) return;

      const newRadarBtn = document.createElement('button');
      newRadarBtn.id = 'newRadarBtn';
      newRadarBtn.className = 'confirm-btn';
      newRadarBtn.textContent = '➕ Añadir Nuevo Radar';
      newRadarBtn.style.marginLeft = 'auto';
      newRadarBtn.addEventListener('click', showLocationInputModal);
      controlsDiv.appendChild(newRadarBtn);
    }

    // Modal para entrada de ubicación
    function showLocationInputModal() {
      if (!document.getElementById('locationInputModal')) {
        const modalHTML = `
        <div id="locationInputModal" class="modal">
          <div class="edit-all-modal-content">
            <button class="close-modal" id="closeLocationInputModal">&times;</button>
            <h2>Añadir Nuevo Radar - Paso 1</h2>
            <div class="form-group">
              <label for="initialLocationInput">Introduce las coordenadas:</label>
              <input type="text" id="initialLocationInput" placeholder="Ejemplo: 41.3851, 2.1734">
              <p style="font-size: 12px; color: #666; margin-top: 5px;">
                Formato: latitud,longitud<br>
                Ejemplo: 41.3851, 2.1734
              </p>
            </div>
            <div class="form-buttons">
              <button id="cancelLocationInputBtn" class="cancel-btn">Cancelar</button>
              <button id="confirmLocationBtn" class="confirm-btn">Continuar</button>
            </div>
          </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        document.getElementById('closeLocationInputModal').addEventListener('click', () => {
          document.getElementById('locationInputModal').style.display = 'none';
        });

        document.getElementById('cancelLocationInputBtn').addEventListener('click', () => {
          document.getElementById('locationInputModal').style.display = 'none';
        });

        document.getElementById('confirmLocationBtn').addEventListener('click', processLocationInput);
      }

      document.getElementById('initialLocationInput').value = '';
      document.getElementById('locationInputModal').style.display = 'flex';
      document.getElementById('initialLocationInput').focus();
    }

    // Función para procesar la entrada de ubicación
    function processLocationInput() {
      const input = document.getElementById('initialLocationInput').value.trim();
      if (!input) {
        showNotification('Por favor ingresa las coordenadas', 'warning');
        return;
      }

      const coords = input.split(/[, ]+/);
      if (coords.length < 2) {
        showNotification('Formato incorrecto. Usa "latitud,longitud"', 'error');
        return;
      }

      const lat = parseFloat(coords[0]);
      const lng = parseFloat(coords[1]);

      if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
        showNotification('Coordenadas inválidas. La latitud debe estar entre -90 y 90, y la longitud entre -180 y 180', 'error');
        return;
      }

      document.getElementById('locationInputModal').style.display = 'none';
      initAdminMap(lat, lng, true);
    }

    const fetchRadares = () => {
      showLoading(true);
      const radaresRef = ref(database, 'radares');
      onValue(radaresRef, (snapshot) => {
        const data = snapshot.val();
        showLoading(false);

        if (data) {
          allRadares = Object.keys(data).map((key) => {
            return { id: key, ...data[key] };
          });

          allRadares.forEach((radar) => {
            if (radar.last_updated) {
              radar.last_updated = new Date(radar.last_updated).getTime();
            }
          });

          updateDashboardStats();
          renderRadares();
          updateStatusOptions();
        } else {
          allRadares = [];
          document.getElementById('radares-table-body').innerHTML = '<tr><td colspan="9">No se encontraron radares</td></tr>';
        }
      }, (error) => {
        showLoading(false);
        showNotification(`Error al cargar radares: ${error.message}`, 'error');
      });
    };

    function logChange(radarId, fieldChanged, oldValue, newValue, changedBy) {
      const changeRef = ref(database, `historial/${radarId}/${Date.now()}`);
      const changeData = {
        field: fieldChanged,
        oldValue: oldValue,
        newValue: newValue,
        changedBy: changedBy,
        timestamp: Date.now()
      };

      return update(changeRef, changeData);
    }

    async function updateRadarField(id, field, value) {
      const radarRef = ref(database, `radares/${id}`);
      const userEmail = document.getElementById('userEmail').textContent;

      try {
        const snapshot = await get(radarRef);
        const oldValue = snapshot.val()[field];

        await update(radarRef, {
          [field]: field === 'speed' ? parseInt(value) : value,
          last_updated: Date.now(),
          updated_by: userEmail
        });

        if (oldValue !== value) {
          await logChange(id, field, oldValue, value, userEmail);
        }

        return true;
      } catch (error) {
        console.error("Error updating radar:", error);
        return false;
      }
    }

    function showHistoryModal(radarId) {
      const historyRef = ref(database, `historial/${radarId}`);
      const modalContent = document.getElementById('historyModalContent');

      modalContent.innerHTML = '<p>Cargando historial...</p>';
      document.getElementById('historyModal').style.display = 'flex';

      onValue(historyRef, (snapshot) => {
        const historyData = snapshot.val();

        if (!historyData) {
          modalContent.innerHTML = '<p>No hay historial de cambios para este radar.</p>';
          return;
        }

        let historyHTML = '<table class="history-table"><thead><tr><th>Fecha</th><th>Campo</th><th>Valor Anterior</th><th>Valor Nuevo</th><th>Usuario</th></tr></thead><tbody>';

        Object.entries(historyData)
          .sort((a, b) => b[0] - a[0])
          .forEach(([timestamp, change]) => {
            historyHTML += `
              <tr>
                <td>${formatDate(parseInt(timestamp))}</td>
                <td>${change.field}</td>
                <td>${change.oldValue || 'N/A'}</td>
                <td>${change.newValue || 'N/A'}</td>
                <td>${change.changedBy}</td>
              </tr>
            `;
          });

        historyHTML += '</tbody></table>';
        modalContent.innerHTML = historyHTML;
      });
    }

    // Función para inicializar/actualizar el mapa
    function initAdminMap(lat, lng, isNewRadar = false) {
      if (!adminMap) {
        adminMap = L.map('adminMap').setView([lat, lng], 15);

        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
          attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        const topoLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
          attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
        });

        osmLayer.addTo(adminMap);
        L.control.layers({
          "Mapa Callejero": osmLayer,
          "Mapa Topográfico": topoLayer,
          "Vista Satélite": satelliteLayer
        }).addTo(adminMap);
      } else {
        adminMap.setView([lat, lng], 15);
      }

      if (editableMarker) adminMap.removeLayer(editableMarker);
      if (newRadarMarker) adminMap.removeLayer(newRadarMarker);

      if (isNewRadar) {
        addingNewRadar = true;
        document.getElementById('mapModal').style.display = 'none';
        showNewRadarForm(lat, lng);

        newRadarMarker = L.marker([lat, lng], {
          draggable: true,
          icon: L.icon({
            iconUrl: 'https://ahorraunamulta.com/velocidades/default/pendiente.png',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
          })
        }).addTo(adminMap);

        newRadarMarker.on('dragend', function() {
          updateNewRadarCoords();
        });
      } else {
        addingNewRadar = false;
        editableMarker = L.marker([lat, lng], {
          draggable: true,
          icon: L.icon({
            iconUrl: 'https://ahorraunamulta.com/velocidades/default/pendiente.png',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
          })
        }).addTo(adminMap);
      }

      enhanceMapModal(isNewRadar);
    }

    // Función para mostrar formulario de nuevo radar
    function showNewRadarForm(lat, lng) {
      if (!document.getElementById('newRadarModal')) {
        const modalHTML = `
        <div id="newRadarModal" class="modal">
          <div class="edit-all-modal-content">
            <button class="close-modal" id="closeNewRadarModal">&times;</button>
            <h2>Añadir Nuevo Radar</h2>
            <div class="edit-form">
              <div class="form-group">
                <label for="newRadarLat">Latitud:</label>
                <input type="text" id="newRadarLat" readonly>
              </div>
              <div class="form-group">
                <label for="newRadarLng">Longitud:</label>
                <input type="text" id="newRadarLng" readonly>
              </div>
              <div class="form-group">
                <label for="newRadarDirection">Dirección:</label>
                <input type="text" id="newRadarDirection">
              </div>
              <div class="form-group">
                <label for="newRadarSpeed">Velocidad (km/h):</label>
                <input type="number" id="newRadarSpeed" min="0" step="1">
              </div>
              <div class="form-group">
                <label for="newRadarStatus">Estado:</label>
                <select id="newRadarStatus">
                  <option value="active">Activo</option>
                  <option value="inactive">Inactivo</option>
                  <option value="hidden">Oculto</option>
                  <option value="pending_review" selected>Pendiente</option>
                </select>
              </div>
              <div class="form-group">
                <label for="newRadarRoad">Vía:</label>
                <input type="text" id="newRadarRoad">
              </div>
              <div class="form-group">
                <label for="newRadarPk">PK:</label>
                <input type="text" id="newRadarPk">
              </div>
              <div class="form-group">
                <label for="newRadarType">Tipo de radar:</label>
                <select id="newRadarType">
                  <option value="Fijo">Fijo</option>
                  <option value="Móvil">Móvil</option>
                  <option value="Tramo">Tramo</option>
                  <option value="Remolque">Remolque</option>
                </select>
              </div>
              <div class="form-buttons">
                <button id="cancelNewRadarBtn" class="cancel-btn">Cancelar</button>
                <button id="saveNewRadarBtn" class="confirm-btn">Guardar Radar</button>
              </div>
            </div>
          </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        document.getElementById('closeNewRadarModal').addEventListener('click', closeNewRadarModal);
        document.getElementById('cancelNewRadarBtn').addEventListener('click', closeNewRadarModal);
        document.getElementById('saveNewRadarBtn').addEventListener('click', saveNewRadar);
      }

      document.getElementById('newRadarLat').value = lat.toFixed(6);
      document.getElementById('newRadarLng').value = lng.toFixed(6);
      document.getElementById('newRadarModal').style.display = 'flex';
    }

    function updateNewRadarCoords() {
      if (newRadarMarker) {
        const latLng = newRadarMarker.getLatLng();
        document.getElementById('newRadarLat').value = latLng.lat.toFixed(6);
        document.getElementById('newRadarLng').value = latLng.lng.toFixed(6);
      }
    }

    function saveNewRadar() {
      const newRadar = {
        lat: parseFloat(document.getElementById('newRadarLat').value),
        lng: parseFloat(document.getElementById('newRadarLng').value),
        direction: document.getElementById('newRadarDirection').value.trim() || 'Sin especificar',
        speed: parseInt(document.getElementById('newRadarSpeed').value) || 0,
        status: document.getElementById('newRadarStatus').value,
        road: document.getElementById('newRadarRoad').value.trim() || 'Sin especificar',
        pk: document.getElementById('newRadarPk').value.trim() || '',
        radarType: document.getElementById('newRadarType').value,
        last_updated: Date.now(),
        updated_by: document.getElementById('userEmail').textContent
      };

      // Validación mínima
      if (isNaN(newRadar.speed)) {
        showNotification('La velocidad debe ser un número válido', 'error');
        return;
      }

      showLoading(true);
      const radaresRef = ref(database, 'radares');
      const newRadarRef = push(radaresRef);

      set(newRadarRef, newRadar)
        .then(() => {
          showNotification('Nuevo radar añadido correctamente', 'success');
          closeNewRadarModal();
          fetchRadares();
        })
        .catch(error => {
          showNotification(`Error al guardar radar: ${error.message}`, 'error');
        })
        .finally(() => {
          showLoading(false);
        });
    }

    function closeNewRadarModal() {
      document.getElementById('newRadarModal').style.display = 'none';
      addingNewRadar = false;
      if (newRadarMarker) {
        adminMap.removeLayer(newRadarMarker);
        newRadarMarker = null;
      }
    }

    const renderRadares = (radares = allRadares) => {
      const filteredRadares = filterRadares(radares);
      const sortedRadares = sortRadares(filteredRadares);
      const paginatedRadares = paginateRadares(sortedRadares);

      const radaresTableBody = document.getElementById('radares-table-body');
      radaresTableBody.innerHTML = '';

      if (paginatedRadares.length === 0) {
        radaresTableBody.innerHTML = '<tr><td colspan="10">No se encontraron radares con los filtros aplicados</td></tr>';
        return;
      }

      paginatedRadares.forEach((radar) => {
        const row = document.createElement('tr');
        row.className = `status-${radar.status || 'pending_review'}`;

        const createEditableCell = (value, fieldName, isNumber = false) => {
          return `
            <td>
              <input type="${isNumber ? 'number' : 'text'}"
                     value="${value || ''}"
                     data-field="${fieldName}"
                     data-id="${radar.id}"
                     ${isNumber ? 'min="0" step="1"' : ''}>
              <button class="confirm-btn" data-field="${fieldName}" data-id="${radar.id}">✓</button>
            </td>
          `;
        };

        const lastUpdated = radar.last_updated ? formatDate(radar.last_updated) : 'N/A';
        const roadValue = radar.road || 'Sin especificar';

        row.innerHTML = `
          <td>
            <button class="toggle-id-btn" data-id="${radar.id}">🔍</button>
            <span class="radar-id" style="display: none;">${radar.id}</span>
          </td>
          ${createEditableCell(radar.direction, 'direction')}
          ${createEditableCell(radar.speed, 'speed', true)}
          <td>
            <select data-field="status" data-id="${radar.id}" class="status-select">
              <option value="active" ${radar.status === 'active' ? 'selected' : ''}>Activo</option>
              <option value="inactive" ${radar.status === 'inactive' ? 'selected' : ''}>Inactivo</option>
              <option value="hidden" ${radar.status === 'hidden' ? 'selected' : ''}>Oculto</option>
              <option value="pending_review" ${radar.status === 'pending_review' ? 'selected' : ''}>Pendiente</option>
            </select>
            <button class="confirm-btn" data-field="status" data-id="${radar.id}">✓</button>
          </td>
          ${createEditableCell(roadValue, 'road')}
          ${createEditableCell(radar.pk, 'pk')}
          <td>${lastUpdated}</td>
          <td>
            <select data-field="radarType" data-id="${radar.id}">
              <option value="Fijo" ${radar.radarType === 'Fijo' ? 'selected' : ''}>Fijo</option>
              <option value="Móvil" ${radar.radarType === 'Móvil' ? 'selected' : ''}>Móvil</option>
              <option value="Tramo" ${radar.radarType === 'Tramo' ? 'selected' : ''}>Tramo</option>
              <option value="Remolque" ${radar.radarType === 'Remolque' ? 'selected' : ''}>Remolque</option>
            </select>
            <button class="confirm-btn" data-field="radarType" data-id="${radar.id}">✓</button>
          </td>
          <td class="actions">
            ${radar.status !== 'active' ? `<button class="approve-btn" data-id="${radar.id}">✅ Activar</button>` : ''}
            ${radar.status !== 'inactive' ? `<button class="reject-btn" data-id="${radar.id}">❌ Desactivar</button>` : ''}
            ${radar.status === 'pending_review' ? `<button class="complete-btn" data-id="${radar.id}">✔️ Completar</button>` : ''}
            <button class="edit-location-btn" data-id="${radar.id}" data-lat="${radar.lat}" data-lng="${radar.lng}">📍 Editar ubicación</button>
            <button class="view-map-btn" data-id="${radar.id}" data-lat="${radar.lat}" data-lng="${radar.lng}">🗺️ Ver en mapa</button>
            <button class="history-btn" data-id="${radar.id}">📜 Historial</button>
            <button class="edit-all-btn" data-id="${radar.id}">✏️ Editar todo</button>
            <button class="delete-btn" data-id="${radar.id}">🗑️ Eliminar</button>
          </td>
        `;

        radaresTableBody.appendChild(row);
      });

      setupEventListeners();
      updatePaginationControls(sortedRadares.length);
    };

    function getIconByRadar(radar) {
      if (radar.status === "hidden") return null;

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

    function updateStatusOptions() {
      document.querySelectorAll('.status-select').forEach(select => {
        if (!select.querySelector('option[value="hidden"]')) {
          const option = document.createElement('option');
          option.value = 'hidden';
          option.textContent = 'Oculto';
          select.appendChild(option);
        }
      });

      const editStatusSelect = document.getElementById('editStatus');
      if (editStatusSelect && !editStatusSelect.querySelector('option[value="hidden"]')) {
        const option = document.createElement('option');
        option.value = 'hidden';
        option.textContent = 'Oculto';
        editStatusSelect.appendChild(option);
      }
    }

    function showMapModal(radarId, lat, lng) {
      currentEditingRadarId = radarId;
      document.getElementById('mapModal').style.display = 'flex';
      initAdminMap(parseFloat(lat), parseFloat(lng));
    }

    function showEditAllModal(radarId) {
      const radar = allRadares.find(r => r.id === radarId);
      if (!radar) return;

      currentEditingRadarId = radarId;
      document.getElementById('editDirection').value = radar.direction || '';
      document.getElementById('editSpeed').value = radar.speed || '';
      document.getElementById('editStatus').value = radar.status || 'pending_review';
      document.getElementById('editRoad').value = radar.road || '';
      document.getElementById('editPk').value = radar.pk || '';
      document.getElementById('editRadarType').value = radar.radarType || 'Fijo';

      document.getElementById('editAllModal').style.display = 'flex';
    }

    function closeMapModal() {
      document.getElementById('mapModal').style.display = 'none';
      currentEditingRadarId = null;
    }

    function closeEditAllModal() {
      document.getElementById('editAllModal').style.display = 'none';
      currentEditingRadarId = null;
    }

    function saveRadarLocation() {
      if (!currentEditingRadarId || !editableMarker) return;

      const newLatLng = editableMarker.getLatLng();
      const radarRef = ref(database, `radares/${currentEditingRadarId}`);
      const userEmail = document.getElementById('userEmail').textContent;

      get(radarRef).then((snapshot) => {
        const currentData = snapshot.val();

        update(radarRef, {
          lat: newLatLng.lat,
          lng: newLatLng.lng,
          last_updated: Date.now(),
          updated_by: userEmail
        })
          .then(() => {
            logChange(currentEditingRadarId, 'lat', currentData.lat, newLatLng.lat, userEmail);
            logChange(currentEditingRadarId, 'lng', currentData.lng, newLatLng.lng, userEmail);
            showNotification('Ubicación del radar actualizada correctamente.', 'success');
            closeMapModal();
            fetchRadares();
          })
          .catch((error) => {
            showNotification(`Error al actualizar ubicación: ${error.message}`, 'error');
          });
      });
    }

    function saveAllRadarData() {
      if (!currentEditingRadarId) return;

      const radarRef = ref(database, `radares/${currentEditingRadarId}`);
      const userEmail = document.getElementById('userEmail').textContent;

      const updateData = {
        direction: document.getElementById('editDirection').value,
        speed: parseInt(document.getElementById('editSpeed').value) || 0,
        status: document.getElementById('editStatus').value,
        road: document.getElementById('editRoad').value,
        pk: document.getElementById('editPk').value,
        radarType: document.getElementById('editRadarType').value,
        last_updated: Date.now(),
        updated_by: userEmail
      };

      if (isNaN(updateData.speed)) {
        showNotification('La velocidad debe ser un número', 'error');
        return;
      }

      get(radarRef).then((snapshot) => {
        const currentData = snapshot.val();

        update(radarRef, updateData)
          .then(() => {
            Object.keys(updateData).forEach(key => {
              if (key !== 'last_updated' && key !== 'updated_by' && currentData[key] !== updateData[key]) {
                logChange(currentEditingRadarId, key, currentData[key], updateData[key], userEmail);
              }
            });

            showNotification('Datos del radar actualizados correctamente.', 'success');
            closeEditAllModal();
            fetchRadares();
          })
          .catch((error) => {
            showNotification(`Error al actualizar datos: ${error.message}`, 'error');
          });
      });
    }

    const filterRadares = (radares) => {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const statusFilter = document.getElementById('statusFilter').value;
      const typeFilter = document.getElementById('typeFilter').value;

      return radares.filter(radar => {
        const matchesSearch =
          (radar.direction && radar.direction.toLowerCase().includes(searchTerm)) ||
          (radar.road && radar.road.toLowerCase().includes(searchTerm)) ||
          (radar.pk && radar.pk.toString().includes(searchTerm)) ||
          radar.id.toLowerCase().includes(searchTerm);

        const matchesStatus = statusFilter === 'all' || radar.status === statusFilter;
        const matchesType = typeFilter === 'all' || radar.radarType === typeFilter;

        return matchesSearch && matchesStatus && matchesType;
      });
    };

    const sortRadares = (radares) => {
      const sortBy = document.getElementById('sortSelect').value;
      const sortOrder = document.getElementById('sortOrder').value === 'asc' ? 1 : -1;

      return [...radares].sort((a, b) => {
        const valA = a[sortBy] || '';
        const valB = b[sortBy] || '';

        if (sortBy === 'last_updated') {
          return (valA - valB) * sortOrder;
        }
        return String(valA).localeCompare(String(valB)) * sortOrder;
      });
    };

    const paginateRadares = (radares) => {
      const startIndex = (currentPage - 1) * itemsPerPage;
      return radares.slice(startIndex, startIndex + itemsPerPage);
    };

    const updateDashboardStats = () => {
      const stats = {
        total: allRadares.length,
        active: allRadares.filter(r => r.status === 'active').length,
        inactive: allRadares.filter(r => r.status === 'inactive').length,
        hidden: allRadares.filter(r => r.status === 'hidden').length,
        pending: allRadares.filter(r => r.status === 'pending_review').length
      };

      document.getElementById('totalRadars').textContent = stats.total;
      document.getElementById('activeRadars').textContent = stats.active;
      document.getElementById('inactiveRadars').textContent = stats.inactive;
      document.getElementById('hiddenRadars').textContent = stats.hidden;
      document.getElementById('pendingRadars').textContent = stats.pending;
    };

    const updatePaginationControls = (totalItems) => {
      const totalPages = Math.ceil(totalItems / itemsPerPage);
      const paginationDiv = document.getElementById('paginationControls');

      paginationDiv.innerHTML = `
        <button class="page-btn" data-page="first" ${currentPage === 1 ? 'disabled' : ''}>⏮️ Primera</button>
        <button class="page-btn" data-page="prev" ${currentPage === 1 ? 'disabled' : ''}>◀️ Anterior</button>
        <span>Página ${currentPage} de ${totalPages}</span>
        <button class="page-btn" data-page="next" ${currentPage >= totalPages ? 'disabled' : ''}>Siguiente ▶️</button>
        <button class="page-btn" data-page="last" ${currentPage >= totalPages ? 'disabled' : ''}>Última ⏭️</button>
      `;
    };

    const setupEventListeners = () => {
      document.querySelectorAll('.toggle-id-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const idSpan = e.target.nextElementSibling;
          if (idSpan.style.display === 'none') {
            idSpan.style.display = 'inline';
            e.target.textContent = '❌';
          } else {
            idSpan.style.display = 'none';
            e.target.textContent = '🔍';
          }
        });
      });

      document.querySelectorAll('.confirm-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const field = e.target.dataset.field;
          const id = e.target.dataset.id;

          if (!field || !id) return;

          let inputElement = e.target.parentElement.querySelector('input, select');

          if (!inputElement) {
            return;
          }

          const newValue = inputElement.value;

          if (field === 'speed' && (isNaN(newValue) || newValue < 0)) {
            showNotification('La velocidad debe ser un número positivo', 'error');
            return;
          }

          if (confirm(`¿Confirmas que deseas actualizar el campo ${field} a "${newValue}"?`)) {
            updateRadarField(id, field, newValue)
              .then(success => {
                if (success) {
                  showNotification(`Campo ${field} actualizado correctamente.`, 'success');
                }
              });
          }
        });
      });

      document.querySelectorAll('.approve-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          if (confirm('¿Confirmas que deseas activar este radar?')) {
            const id = btn.dataset.id;
            updateRadarStatus(id, 'active');
          }
        });
      });

      document.querySelectorAll('.reject-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          if (confirm('¿Confirmas que deseas desactivar este radar?')) {
            const id = btn.dataset.id;
            updateRadarStatus(id, 'inactive');
          }
        });
      });

      document.querySelectorAll('.complete-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          if (confirm('¿Confirmas que deseas marcar este radar como completado y activo?')) {
            const id = btn.dataset.id;
            updateRadarStatus(id, 'active');
          }
        });
      });

      document.querySelectorAll('.edit-location-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const radarId = btn.dataset.id;
          const lat = btn.dataset.lat;
          const lng = btn.dataset.lng;
          showMapModal(radarId, lat, lng);
        });
      });

      document.querySelectorAll('.edit-all-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const radarId = btn.dataset.id;
          showEditAllModal(radarId);
        });
      });

      document.querySelectorAll('.view-map-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const id = btn.dataset.id;
          const lat = btn.dataset.lat;
          const lng = btn.dataset.lng;
          viewRadarOnMap(id, lat, lng);
        });
      });

      document.querySelectorAll('.history-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const radarId = btn.dataset.id;
          showHistoryModal(radarId);
        });
      });

      document.querySelectorAll('.delete-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const id = btn.dataset.id;
          if (confirm('¿Estás seguro de que deseas eliminar permanentemente este radar? Esta acción no se puede deshacer.')) {
            deleteRadar(id);
          }
        });
      });
    };

    const viewRadarOnMap = (id, lat, lng) => {
      const url = `index.php?radar=${id}&lat=${lat}&lng=${lng}`;
      window.open(url, '_blank');
    };

    const updateRadarStatus = (id, status) => {
      const radarRef = ref(database, `radares/${id}`);
      const userEmail = document.getElementById('userEmail').textContent;

      get(radarRef).then((snapshot) => {
        const currentStatus = snapshot.val().status;

        update(radarRef, {
          status: status,
          last_updated: Date.now(),
          updated_by: userEmail
        })
          .then(() => {
            logChange(id, 'status', currentStatus, status, userEmail);
            showNotification(`Radar ${id} actualizado a ${status}.`, 'success');
            fetchRadares();
          })
          .catch((error) => {
            showNotification(`Error: ${error.message}`, 'error');
          });
      });
    };

    const deleteRadar = (id) => {
      showLoading(true);
      const radarRef = ref(database, `radares/${id}`);
      remove(radarRef)
        .then(() => {
          showNotification(`Radar ${id} eliminado correctamente.`, 'success');
          fetchRadares();
        })
        .catch((error) => {
          showLoading(false);
          showNotification(`Error al eliminar radar: ${error.message}`, 'error');
        });
    };

    document.getElementById('searchInput').addEventListener('input', () => {
      currentPage = 1;
      renderRadares();
    });

    document.getElementById('statusFilter').addEventListener('change', () => {
      currentPage = 1;
      renderRadares();
    });

    document.getElementById('typeFilter').addEventListener('change', () => {
      currentPage = 1;
      renderRadares();
    });

    document.getElementById('sortSelect').addEventListener('change', () => {
      renderRadares();
    });

    document.getElementById('sortOrder').addEventListener('change', () => {
      renderRadares();
    });

    document.getElementById('paginationControls').addEventListener('click', (e) => {
      if (e.target.classList.contains('page-btn')) {
        const action = e.target.dataset.page;
        const totalPages = Math.ceil(filterRadares(allRadares).length / itemsPerPage);

        switch(action) {
          case 'first':
            currentPage = 1;
            break;
          case 'prev':
            if (currentPage > 1) currentPage--;
            break;
          case 'next':
            if (currentPage < totalPages) currentPage++;
            break;
          case 'last':
            currentPage = totalPages;
            break;
        }

        renderRadares();
      }
    });

    // Event listeners para los modales
    document.getElementById('closeMapModal').addEventListener('click', closeMapModal);
    document.getElementById('cancelLocationBtn').addEventListener('click', closeMapModal);
    document.getElementById('saveLocationBtn').addEventListener('click', saveRadarLocation);

    document.getElementById('closeEditAllModal').addEventListener('click', closeEditAllModal);
    document.getElementById('cancelEditAllBtn').addEventListener('click', closeEditAllModal);
    document.getElementById('saveAllBtn').addEventListener('click', saveAllRadarData);

    document.getElementById('closeHistoryModal').addEventListener('click', () => {
      document.getElementById('historyModal').style.display = 'none';
    });

    document.getElementById('closeHistoryBtn').addEventListener('click', () => {
      document.getElementById('historyModal').style.display = 'none';
    });

    document.getElementById('mapModal').addEventListener('click', (e) => {
      if (e.target === document.getElementById('mapModal')) {
        closeMapModal();
      }
    });

    document.getElementById('editAllModal').addEventListener('click', (e) => {
      if (e.target === document.getElementById('editAllModal')) {
        closeEditAllModal();
      }
    });

    document.getElementById('historyModal').addEventListener('click', (e) => {
      if (e.target === document.getElementById('historyModal')) {
        document.getElementById('historyModal').style.display = 'none';
      }
    });

    function formatDate(timestamp) {
      const date = new Date(timestamp);
      return date.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }

    const showNotification = (message, type = 'success') => {
      const notification = document.getElementById('notification');
      notification.textContent = message;
      notification.className = type;
      notification.style.display = 'block';

      setTimeout(() => {
        notification.style.display = 'none';
      }, 3000);
    };

    const showLoading = (show) => {
      document.getElementById('loadingIndicator').style.display = show ? 'block' : 'none';
    };

    const logout = () => {
      if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        signOut(auth)
          .then(() => (window.location.href = 'login'))
          .catch((error) => showNotification(`Error al cerrar sesión: ${error.message}`, 'error'));
      }
    };

    const goToMainSite = () => {
      window.open('https://ahorraunamulta.com', '_blank');
    };

    window.logout = logout;
    window.goToMainSite = goToMainSite;
  </script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      color: #333;
    }

    h1 {
      color: #2c3e50;
      border-bottom: 2px solid #3498db;
      padding-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    h2 {
      color: #34495e;
    }

    .dashboard-stats {
      display: flex;
      justify-content: space-between;
      margin: 20px 0;
      flex-wrap: wrap;
      gap: 10px;
    }

    .stat-card {
      background: white;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      flex: 1;
      min-width: 150px;
      text-align: center;
    }

    .stat-card h3 {
      margin-top: 0;
      color: #7f8c8d;
      font-size: 14px;
    }

    .stat-card p {
      font-size: 24px;
      font-weight: bold;
      margin: 10px 0 0;
    }

    .controls {
      margin: 20px 0;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
    }

    .controls input, .controls select {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }

    #searchInput {
      flex: 2;
      min-width: 200px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
    }

    th {
      background-color: #3498db;
      color: white;
      position: sticky;
      top: 0;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    tr:hover {
      background-color: #e9e9e9;
    }

    .status-active {
      background-color: #e8f5e9 !important;
    }

    .status-inactive {
      background-color: #ffebee !important;
    }

    .status-hidden {
      background-color: #f5f5f5 !important;
      color: #999;
    }

    .status-pending_review {
      background-color: #fff8e1 !important;
    }

    button {
      padding: 6px 12px;
      margin: 0 2px;
      border: none;
      color: white;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      transition: all 0.3s;
    }

    button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .approve-btn {
      background-color: #27ae60;
    }

    .approve-btn:hover {
      background-color: #2ecc71;
    }

    .reject-btn {
      background-color: #e74c3c;
    }

    .reject-btn:hover {
      background-color: #c0392b;
    }

    .complete-btn {
      background-color: #f39c12;
    }

    .complete-btn:hover {
      background-color: #e67e22;
    }

    .delete-btn {
      background-color: #e74c3c;
    }

    .delete-btn:hover {
      background-color: #c0392b;
    }

    .view-map-btn {
      background-color: #3498db;
    }

    .view-map-btn:hover {
      background-color: #2980b9;
    }

    .edit-location-btn {
      background-color: #9b59b6;
    }

    .edit-location-btn:hover {
      background-color: #8e44ad;
    }

    .edit-all-btn {
      background-color: #2ecc71;
    }

    .edit-all-btn:hover {
      background-color: #27ae60;
    }

    .history-btn {
      background-color: #3498db;
    }

    .history-btn:hover {
      background-color: #2980b9;
    }

    #notification {
      display: none;
      margin: 20px 0;
      padding: 15px;
      border-radius: 4px;
      color: white;
    }

    #notification.success {
      background-color: #27ae60;
    }

    #notification.error {
      background-color: #e74c3c;
    }

    #notification.warning {
      background-color: #f39c12;
    }

    .logout-btn {
      display: block;
      margin: 30px auto;
      padding: 10px 20px;
      font-size: 16px;
      color: white;
      background-color: #3498db;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-align: center;
      transition: background-color 0.3s;
    }

    .logout-btn:hover {
      background-color: #2980b9;
    }

    .main-site-btn {
      background-color: #f39c12;
      padding: 8px 15px;
      font-size: 14px;
    }

    .main-site-btn:hover {
      background-color: #e67e22;
    }

    .confirm-btn {
      background-color: #3498db;
    }

    .confirm-btn:hover {
      background-color: #2980b9;
    }

    .toggle-id-btn {
      background-color: #9b59b6;
    }

    .toggle-id-btn:hover {
      background-color: #8e44ad;
    }

    select, input {
      padding: 8px;
      border-radius: 4px;
      border: 1px solid #ddd;
      font-size: 14px;
    }

    input {
      width: 80%;
    }

    .actions {
      display: flex;
      gap: 5px;
      flex-wrap: wrap;
      justify-content: center;
    }

    #loadingIndicator {
      display: none;
      text-align: center;
      margin: 20px 0;
      font-size: 18px;
      color: #3498db;
    }

    .pagination-controls {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin: 20px 0;
    }

    .page-btn {
      background-color: #3498db;
      padding: 8px 12px;
    }

    .page-btn:hover {
      background-color: #2980b9;
    }

    /* Estilos para los modales */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      position: relative;
      width: 90%;
      max-width: 800px;
      height: 80vh;
    }

    .edit-all-modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      position: relative;
      width: 90%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
    }

    .history-modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      position: relative;
      width: 90%;
      max-width: 900px;
      max-height: 80vh;
      overflow-y: auto;
    }

    .close-modal {
      position: absolute;
      top: 10px;
      right: 10px;
      background: none;
      border: none;
      font-size: 24px;
      cursor: pointer;
    }

    .cancel-btn {
      background-color: #e74c3c;
    }

    .cancel-btn:hover {
      background-color: #c0392b;
    }

    /* Estilos para los marcadores del mapa */
    .leaflet-marker-icon {
      transition: transform 0.2s ease;
    }

    .leaflet-marker-icon:hover {
      transform: scale(1.2);
      z-index: 1000 !important;
    }

    /* Asegurar que el mapa tenga un tamaño adecuado */
    #adminMap {
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Estilos para el formulario de edición completa */
    .edit-form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .form-buttons {
      grid-column: span 2;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }

    /* Estilos para la tabla de historial */
    .history-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .history-table th, .history-table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    .history-table th {
      background-color: #3498db;
      color: white;
    }

    .history-table tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    /* Estilos para nuevo radar */
    #newRadarModal .edit-all-modal-content {
      border: 2px solid #27ae60;
    }

    #newRadarLat, #newRadarLng {
      background-color: #f8f9fa;
      cursor: not-allowed;
    }

    .leaflet-marker-draggable {
      cursor: move !important;
    }

    @media (max-width: 768px) {
      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }

      .controls {
        flex-direction: column;
        align-items: stretch;
      }

      .stat-card {
        min-width: 100%;
      }

      .actions {
        flex-direction: column;
      }

      .actions button {
        width: 100%;
        margin: 2px 0;
      }

      .edit-form {
        grid-template-columns: 1fr;
      }

      .form-buttons {
        grid-column: span 1;
      }

      .history-table {
        display: block;
        overflow-x: auto;
      }
    }

    .radar-id {
      font-family: monospace;
      background: #f8f9fa;
      padding: 2px 5px;
      border-radius: 3px;
      margin-left: 5px;
    }

    .status-select {
      min-width: 120px;
    }

    /* Campo de conversión de ubicación */
    #locationConverterInput {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      margin-bottom: 5px;
    }

    #osmLinkContainer a {
      color: #3498db;
      text-decoration: none;
    }

    #osmLinkContainer a:hover {
      text-decoration: underline;
    }

    /* Estilos para el aviso beta */
    #betaWarningModal .edit-all-modal-content {
      text-align: center;
      max-width: 500px;
    }

    #betaWarningModal h2 {
      color: #f39c12;
      margin-bottom: 20px;
    }

    #betaWarningModal p {
      margin-bottom: 15px;
      line-height: 1.5;
    }
  </style>
</head>
<body>
  <h1>
    PANEL DE ADMINISTRACIÓN DE RADARES
    <button class="main-site-btn" onclick="goToMainSite()">Ir a ahorraunamulta.com</button>
  </h1>
  <h2 id="userInfo">Cargando...</h2>
  <span id="userEmail" style="display:none;"></span>

  <div id="loadingIndicator">Cargando datos, por favor espere...</div>
  <div id="notification"></div>

  <div class="dashboard-stats">
    <div class="stat-card">
      <h3>Total de Radares</h3>
      <p id="totalRadars">0</p>
    </div>
    <div class="stat-card">
      <h3>Activos</h3>
      <p id="activeRadars">0</p>
    </div>
    <div class="stat-card">
      <h3>Inactivos</h3>
      <p id="inactiveRadars">0</p>
    </div>
    <div class="stat-card">
      <h3>Ocultos</h3>
      <p id="hiddenRadars">0</p>
    </div>
    <div class="stat-card">
      <h3>Pendientes</h3>
      <p id="pendingRadars">0</p>
    </div>
  </div>

  <div class="controls">
    <input type="text" id="searchInput" placeholder="Buscar por ID, dirección, vía o PK...">
    <select id="statusFilter">
      <option value="all">Todos los estados</option>
      <option value="active">Activos</option>
      <option value="inactive">Inactivos</option>
      <option value="hidden">Ocultos</option>
      <option value="pending_review">Pendientes</option>
    </select>
    <select id="typeFilter">
      <option value="all">Todos los tipos</option>
      <option value="Fijo">Fijo</option>
      <option value="Móvil">Móvil</option>
      <option value="Tramo">Tramo</option>
      <option value="Remolque">Remolque</option>
    </select>
    <select id="sortSelect">
      <option value="last_updated">Ordenar por fecha</option>
      <option value="direction">Ordenar por dirección</option>
      <option value="road">Ordenar por vía</option>
      <option value="speed">Ordenar por velocidad</option>
    </select>
    <select id="sortOrder">
      <option value="desc">Descendente</option>
      <option value="asc">Ascendente</option>
    </select>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>DIRECCIÓN</th>
        <th>VELOCIDAD (km/h)</th>
        <th>ESTADO</th>
        <th>VÍA</th>
        <th>PK</th>
        <th>ÚLTIMA MODIFICACIÓN</th>
        <th>TIPO RADAR</th>
        <th>ACCIONES</th>
      </tr>
    </thead>
    <tbody id="radares-table-body"></tbody>
  </table>

  <div id="paginationControls" class="pagination-controls"></div>

  <!-- Modal para editar ubicación -->
  <div id="mapModal" class="modal">
    <div class="modal-content">
      <button class="close-modal" id="closeMapModal">&times;</button>
      <h2>Editar ubicación del radar</h2>
      <div id="adminMap" style="width: 100%; height: calc(100% - 60px);"></div>
      <div style="margin-top: 10px; text-align: center;">
        <button id="saveLocationBtn" class="confirm-btn">Guardar ubicación</button>
        <button id="cancelLocationBtn" class="cancel-btn">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- Modal para edición completa -->
  <div id="editAllModal" class="modal">
    <div class="edit-all-modal-content">
      <button class="close-modal" id="closeEditAllModal">&times;</button>
      <h2>Editar todos los datos del radar</h2>
      <div class="edit-form">
        <div class="form-group">
          <label for="editDirection">Dirección:</label>
          <input type="text" id="editDirection">
        </div>
        <div class="form-group">
          <label for="editSpeed">Velocidad (km/h):</label>
          <input type="number" id="editSpeed" min="0" step="1">
        </div>
        <div class="form-group">
          <label for="editStatus">Estado:</label>
          <select id="editStatus">
            <option value="active">Activo</option>
            <option value="inactive">Inactivo</option>
            <option value="hidden">Oculto</option>
            <option value="pending_review">Pendiente</option>
          </select>
        </div>
        <div class="form-group">
          <label for="editRoad">Vía:</label>
          <input type="text" id="editRoad">
        </div>
        <div class="form-group">
          <label for="editPk">PK:</label>
          <input type="text" id="editPk">
        </div>
        <div class="form-group">
          <label for="editRadarType">Tipo de radar:</label>
          <select id="editRadarType">
            <option value="Fijo">Fijo</option>
            <option value="Móvil">Móvil</option>
            <option value="Tramo">Tramo</option>
            <option value="Remolque">Remolque</option>
          </select>
        </div>
        <div class="form-buttons">
          <button id="cancelEditAllBtn" class="cancel-btn">Cancelar</button>
          <button id="saveAllBtn" class="confirm-btn">Guardar todos los cambios</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para historial de cambios -->
  <div id="historyModal" class="modal">
    <div class="history-modal-content">
      <button class="close-modal" id="closeHistoryModal">&times;</button>
      <h2>Historial de Cambios</h2>
      <div id="historyModalContent"></div>
      <div style="margin-top: 15px; text-align: center;">
        <button id="closeHistoryBtn" class="cancel-btn">Cerrar</button>
      </div>
    </div>
  </div>

  <button class="logout-btn" onclick="logout()">CERRAR SESIÓN</button>
</body>
</html>
