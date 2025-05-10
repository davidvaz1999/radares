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
        radaresTableBody.innerHTML = '';

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

          radaresArray.forEach((radar) => {
            const row = document.createElement('tr');

            const createEditableCell = (value, fieldName) => {
              return `
                <td>
                  <input type="text" value="${value || ''}" data-field="${fieldName}" data-id="${radar.id}">
                  <button class="confirm-btn" data-field="${fieldName}" data-id="${radar.id}">Confirmar</button>
                </td>
              `;
            };

            const lastUpdated = radar.last_updated ? formatDate(radar.last_updated) : 'N/A';
            const roadValue = radar.road || 'Sin especificar';

            row.innerHTML = `
              <td>
                <button class="toggle-id-btn" data-id="${radar.id}">Ver ID</button>
                <span class="radar-id" style="display: none;">${radar.id}</span>
              </td>
              ${createEditableCell(radar.direction, 'direction')}
              ${createEditableCell(radar.speed, 'speed')}
              <td>
                <select data-field="status" data-id="${radar.id}">
                  <option value="active" ${radar.status === 'active' ? 'selected' : ''}>Activo</option>
                  <option value="inactive" ${radar.status === 'inactive' ? 'selected' : ''}>Inactivo</option>
                  <option value="pending_review" ${radar.status === 'pending_review' ? 'selected' : ''}>Pendiente</option>
                </select>
                <button class="confirm-btn" data-field="status" data-id="${radar.id}">Confirmar</button>
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
                <button class="confirm-btn" data-field="radarType" data-id="${radar.id}">Confirmar</button>
              </td>
              <td>
                ${radar.status !== 'active' ? `<button class="approve-btn" data-id="${radar.id}">Activar</button>` : ''}
                ${radar.status !== 'inactive' ? `<button class="reject-btn" data-id="${radar.id}">Desactivar</button>` : ''}
                ${radar.status === 'pending_review' ? `<button class="complete-btn" data-id="${radar.id}">Completar</button>` : ''}
              </td>
            `;

            radaresTableBody.appendChild(row);
          });

          document.querySelectorAll('.toggle-id-btn').forEach((btn) => {
            btn.addEventListener('click', (e) => {
              const idSpan = e.target.nextElementSibling;
              if (idSpan.style.display === 'none') {
                idSpan.style.display = 'inline';
                e.target.textContent = 'Ocultar ID';
              } else {
                idSpan.style.display = 'none';
                e.target.textContent = 'Ver ID';
              }
            });
          });

          document.querySelectorAll('.confirm-btn').forEach((btn) => {
            btn.addEventListener('click', (e) => {
              e.preventDefault();
              const field = e.target.dataset.field;
              const id = e.target.dataset.id;
              const input = e.target.previousElementSibling;
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
      });
    };

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
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: center;
    }
    th {
      background-color: #f4f4f4;
    }
    button {
      padding: 5px 10px;
      margin: 0 2px;
      border: none;
      color: white;
      border-radius: 4px;
      cursor: pointer;
    }
    .approve-btn {
      background-color: #4CAF50;
    }
    .reject-btn {
      background-color: #F44336;
    }
    .complete-btn {
      background-color: #FF9800;
    }
    #notification {
      display: none;
      margin-bottom: 20px;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border-radius: 4px;
    }
    .logout-btn {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      font-size: 16px;
      color: white;
      background-color: #2196F3;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-align: center;
      transition: background-color 0.3s ease;
    }
    .logout-btn:hover {
      background-color: #1976D2;
    }
    .logout-btn:focus {
      outline: none;
    }
    .confirm-btn {
      background-color: #2196F3;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 5px;
      transition: background-color 0.3s ease;
    }
    .confirm-btn:hover {
      background-color: #1976D2;
    }
    .confirm-btn:focus {
      outline: none;
    }
    .toggle-id-btn {
      background-color: #FFA500;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      padding: 5px 10px;
      font-size: 14px;
    }
    .toggle-id-btn:hover {
      background-color: #FF8C00;
    }
    .toggle-id-btn:focus {
      outline: none;
    }
    select {
      padding: 5px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    input {
      padding: 5px;
      border-radius: 4px;
      border: 1px solid #ccc;
      width: 80%;
    }
  </style>
</head>
<body>
  <h1>PANEL DE ADMINISTRACIÓN</h1>
  <h2 id="userInfo">Cargando...</h2>
  <div id="notification"></div>
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
        <th>ACCIÓN</th>
      </tr>
    </thead>
    <tbody id="radares-table-body"></tbody>
  </table>
  <button class="logout-btn" onclick="logout()">CERRAR SESIÓN</button>
</body>
</html>