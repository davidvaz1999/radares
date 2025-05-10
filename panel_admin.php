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
    import { getDatabase, ref, onValue, update } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";

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

    onAuthStateChanged(auth, (user) => {
      if (!user) {
        window.location.href = 'login.html';
      } else {
        document.getElementById('userInfo').innerText = `Bienvenido, ${user.email}`;
        fetchRadares();
      }
    });

    const fetchRadares = () => {
      const radaresRef = ref(database, 'radares');
      onValue(radaresRef, (snapshot) => {
        const data = snapshot.val();
        const radaresTableBody = document.getElementById('radares-table-body');
        const radaresCardsContainer = document.getElementById('radares-cards-container');

        radaresTableBody.innerHTML = '';
        radaresCardsContainer.innerHTML = '';

        if (data) {
          const radaresArray = Object.keys(data).map((key) => {
            return { id: key, ...data[key] };
          });

          radaresArray.forEach((radar) => {
            if (radar.last_updated) {
              radar.last_updated = new Date(radar.last_updated).getTime();
            }
          });

          radaresArray.sort((a, b) => b.last_updated - a.last_updated);

          // Render para desktop (tabla)
          radaresArray.forEach((radar) => {
            const row = document.createElement('tr');

            const createEditableCell = (value, fieldName) => {
              return `
                <td>
                  <input type="text" value="${value || ''}" data-field="${fieldName}" data-id="${radar.id}">
                  <button class="confirm-btn" data-field="${fieldName}" data-id="${radar.id}">✓</button>
                </td>
              `;
            };

            const lastUpdated = radar.last_updated ? formatDate(radar.last_updated) : 'N/A';
            const roadValue = radar.road || 'Sin especificar';

            row.innerHTML = `
              <td>
                <span class="radar-id">${radar.id.substring(0, 6)}...</span>
              </td>
              ${createEditableCell(radar.direction, 'direction')}
              ${createEditableCell(radar.speed, 'speed')}
              <td>
                <select data-field="status" data-id="${radar.id}">
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
              <td class="action-buttons">
                ${radar.status !== 'active' ? `<button class="approve-btn" data-id="${radar.id}">Activar</button>` : ''}
                ${radar.status !== 'inactive' ? `<button class="reject-btn" data-id="${radar.id}">Desactivar</button>` : ''}
                ${radar.status === 'pending_review' ? `<button class="complete-btn" data-id="${radar.id}">Completar</button>` : ''}
              </td>
            `;

            radaresTableBody.appendChild(row);
          });

          // Render para móvil (tarjetas)
          radaresArray.forEach((radar) => {
            const card = document.createElement('div');
            card.className = 'radar-card';

            const lastUpdated = radar.last_updated ? formatDate(radar.last_updated) : 'N/A';
            const roadValue = radar.road || 'Sin especificar';
            const statusClass = radar.status === 'active' ? 'active' : radar.status === 'inactive' ? 'inactive' : 'pending';

            card.innerHTML = `
              <div class="card-header">
                <span class="radar-id">ID: ${radar.id.substring(0, 8)}...</span>
                <span class="status-badge ${statusClass}">${getStatusText(radar.status)}</span>
              </div>
              <div class="card-body">
                <div class="card-row">
                  <label>Dirección:</label>
                  <div class="editable-field">
                    <input type="text" value="${radar.direction || ''}" data-field="direction" data-id="${radar.id}">
                    <button class="confirm-btn" data-field="direction" data-id="${radar.id}">✓</button>
                  </div>
                </div>
                <div class="card-row">
                  <label>Velocidad:</label>
                  <div class="editable-field">
                    <input type="text" value="${radar.speed || ''}" data-field="speed" data-id="${radar.id}">
                    <button class="confirm-btn" data-field="speed" data-id="${radar.id}">✓</button>
                  </div>
                </div>
                <div class="card-row">
                  <label>Vía:</label>
                  <div class="editable-field">
                    <input type="text" value="${roadValue}" data-field="road" data-id="${radar.id}">
                    <button class="confirm-btn" data-field="road" data-id="${radar.id}">✓</button>
                  </div>
                </div>
                <div class="card-row">
                  <label>PK:</label>
                  <div class="editable-field">
                    <input type="text" value="${radar.pk || ''}" data-field="pk" data-id="${radar.id}">
                    <button class="confirm-btn" data-field="pk" data-id="${radar.id}">✓</button>
                  </div>
                </div>
                <div class="card-row">
                  <label>Tipo:</label>
                  <div class="editable-field">
                    <select data-field="radarType" data-id="${radar.id}">
                      <option value="Fijo" ${radar.radarType === 'Fijo' ? 'selected' : ''}>Fijo</option>
                      <option value="Móvil" ${radar.radarType === 'Móvil' ? 'selected' : ''}>Móvil</option>
                      <option value="Tramo" ${radar.radarType === 'Tramo' ? 'selected' : ''}>Tramo</option>
                      <option value="Remolque" ${radar.radarType === 'Remolque' ? 'selected' : ''}>Remolque</option>
                    </select>
                    <button class="confirm-btn" data-field="radarType" data-id="${radar.id}">✓</button>
                  </div>
                </div>
                <div class="card-row">
                  <label>Estado:</label>
                  <div class="editable-field">
                    <select data-field="status" data-id="${radar.id}">
                      <option value="active" ${radar.status === 'active' ? 'selected' : ''}>Activo</option>
                      <option value="inactive" ${radar.status === 'inactive' ? 'selected' : ''}>Inactivo</option>
                      <option value="pending_review" ${radar.status === 'pending_review' ? 'selected' : ''}>Pendiente</option>
                    </select>
                    <button class="confirm-btn" data-field="status" data-id="${radar.id}">✓</button>
                  </div>
                </div>
                <div class="card-row">
                  <label>Últ. modificación:</label>
                  <span>${lastUpdated}</span>
                </div>
              </div>
              <div class="card-actions">
                ${radar.status !== 'active' ? `<button class="approve-btn" data-id="${radar.id}">Activar</button>` : ''}
                ${radar.status !== 'inactive' ? `<button class="reject-btn" data-id="${radar.id}">Desactivar</button>` : ''}
                ${radar.status === 'pending_review' ? `<button class="complete-btn" data-id="${radar.id}">Completar</button>` : ''}
              </div>
            `;

            radaresCardsContainer.appendChild(card);
          });

          setupEventListeners();
        }
      });
    };

    function getStatusText(status) {
      const statusMap = {
        'active': 'Activo',
        'inactive': 'Inactivo',
        'pending_review': 'Pendiente'
      };
      return statusMap[status] || status;
    }

    function setupEventListeners() {
      document.querySelectorAll('.confirm-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const field = e.target.dataset.field;
          const id = e.target.dataset.id;
          const input = e.target.previousElementSibling || e.target.parentElement.querySelector('select');
          const newValue = input.value;
          const currentDate = Date.now();

          const radarRef = ref(database, `radares/${id}`);
          const updateData = {
            [field]: newValue,
            last_updated: currentDate
          };

          update(radarRef, updateData)
            .then(() => showNotification(`Campo ${field} actualizado a "${newValue}".`))
            .catch((error) => showNotification(`Error: ${error.message}`));
        });
      });

      document.querySelectorAll('.approve-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          const radarRef = ref(database, `radares/${id}`);
          update(radarRef, {
            status: 'active',
            last_updated: Date.now()
          })
            .then(() => showNotification(`Radar con ID ${id} activado.`))
            .catch((error) => showNotification(`Error: ${error.message}`));
        });
      });

      document.querySelectorAll('.reject-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          const radarRef = ref(database, `radares/${id}`);
          update(radarRef, {
            status: 'inactive',
            last_updated: Date.now()
          })
            .then(() => showNotification(`Radar con ID ${id} desactivado.`))
            .catch((error) => showNotification(`Error: ${error.message}`));
        });
      });

      document.querySelectorAll('.complete-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          const radarRef = ref(database, `radares/${id}`);
          update(radarRef, {
            status: 'active',
            last_updated: Date.now()
          })
            .then(() => showNotification(`Radar con ID ${id} completado y activado.`))
            .catch((error) => showNotification(`Error: ${error.message}`));
        });
      });
    }

    function formatDate(isoDate) {
      const date = new Date(isoDate);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${day}-${month}-${year} ${hours}:${minutes}`;
    }

    const showNotification = (message) => {
      const notification = document.getElementById('notification');
      notification.textContent = message;
      notification.style.display = 'block';
      setTimeout(() => {
        notification.style.display = 'none';
      }, 3000);
    };

    const logout = () => {
      signOut(auth)
        .then(() => (window.location.href = 'login'))
        .catch((error) => console.error("Error al cerrar sesión:", error));
    };

    window.logout = logout;
  </script>
  <style>
    :root {
      --primary-color: #3498db;
      --primary-dark: #2980b9;
      --success-color: #2ecc71;
      --danger-color: #e74c3c;
      --warning-color: #f39c12;
      --info-color: #1abc9c;
      --light-gray: #ecf0f1;
      --medium-gray: #bdc3c7;
      --dark-gray: #2c3e50;
      --text-color: #333;
      --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      --border-radius: 8px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 20px;
      color: var(--text-color);
      line-height: 1.6;
      background-color: #f5f7fa;
    }

    .header {
      text-align: center;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 1px solid var(--medium-gray);
    }

    h1 {
      color: var(--dark-gray);
      margin-bottom: 5px;
      font-size: 1.8rem;
    }

    h2 {
      color: var(--text-color);
      font-size: 1.2rem;
      font-weight: normal;
    }

    /* Notificación */
    #notification {
      display: none;
      margin: 0 auto 20px;
      padding: 12px 20px;
      background-color: var(--success-color);
      color: white;
      border-radius: var(--border-radius);
      text-align: center;
      max-width: 600px;
      box-shadow: var(--card-shadow);
    }

    /* Contenedor principal */
    .container {
      max-width: 1400px;
      margin: 0 auto;
    }

    /* Vista de tabla (desktop) */
    .desktop-view {
      display: block;
      width: 100%;
      overflow-x: auto;
      margin-bottom: 30px;
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 1000px;
    }

    th, td {
      border: 1px solid var(--light-gray);
      padding: 12px 10px;
      text-align: center;
    }

    th {
      background-color: var(--primary-color);
      color: white;
      font-weight: 600;
      position: sticky;
      top: 0;
    }

    tr:nth-child(even) {
      background-color: rgba(52, 152, 219, 0.05);
    }

    tr:hover {
      background-color: rgba(52, 152, 219, 0.1);
    }

    /* Vista de tarjetas (móvil) */
    .mobile-view {
      display: none;
    }

    .radar-cards-container {
      display: grid;
      grid-template-columns: 1fr;
      gap: 15px;
      margin-bottom: 20px;
    }

    .radar-card {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
      overflow: hidden;
      transition: transform 0.3s ease;
    }

    .radar-card:hover {
      transform: translateY(-3px);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 15px;
      background-color: var(--primary-color);
      color: white;
      font-weight: bold;
    }

    .status-badge {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: bold;
    }

    .status-badge.active {
      background-color: var(--success-color);
    }

    .status-badge.inactive {
      background-color: var(--danger-color);
    }

    .status-badge.pending {
      background-color: var(--warning-color);
    }

    .card-body {
      padding: 15px;
    }

    .card-row {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .card-row:last-child {
      margin-bottom: 0;
    }

    .card-row label {
      flex: 0 0 120px;
      font-weight: 500;
      color: var(--dark-gray);
    }

    .editable-field {
      flex: 1;
      display: flex;
      align-items: center;
    }

    .card-actions {
      display: flex;
      justify-content: space-around;
      padding: 10px 15px;
      border-top: 1px solid var(--light-gray);
      background-color: rgba(52, 152, 219, 0.05);
    }

    /* Botones */
    button {
      padding: 8px 12px;
      margin: 0 5px;
      border: none;
      color: white;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.85rem;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .approve-btn {
      background-color: var(--success-color);
    }

    .approve-btn:hover {
      background-color: #27ae60;
    }

    .reject-btn {
      background-color: var(--danger-color);
    }

    .reject-btn:hover {
      background-color: #c0392b;
    }

    .complete-btn {
      background-color: var(--warning-color);
    }

    .complete-btn:hover {
      background-color: #e67e22;
    }

    .logout-btn {
      display: block;
      margin: 30px auto;
      padding: 12px 25px;
      font-size: 1rem;
      color: white;
      background-color: var(--dark-gray);
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      text-align: center;
      transition: background-color 0.3s ease;
      box-shadow: var(--card-shadow);
    }

    .logout-btn:hover {
      background-color: #1a252f;
    }

    .confirm-btn {
      background-color: var(--info-color);
      color: white;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 8px;
      font-size: 0.8rem;
      min-width: 28px;
    }

    .confirm-btn:hover {
      background-color: #16a085;
    }

    /* Formularios */
    select {
      padding: 8px;
      border-radius: 4px;
      border: 1px solid var(--medium-gray);
      flex: 1;
      font-size: 0.9rem;
    }

    input {
      padding: 8px;
      border-radius: 4px;
      border: 1px solid var(--medium-gray);
      flex: 1;
      font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .desktop-view {
        display: none;
      }

      .mobile-view {
        display: block;
      }

      .card-row {
        flex-direction: column;
        align-items: flex-start;
      }

      .card-row label {
        margin-bottom: 5px;
        flex: 0 0 auto;
      }

      .editable-field {
        width: 100%;
      }

      .card-actions {
        flex-direction: column;
        gap: 8px;
      }

      .card-actions button {
        width: 100%;
        margin: 2px 0;
      }
    }

    @media (max-width: 768px) {
      body {
        padding: 15px;
      }

      h1 {
        font-size: 1.5rem;
      }

      h2 {
        font-size: 1.1rem;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 10px;
      }

      .header {
        margin-bottom: 15px;
      }

      h1 {
        font-size: 1.3rem;
      }

      h2 {
        font-size: 1rem;
      }

      .logout-btn {
        width: 100%;
        padding: 12px;
      }

      .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>PANEL DE ADMINISTRACIÓN</h1>
      <h2 id="userInfo">Cargando...</h2>
    </div>

    <div id="notification"></div>

    <!-- Vista para desktop -->
    <div class="desktop-view">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>DIRECCIÓN</th>
            <th>VELOCIDAD</th>
            <th>ESTADO</th>
            <th>VIA</th>
            <th>PK</th>
            <th>ÚLTIMA MODIFICACIÓN</th>
            <th>TIPO RADAR</th>
            <th>ACCIÓN</th>
          </tr>
        </thead>
        <tbody id="radares-table-body"></tbody>
      </table>
    </div>

    <!-- Vista para móvil -->
    <div class="mobile-view">
      <div id="radares-cards-container" class="radar-cards-container"></div>
    </div>

    <button class="logout-btn" onclick="logout()">CERRAR SESIÓN</button>
  </div>
</body>
</html>
