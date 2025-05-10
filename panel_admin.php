<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="https://ahorraunamulta.com/favicon.png" />
  <title>PANEL DE ADMINISTRACIÓN - AHORRA UNA MULTA</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
    import { getAuth, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-auth.js";
    import { getDatabase, ref, onValue, update } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";

    const firebaseConfigLogin = {
      apiKey: "AIzaSyAX4uy3ON91cwK3Tt9r5Eqpucyf4sfv0No",
      authDomain: "login-radares.firebaseapp.com",
      projectId: "login-radares",
      storageBucket: "login-radares.appspot.com",
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

    // Variables globales
    let currentUser = null;
    let allRadares = [];
    let currentPage = 1;
    const itemsPerPage = 10;

    onAuthStateChanged(auth, (user) => {
      if (!user) {
        window.location.href = 'login.html';
      } else {
        currentUser = user;
        document.getElementById('userInfo').innerHTML = `
          <i class="fas fa-user-circle"></i> Bienvenido, ${user.email}
        `;
        fetchRadares();
        initDashboard();
      }
    });

    const fetchRadares = () => {
      const radaresRef = ref(database, 'radares');
      onValue(radaresRef, (snapshot) => {
        const data = snapshot.val();
        const radaresTableBody = document.getElementById('radares-table-body');
        radaresTableBody.innerHTML = '';

        if (data) {
          allRadares = Object.keys(data).map((key) => {
            return { id: key, ...data[key] };
          });

          // Procesar fechas
          allRadares.forEach((radar) => {
            if (radar.last_updated) {
              radar.last_updated = new Date(radar.last_updated).getTime();
            }
          });

          // Ordenar por fecha
          allRadares.sort((a, b) => b.last_updated - a.last_updated);

          renderTable();
          updatePagination();
          updateStats();
        }
      });
    };

    const renderTable = (radares = allRadares) => {
      const radaresTableBody = document.getElementById('radares-table-body');
      radaresTableBody.innerHTML = '';

      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      const paginatedRadares = radares.slice(startIndex, endIndex);

      paginatedRadares.forEach((radar) => {
        const row = document.createElement('tr');

        const createEditableCell = (value, fieldName) => {
          return `
            <td>
              <input type="text" value="${value || ''}" data-field="${fieldName}" data-id="${radar.id}">
              <button class="btn btn-sm btn-confirm" data-field="${fieldName}" data-id="${radar.id}">
                <i class="fas fa-check"></i>
              </button>
            </td>
          `;
        };

        const lastUpdated = radar.last_updated ? formatDate(radar.last_updated) : 'N/A';
        const roadValue = radar.road || 'Sin especificar';

        row.innerHTML = `
          <td>
            <button class="btn btn-sm btn-toggle" data-id="${radar.id}">
              <i class="fas fa-eye"></i>
            </button>
            <span class="radar-id" style="display: none;">${radar.id}</span>
          </td>
          ${createEditableCell(radar.direction, 'direction')}
          ${createEditableCell(radar.speed, 'speed')}
          <td>
            <select class="form-select" data-field="status" data-id="${radar.id}">
              <option value="active" ${radar.status === 'active' ? 'selected' : ''}>Activo</option>
              <option value="inactive" ${radar.status === 'inactive' ? 'selected' : ''}>Inactivo</option>
              <option value="pending_review" ${radar.status === 'pending_review' ? 'selected' : ''}>Pendiente</option>
            </select>
            <button class="btn btn-sm btn-confirm" data-field="status" data-id="${radar.id}">
              <i class="fas fa-check"></i>
            </button>
          </td>
          ${createEditableCell(roadValue, 'road')}
          ${createEditableCell(radar.pk, 'pk')}
          <td>${lastUpdated}</td>
          <td>
            <select class="form-select" data-field="radarType" data-id="${radar.id}">
              <option value="Fijo" ${radar.radarType === 'Fijo' ? 'selected' : ''}>Fijo</option>
              <option value="Móvil" ${radar.radarType === 'Móvil' ? 'selected' : ''}>Móvil</option>
              <option value="Tramo" ${radar.radarType === 'Tramo' ? 'selected' : ''}>Tramo</option>
              <option value="Remolque" ${radar.radarType === 'Remolque' ? 'selected' : ''}>Remolque</option>
            </select>
            <button class="btn btn-sm btn-confirm" data-field="radarType" data-id="${radar.id}">
              <i class="fas fa-check"></i>
            </button>
          </td>
          <td>
            <div class="action-buttons">
              ${radar.status !== 'active' ? `
                <button class="btn btn-sm btn-success btn-approve" data-id="${radar.id}" title="Activar">
                  <i class="fas fa-check-circle"></i>
                </button>
              ` : ''}
              ${radar.status !== 'inactive' ? `
                <button class="btn btn-sm btn-danger btn-reject" data-id="${radar.id}" title="Desactivar">
                  <i class="fas fa-times-circle"></i>
                </button>
              ` : ''}
              ${radar.status === 'pending_review' ? `
                <button class="btn btn-sm btn-warning btn-complete" data-id="${radar.id}" title="Completar">
                  <i class="fas fa-clipboard-check"></i>
                </button>
              ` : ''}
              <button class="btn btn-sm btn-info btn-history" data-id="${radar.id}" title="Historial">
                <i class="fas fa-history"></i>
              </button>
            </div>
          </td>
        `;

        radaresTableBody.appendChild(row);
      });

      // Event listeners
      setupEventListeners();
    };

    const setupEventListeners = () => {
      // Toggle ID visibility
      document.querySelectorAll('.btn-toggle').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          const idSpan = e.target.closest('button').nextElementSibling;
          if (idSpan.style.display === 'none') {
            idSpan.style.display = 'inline';
            e.target.innerHTML = '<i class="fas fa-eye-slash"></i>';
          } else {
            idSpan.style.display = 'none';
            e.target.innerHTML = '<i class="fas fa-eye"></i>';
          }
        });
      });

      // Confirm button clicks
      document.querySelectorAll('.btn-confirm').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const field = e.target.closest('button').dataset.field;
          const id = e.target.closest('button').dataset.id;
          let newValue;

          if (field === 'status' || field === 'radarType') {
            newValue = e.target.closest('td').querySelector('select').value;
          } else {
            newValue = e.target.closest('td').querySelector('input').value;
          }

          if (field === 'speed' && isNaN(newValue)) {
            showNotification('La velocidad debe ser un número', 'error');
            return;
          }

          const currentDate = Date.now();
          const radarRef = ref(database, `radares/${id}`);
          const updateData = {
            [field]: newValue,
            last_updated: currentDate,
            last_updated_by: currentUser.email
          };

          update(radarRef, updateData)
            .then(() => showNotification(`Campo ${field} actualizado a "${newValue}".`, 'success'))
            .catch((error) => showNotification(`Error: ${error.message}`, 'error'));
        });
      });

      // Approve button
      document.querySelectorAll('.btn-approve').forEach((btn) => {
        btn.addEventListener('click', () => {
          if (!confirm('¿Está seguro de que desea activar este radar?')) return;
          const id = btn.dataset.id;
          const radarRef = ref(database, `radares/${id}`);
          update(radarRef, {
            status: 'active',
            last_updated: Date.now(),
            last_updated_by: currentUser.email
          })
            .then(() => showNotification(`Radar con ID ${id} activado.`, 'success'))
            .catch((error) => showNotification(`Error: ${error.message}`, 'error'));
        });
      });

      // Reject button
      document.querySelectorAll('.btn-reject').forEach((btn) => {
        btn.addEventListener('click', () => {
          if (!confirm('¿Está seguro de que desea desactivar este radar?')) return;
          const id = btn.dataset.id;
          const radarRef = ref(database, `radares/${id}`);
          update(radarRef, {
            status: 'inactive',
            last_updated: Date.now(),
            last_updated_by: currentUser.email
          })
            .then(() => showNotification(`Radar con ID ${id} desactivado.`, 'success'))
            .catch((error) => showNotification(`Error: ${error.message}`, 'error'));
        });
      });

      // Complete button
      document.querySelectorAll('.btn-complete').forEach((btn) => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          const radarRef = ref(database, `radares/${id}`);
          update(radarRef, {
            status: 'active',
            last_updated: Date.now(),
            last_updated_by: currentUser.email
          })
            .then(() => showNotification(`Radar con ID ${id} completado y activado.`, 'success'))
            .catch((error) => showNotification(`Error: ${error.message}`, 'error'));
        });
      });

      // History button
      document.querySelectorAll('.btn-history').forEach((btn) => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          showNotification(`Historial del radar ${id} (función en desarrollo)`, 'info');
        });
      });
    };

    // Dashboard functions
    const initDashboard = () => {
      // Search functionality
      document.getElementById('searchInput').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredRadares = allRadares.filter(radar => {
          return (
            radar.id.toLowerCase().includes(searchTerm) ||
            (radar.direction && radar.direction.toLowerCase().includes(searchTerm)) ||
            (radar.road && radar.road.toLowerCase().includes(searchTerm)) ||
            (radar.pk && radar.pk.toString().includes(searchTerm))
        });
        renderTable(filteredRadares);
      });

      // Status filter
      document.getElementById('statusFilter').addEventListener('change', (e) => {
        const status = e.target.value;
        if (status === 'all') {
          renderTable(allRadares);
        } else {
          const filteredRadares = allRadares.filter(radar => radar.status === status);
          renderTable(filteredRadares);
        }
      });

      // Type filter
      document.getElementById('typeFilter').addEventListener('change', (e) => {
        const type = e.target.value;
        if (type === 'all') {
          renderTable(allRadares);
        } else {
          const filteredRadares = allRadares.filter(radar => radar.radarType === type);
          renderTable(filteredRadares);
        }
      });
    };

    // Pagination functions
    const updatePagination = () => {
      const totalPages = Math.ceil(allRadares.length / itemsPerPage);
      const pagination = document.getElementById('pagination');
      pagination.innerHTML = '';

      if (totalPages <= 1) return;

      // Previous button
      pagination.innerHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
          <a class="page-link" href="#" data-page="${currentPage - 1}">
            <i class="fas fa-chevron-left"></i>
          </a>
        </li>
      `;

      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `
          <li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
          </li>
        `;
      }

      // Next button
      pagination.innerHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
          <a class="page-link" href="#" data-page="${currentPage + 1}">
            <i class="fas fa-chevron-right"></i>
          </a>
        </li>
      `;

      // Event listeners for pagination
      document.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          currentPage = parseInt(e.target.dataset.page);
          renderTable();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
      });
    };

    // Stats functions
    const updateStats = () => {
      const activeCount = allRadares.filter(r => r.status === 'active').length;
      const inactiveCount = allRadares.filter(r => r.status === 'inactive').length;
      const pendingCount = allRadares.filter(r => r.status === 'pending_review').length;

      document.getElementById('activeCount').textContent = activeCount;
      document.getElementById('inactiveCount').textContent = inactiveCount;
      document.getElementById('pendingCount').textContent = pendingCount;
      document.getElementById('totalCount').textContent = allRadares.length;
    };

    // Utility functions
    function formatDate(timestamp) {
      const date = new Date(timestamp);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${day}-${month}-${year} ${hours}:${minutes}`;
    }

    const showNotification = (message, type = 'success') => {
      const notification = document.getElementById('notification');
      notification.innerHTML = `
        <div class="notification ${type} show">
          <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
          ${message}
        </div>
      `;

      setTimeout(() => {
        notification.innerHTML = '';
      }, 3000);
    };

    const logout = () => {
      signOut(auth)
        .then(() => (window.location.href = 'login.html'))
        .catch((error) => {
          console.error("Error al cerrar sesión:", error);
          showNotification('Error al cerrar sesión', 'error');
        });
    };

    window.logout = logout;
    window.exportToCSV = () => {
      showNotification('Exportando datos a CSV (función en desarrollo)', 'info');
    };
  </script>
  <style>
    :root {
      --primary-color: #3498db;
      --primary-hover: #2980b9;
      --success-color: #2ecc71;
      --success-hover: #27ae60;
      --danger-color: #e74c3c;
      --danger-hover: #c0392b;
      --warning-color: #f39c12;
      --warning-hover: #d35400;
      --info-color: #1abc9c;
      --info-hover: #16a085;
      --light-color: #ecf0f1;
      --dark-color: #34495e;
      --gray-color: #95a5a6;
      --border-color: #ddd;
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      color: #333;
      background-color: #f5f7fa;
      padding: 0;
      margin: 0;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid var(--border-color);
      flex-wrap: wrap;
    }

    .header h1 {
      color: var(--dark-color);
      margin-bottom: 10px;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
      background-color: white;
      padding: 10px 15px;
      border-radius: 5px;
      box-shadow: var(--shadow);
    }

    .user-info i {
      color: var(--primary-color);
      font-size: 1.2rem;
    }

    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .card {
      background-color: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: var(--shadow);
      text-align: center;
    }

    .card h3 {
      color: var(--gray-color);
      font-size: 1rem;
      margin-bottom: 10px;
    }

    .card p {
      font-size: 2rem;
      font-weight: bold;
      color: var(--dark-color);
    }

    .card.active {
      border-top: 4px solid var(--success-color);
    }

    .card.inactive {
      border-top: 4px solid var(--danger-color);
    }

    .card.pending {
      border-top: 4px solid var(--warning-color);
    }

    .card.total {
      border-top: 4px solid var(--primary-color);
    }

    .filters {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .filter-group {
      flex: 1;
      min-width: 200px;
    }

    .filter-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
      color: var(--dark-color);
    }

    .form-control {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid var(--border-color);
      border-radius: 5px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary-color);
    }

    .form-select {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid var(--border-color);
      border-radius: 5px;
      font-size: 1rem;
      background-color: white;
      cursor: pointer;
    }

    .table-container {
      background: white;
      border-radius: 8px;
      box-shadow: var(--shadow);
      overflow: hidden;
      margin-bottom: 20px;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 1000px;
    }

    th {
      background-color: var(--dark-color);
      color: white;
      padding: 12px 15px;
      text-align: left;
      position: sticky;
      top: 0;
    }

    td {
      padding: 12px 15px;
      border-bottom: 1px solid var(--border-color);
      vertical-align: middle;
    }

    tr:hover {
      background-color: rgba(0, 0, 0, 0.02);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 12px;
      border-radius: 5px;
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      gap: 5px;
    }

    .btn-sm {
      padding: 5px 8px;
      font-size: 0.75rem;
    }

    .btn-primary {
      background-color: var(--primary-color);
      color: white;
    }

    .btn-primary:hover {
      background-color: var(--primary-hover);
    }

    .btn-success {
      background-color: var(--success-color);
      color: white;
    }

    .btn-success:hover {
      background-color: var(--success-hover);
    }

    .btn-danger {
      background-color: var(--danger-color);
      color: white;
    }

    .btn-danger:hover {
      background-color: var(--danger-hover);
    }

    .btn-warning {
      background-color: var(--warning-color);
      color: white;
    }

    .btn-warning:hover {
      background-color: var(--warning-hover);
    }

    .btn-info {
      background-color: var(--info-color);
      color: white;
    }

    .btn-info:hover {
      background-color: var(--info-hover);
    }

    .btn-light {
      background-color: var(--light-color);
      color: var(--dark-color);
    }

    .btn-light:hover {
      background-color: #d5dbdb;
    }

    .action-buttons {
      display: flex;
      gap: 5px;
      flex-wrap: wrap;
    }

    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
    }

    .notification > div {
      padding: 15px 25px;
      border-radius: 5px;
      color: white;
      box-shadow: var(--shadow);
      display: flex;
      align-items: center;
      gap: 10px;
      transform: translateX(120%);
      transition: transform 0.3s ease;
    }

    .notification .show {
      transform: translateX(0);
    }

    .notification .success {
      background-color: var(--success-color);
    }

    .notification .error {
      background-color: var(--danger-color);
    }

    .notification .info {
      background-color: var(--info-color);
    }

    .pagination-container {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .pagination {
      display: flex;
      list-style: none;
      gap: 5px;
    }

    .page-item {
      margin: 0;
    }

    .page-item.disabled .page-link {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .page-item.active .page-link {
      background-color: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
    }

    .page-link {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 40px;
      min-width: 40px;
      padding: 0 10px;
      border: 1px solid var(--border-color);
      border-radius: 5px;
      color: var(--dark-color);
      text-decoration: none;
      transition: all 0.3s;
    }

    .page-link:hover {
      background-color: var(--light-color);
    }

    .footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid var(--border-color);
    }

    .logout-btn {
      background-color: var(--danger-color);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .logout-btn:hover {
      background-color: var(--danger-hover);
    }

    .export-btn {
      background-color: var(--success-color);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .export-btn:hover {
      background-color: var(--success-hover);
    }

    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }

      .user-info {
        width: 100%;
        justify-content: space-between;
      }

      .dashboard-cards {
        grid-template-columns: 1fr 1fr;
      }

      .filters {
        flex-direction: column;
        gap: 10px;
      }

      .filter-group {
        min-width: 100%;
      }
    }

    @media (max-width: 480px) {
      .dashboard-cards {
        grid-template-columns: 1fr;
      }

      .action-buttons {
        flex-direction: column;
      }

      .btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><i class="fas fa-tachometer-alt"></i> PANEL DE ADMINISTRACIÓN</h1>
      <div class="user-info" id="userInfo">Cargando...</div>
    </div>

    <div id="notification"></div>

    <div class="dashboard-cards">
      <div class="card active">
        <h3><i class="fas fa-check-circle"></i> Activos</h3>
        <p id="activeCount">0</p>
      </div>
      <div class="card inactive">
        <h3><i class="fas fa-times-circle"></i> Inactivos</h3>
        <p id="inactiveCount">0</p>
      </div>
      <div class="card pending">
        <h3><i class="fas fa-clock"></i> Pendientes</h3>
        <p id="pendingCount">0</p>
      </div>
      <div class="card total">
        <h3><i class="fas fa-radar"></i> Total</h3>
        <p id="totalCount">0</p>
      </div>
    </div>

    <div class="filters">
      <div class="filter-group">
        <label for="searchInput"><i class="fas fa-search"></i> Buscar</label>
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar radares...">
      </div>
      <div class="filter-group">
        <label for="statusFilter"><i class="fas fa-filter"></i> Estado</label>
        <select id="statusFilter" class="form-select">
          <option value="all">Todos</option>
          <option value="active">Activos</option>
          <option value="inactive">Inactivos</option>
          <option value="pending_review">Pendientes</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="typeFilter"><i class="fas fa-radar"></i> Tipo</label>
        <select id="typeFilter" class="form-select">
          <option value="all">Todos</option>
          <option value="Fijo">Fijo</option>
          <option value="Móvil">Móvil</option>
          <option value="Tramo">Tramo</option>
          <option value="Remolque">Remolque</option>
        </select>
      </div>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>DIRECCIÓN</th>
            <th>VELOCIDAD MÁXIMA</th>
            <th>ESTADO</th>
            <th>VIA</th>
            <th>PK</th>
            <th>ÚLTIMA MODIFICACIÓN</th>
            <th>TIPO RADAR</th>
            <th>ACCIONES</th>
          </tr>
        </thead>
        <tbody id="radares-table-body"></tbody>
      </table>
    </div>

    <div class="pagination-container">
      <ul class="pagination" id="pagination"></ul>
    </div>

    <div class="footer">
      <button class="export-btn" onclick="exportToCSV()">
        <i class="fas fa-file-export"></i> Exportar a CSV
      </button>
      <button class="logout-btn" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
      </button>
    </div>
  </div>
</body>
</html>
