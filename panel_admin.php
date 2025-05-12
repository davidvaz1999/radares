<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="https://ahorraunamulta.com/favicon.png" />
  <title>PANEL DE ADMINISTRACIÓN - AHORRA UNA MULTA</title>
  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
    import { getAuth, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-auth.js";
    import { getDatabase, ref, onValue, update, remove } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";

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

    onAuthStateChanged(auth, (user) => {
      if (!user) {
        window.location.href = 'login.html';
      } else {
        document.getElementById('userInfo').innerText = `Bienvenido, ${user.email}`;
        document.getElementById('userEmail').textContent = user.email;
        fetchRadares();
      }
    });

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
        } else {
          allRadares = [];
          document.getElementById('radares-table-body').innerHTML = '<tr><td colspan="9">No se encontraron radares</td></tr>';
        }
      }, (error) => {
        showLoading(false);
        showNotification(`Error al cargar radares: ${error.message}`, 'error');
      });
    };

    const renderRadares = (radares = allRadares) => {
      const filteredRadares = filterRadares(radares);
      const sortedRadares = sortRadares(filteredRadares);
      const paginatedRadares = paginateRadares(sortedRadares);

      const radaresTableBody = document.getElementById('radares-table-body');
      radaresTableBody.innerHTML = '';

      if (paginatedRadares.length === 0) {
        radaresTableBody.innerHTML = '<tr><td colspan="9">No se encontraron radares con los filtros aplicados</td></tr>';
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
            <button class="delete-btn" data-id="${radar.id}">🗑️ Eliminar</button>
          </td>
        `;

        radaresTableBody.appendChild(row);
      });

      setupEventListeners();
      updatePaginationControls(sortedRadares.length);
    };

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
        pending: allRadares.filter(r => r.status === 'pending_review').length
      };

      document.getElementById('totalRadars').textContent = stats.total;
      document.getElementById('activeRadars').textContent = stats.active;
      document.getElementById('inactiveRadars').textContent = stats.inactive;
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

          let inputElement = e.target.parentElement.querySelector('input, select');

          if (!inputElement) {
            showNotification('No se pudo encontrar el elemento de entrada', 'error');
            return;
          }

          const newValue = inputElement.value;

          if (field === 'speed' && (isNaN(newValue) || newValue < 0)) {
            showNotification('La velocidad debe ser un número positivo', 'error');
            return;
          }

          if (confirm(`¿Confirmas que deseas actualizar el campo ${field} a "${newValue}"?`)) {
            const radarRef = ref(database, `radares/${id}`);
            const updateData = {
              [field]: field === 'speed' ? parseInt(newValue) : newValue,
              last_updated: Date.now(),
              updated_by: document.getElementById('userEmail').textContent
            };

            update(radarRef, updateData)
              .then(() => showNotification(`Campo ${field} actualizado correctamente.`, 'success'))
              .catch((error) => showNotification(`Error: ${error.message}`, 'error'));
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

      document.querySelectorAll('.delete-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const id = btn.dataset.id;
          if (confirm('¿Estás seguro de que deseas eliminar permanentemente este radar? Esta acción no se puede deshacer.')) {
            deleteRadar(id);
          }
        });
      });
    };

    const updateRadarStatus = (id, status) => {
      const radarRef = ref(database, `radares/${id}`);
      update(radarRef, {
        status: status,
        last_updated: Date.now(),
        updated_by: document.getElementById('userEmail').textContent
      })
        .then(() => showNotification(`Radar ${id} actualizado a ${status}.`, 'success'))
        .catch((error) => showNotification(`Error: ${error.message}`, 'error'));
    };

    const deleteRadar = (id) => {
      showLoading(true);
      const radarRef = ref(database, `radares/${id}`);
      remove(radarRef)
        .then(() => {
          showNotification(`Radar ${id} eliminado correctamente.`, 'success');
          fetchRadares(); // Recargar la lista de radares
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

    window.logout = logout;
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
  </style>
</head>
<body>
  <h1>PANEL DE ADMINISTRACIÓN DE RADARES</h1>
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

  <button class="logout-btn" onclick="logout()">CERRAR SESIÓN</button>
</body>
</html>
