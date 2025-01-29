<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="https://ahorraunamulta.com/favicon.png" />
  
  <title>PANEL DE ADMINISTRACIÓN - AHORRA UNA MULTA</title>
  
  <!-- Firebase SDK -->
  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
    import { getAuth, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-auth.js";
    import { getDatabase, ref, onValue, update } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";
    
    // Configuración para la base de datos de login (Firebase Authentication)
    const firebaseConfigLogin = {
      apiKey: "AIzaSyAX4uy3ON91cwK3Tt9r5Eqpucyf4sfv0No",
      authDomain: "login-radares.firebaseapp.com",
      projectId: "login-radares",
      storageBucket: "login-radares.firebasestorage.app",
      messagingSenderId: "661760692554",
      appId: "1:661760692554:web:2da6e767592800380eb1b3",
      measurementId: "G-S2ZCB85HX1"
    };

    // Configuración para la base de datos de radares (Firebase Realtime Database)
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

    // Inicializar Firebase
    const appLogin = initializeApp(firebaseConfigLogin); // Base de datos de login
    const appRadares = initializeApp(firebaseConfigRadares, "radares"); // Base de datos de radares

    const auth = getAuth(appLogin); // Autenticación
    const database = getDatabase(appRadares); // Base de datos de radares

    // Verificar si el usuario está autenticado
    onAuthStateChanged(auth, (user) => {
      if (!user) {
        window.location.href = 'login.html'; // Redirige a la página de login si no está autenticado
      } else {
        document.getElementById('userInfo').innerText = `Bienvenido, ${user.email}`;
        fetchRadares(); // Cargar radares después de autenticarse
      }
    });

    // Función para obtener los radares desde la base de datos
    const fetchRadares = () => {
      const radaresRef = ref(database, 'radares');
      onValue(radaresRef, (snapshot) => {
        const data = snapshot.val();
        const radaresTableBody = document.getElementById('radares-table-body');
        radaresTableBody.innerHTML = '';

        if (data) {
          // Convertir los datos en un array y ordenar por last_updated
          const radaresArray = Object.keys(data).map((key) => {
            return { id: key, ...data[key] };
          });

          // Asegurarse de que last_updated sea un número (milisegundos)
          radaresArray.forEach((radar) => {
            if (radar.last_updated) {
              radar.last_updated = new Date(radar.last_updated).getTime(); // Convertir a milisegundos
            }
          });

          // Ordenar los radares por last_updated en orden descendente
          radaresArray.sort((a, b) => b.last_updated - a.last_updated);

          // Mostrar los radares ordenados en la tabla
radaresArray.forEach((radar) => {
  const row = document.createElement('tr');

  // Función para crear un campo editable con botón confirmar
  const createEditableCell = (value, fieldName) => {
    return `
      <td>
        <input type="text" value="${value}" data-field="${fieldName}" data-id="${radar.id}">
        <button class="confirm-btn" data-field="${fieldName}" data-id="${radar.id}">Confirmar</button>
      </td>
    `;
  };

  const lastUpdated = radar.last_updated ? formatDate(radar.last_updated) : 'N/A';

  // Asegurarte de que radar.road se trata como texto
  const roadValue = radar.road || 'Sin especificar'; // Si está vacío, muestra un texto predeterminado

  row.innerHTML = `
    <td>
      <button class="toggle-id-btn" data-id="${radar.id}">Ver ID</button>
      <span class="radar-id" style="display: none;">${radar.id}</span>
    </td>
    ${createEditableCell(radar.direction, 'direction')}
    ${createEditableCell(radar.speed, 'speed')}
    <!--${createEditableCell(radar.status, 'status')}-->
    <td><span>${radar.status}</span></td> <!-- Aquí está el campo "estado" como un span (no editable) -->
    ${createEditableCell(roadValue, 'road')} <!-- Aquí se asegura que "Vía" sea texto -->
    ${createEditableCell(radar.pk, 'pk')} <!-- Nueva celda editable para radar.pk -->
    <td>${lastUpdated}</td>
    <!--${createEditableCell(radar.radarType, 'radarType')}-->
    <td><span>${radar.radarType}</span></td> <!-- Aquí está el campo "tipo radar" como un span (no editable) -->
    <td>
      ${radar.status !== 'active' ? `<button class="approve-btn" data-id="${radar.id}">Activar</button>` : ''}
      ${radar.status !== 'inactive' ? `<button class="reject-btn" data-id="${radar.id}">Desactivar</button>` : ''}
    </td>
  `;

  radaresTableBody.appendChild(row);
});

// Agregar event listeners para los botones "Ver ID"
document.querySelectorAll('.toggle-id-btn').forEach((btn) => {
  btn.addEventListener('click', (e) => {
    const idSpan = e.target.nextElementSibling; // El <span> con el ID está justo después del botón
    if (idSpan.style.display === 'none') {
      idSpan.style.display = 'inline'; // Mostrar el ID
      e.target.textContent = 'Ocultar ID'; // Cambiar el texto del botón
    } else {
      idSpan.style.display = 'none'; // Ocultar el ID
      e.target.textContent = 'Ver ID'; // Cambiar el texto del botón
    }
  });
});

// Agregar event listeners para los botones "Confirmar"
// Asegurándonos de que solo los botones "Confirmar" actualicen los datos
document.querySelectorAll('.confirm-btn').forEach((btn) => {
  btn.addEventListener('click', (e) => {
    e.preventDefault(); // Prevenir cualquier acción no deseada en el evento de clic

    const field = e.target.dataset.field;
    const id = e.target.dataset.id;
    const input = e.target.previousElementSibling; // El input está antes del botón "Confirmar"
    const newValue = input.value;

    // Obtener la fecha y hora actuales
    const currentDate = Date.now(); // Timestamp actual (milisegundos)

    // Actualizar en la base de datos Firebase cuando se confirme el cambio
    const radarRef = ref(database, `radares/${id}`);
    const updateData = {
      [field]: newValue, // Actualizar el campo modificado
      last_updated: currentDate // Actualizar la fecha de la última modificación
    };

    update(radarRef, updateData)
      .then(() => showNotification(`Campo ${field} actualizado a "${newValue}".`))
      .catch((error) => showNotification(`Error: ${error.message}`));
  });
});

// Agregar event listeners para los botones "Confirmar"
// Asegurándonos de que solo los botones "Confirmar" actualicen los datos
// Agregar event listeners para los botones "Confirmar"
document.querySelectorAll('.confirm-btn').forEach((btn) => {
  btn.addEventListener('click', (e) => {
    e.preventDefault(); // Prevenir cualquier acción no deseada en el evento de clic

    const field = e.target.dataset.field;
    const id = e.target.dataset.id;
    const input = e.target.previousElementSibling; // El input está antes del botón "Confirmar"
    const newValue = input.value;

    // Obtener la fecha y hora actuales (timestamp)
    const currentDate = Date.now();

    // Referencia al radar específico
    const radarRef = ref(database, `radares/${id}`);

    // Crear objeto para los datos que queremos actualizar
    const updateData = {
      [field]: newValue, // Actualizar el campo modificado
      last_updated: currentDate // Actualizar last_updated con la fecha actual
    };

    // Realizar la actualización en Firebase
    update(radarRef, updateData)
      .then(() => {
        // Notificación de éxito (comentada)
        // showNotification(`Campo ${field} actualizado y last_updated modificado.`);
      })
      .catch((error) => {
        // Notificación de error (comentada)
        // showNotification(`Error: ${error.message}`);
        
        // O también puedes loguear el error en la consola si prefieres no mostrarlo:
        console.error(`Error al actualizar el campo ${field}: ${error.message}`);
      });
  });
});

// Agregar event listeners para los campos editables
document.querySelectorAll('input').forEach(input => {
  input.addEventListener('blur', (e) => {
    const value = e.target.value;
    const field = e.target.dataset.field;
    const id = e.target.dataset.id;
    
    // Actualizar en la base de datos Firebase cuando un campo editable pierde el foco
    const radarRef = ref(database, `radares/${id}`);
    const updateData = {};
    updateData[field] = value;
    update(radarRef, updateData)
      .then(() => showNotification(`Campo ${field} actualizado.`))
      .catch((error) => showNotification(`Error: ${error.message}`));
  });
});

          // Activar radar
          document.querySelectorAll('.approve-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
              const id = btn.dataset.id;
              const radarRef = ref(database, `radares/${id}`);
              update(radarRef, {
                status: 'active',
                last_updated: Date.now() // Actualizamos la fecha de la última actualización
              })
                .then(() => showNotification(`Radar con ID ${id} activado.`))
                .catch((error) => showNotification(`Error: ${error.message}`));
            });
          });

          // Desactivar radar
          document.querySelectorAll('.reject-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
              const id = btn.dataset.id;
              const radarRef = ref(database, `radares/${id}`);
              update(radarRef, {
                status: 'inactive',
                last_updated: Date.now() // Actualizamos la fecha de la última actualización
              })
                .then(() => showNotification(`Radar con ID ${id} desactivado.`))
                .catch((error) => showNotification(`Error: ${error.message}`));
            });
          });
        }
      });
    };

    // Función para formatear la fecha
    function formatDate(isoDate) {
      const date = new Date(isoDate);
      
      const day = String(date.getDate()).padStart(2, '0'); // Día con 2 dígitos
      const month = String(date.getMonth() + 1).padStart(2, '0'); // Mes con 2 dígitos
      const year = date.getFullYear(); // Año
      const hours = String(date.getHours()).padStart(2, '0'); // Hora con 2 dígitos
      const minutes = String(date.getMinutes()).padStart(2, '0'); // Minutos con 2 dígitos

      // Devuelve la fecha en el formato deseado
      return `${day}-${month}-${year} ${hours}:${minutes}`;
    }

    // Función para mostrar notificaciones
    const showNotification = (message) => {
      const notification = document.getElementById('notification');
      notification.textContent = message;
      notification.style.display = 'block';
      setTimeout(() => {
        notification.style.display = 'none';
      }, 3000);
    };

    // Función para cerrar sesión
    const logout = () => {
  signOut(auth)
    .then(() => (window.location.href = 'login'))
    .catch((error) => console.error("Error al cerrar sesión:", error));
};

// Exponer logout al ámbito global
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

    #notification {
      display: none;
      margin-bottom: 20px;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border-radius: 4px;
    }

    /* Estilo independiente para el botón de Cerrar Sesión */
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
  background-color: #2196F3; /* Azul */
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
  margin-left: 5px; /* Espacio entre el input y el botón */
  transition: background-color 0.3s ease;
}

.confirm-btn:hover {
  background-color: #1976D2; /* Azul más oscuro al pasar el ratón */
}

.confirm-btn:focus {
  outline: none; /* Elimina el borde cuando está enfocado */
}

/* Estilo para el botón "Ver ID" */
.toggle-id-btn {
  background-color: #FFA500; /* Naranja */
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  padding: 5px 10px;
  font-size: 14px;
}

.toggle-id-btn:hover {
  background-color: #FF8C00; /* Naranja más oscuro al pasar el ratón */
}

.toggle-id-btn:focus {
  outline: none;
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