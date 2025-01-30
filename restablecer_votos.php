<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restablecer Votos de Radares</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f9;
    }

    h1 {
      background-color: #4CAF50;
      color: white;
      text-align: center;
      padding: 20px 0;
    }

    #radares-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 20px;
    }

    .radar {
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin: 10px;
      width: 250px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.3s ease;
    }

    .radar:hover {
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .radar h3 {
      font-size: 18px;
      margin: 0;
      color: #333;
    }

    .radar p {
      font-size: 14px;
      color: #555;
      margin: 8px 0;
    }

    button {
      background-color: #4CAF50;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      margin-top: 10px;
      font-size: 16px;
    }

    button:hover {
      background-color: #45a049;
    }

    #reset-button {
      background-color: #f44336;
      margin: 20px auto;
      width: 300px auto;
    }

    #reset-button:hover {
      background-color: #e53935;
    }
  </style>
</head>
<body>
  <h1>CONTABILIDAD VOTOS RADARES</h1>
  <p id="userInfo"></p>
  <button id="reset-button">RESTABLECER TODOS LOS VOTOS</button>
  <div id="radares-container"></div>
  <button onclick="logout()">Cerrar sesión</button>
  
  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
    import { getAuth, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-auth.js";
    import { getDatabase, ref, get, onValue, update } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";
	
	window.onload = function() {
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

    // Configuración de Firebase para radares
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

    // Inicializar Firebase para login y radares
    const appLogin = initializeApp(firebaseConfigLogin); // Base de datos de login
    const appRadares = initializeApp(firebaseConfigRadares, "radares");
    const auth = getAuth(appLogin); // Autenticación
    const db = getDatabase(appRadares); // Base de datos de radares

    // Función para verificar si el usuario está autenticado
    onAuthStateChanged(auth, (user) => {
      if (!user) {
        window.location.href = 'login2'; // Redirige a la página de login si no está autenticado
      } else {
        document.getElementById('userInfo').innerText = `Bienvenido, ${user.email}`;
        loadRadares(); // Cargar radares después de autenticarse
      }
    });

    // Recuperar los radares desde Firebase
    function loadRadares() {
      const radaresRef = ref(db, 'radares'); // Uso de 'ref' correctamente
      get(radaresRef).then(snapshot => {
        const radares = snapshot.val();
        const container = document.getElementById('radares-container');
        container.innerHTML = ''; // Limpiar antes de cargar
        for (let id in radares) {
          const radar = radares[id];
          const radarElement = document.createElement('div');
          radarElement.classList.add('radar');
          radarElement.innerHTML = `
            <h3>${radar.road}<br>(${radar.direction})</h3>
            <p>Votos Positivos: ${radar.votos_positivos}</p>
            <p>Votos Negativos: ${radar.votos_negativos}</p>
            <button class="reset-votos" data-id="${id}">RESTABLECER VOTOS</button>
            <hr>
          `;
          container.appendChild(radarElement);
        }

        // Asociamos el evento de click a los botones de restablecer votos
        const resetButtons = document.querySelectorAll('.reset-votos');
        resetButtons.forEach(button => {
          button.addEventListener('click', function() {
            const id = button.getAttribute('data-id');
            resetVotos(id);
          });
        });
      }).catch((error) => {
        console.error('Error al cargar los radares:', error);
      });
    }

    // Restablecer los votos de un radar específico
    function resetVotos(id) {
      console.log('Restableciendo votos para el radar con id:', id);
      const radarRef = ref(db, 'radares/' + id); // Uso correcto de 'ref' con 'db'
      update(radarRef, {
        votos_positivos: 0,
        votos_negativos: 0
      }).then(() => {
        console.log('Votos restablecidos correctamente.');
        alert('Votos restablecidos con éxito.');
        loadRadares(); // Recargar los radares después de restablecer
      }).catch((error) => {
        console.error('Error al restablecer los votos:', error);
        alert('Hubo un error al restablecer los votos.');
      });
    }

    // Restablecer los votos de todos los radares
    function resetAllVotos() {
      console.log('Iniciando el restablecimiento de todos los votos...');
      const radaresRef = ref(db, 'radares'); // Uso correcto de 'ref' con 'db'
      get(radaresRef).then(snapshot => {
        const radares = snapshot.val();
        if (radares) {
          console.log('Radares encontrados:', radares);
          for (let id in radares) {
            const radarRef = ref(db, 'radares/' + id); // Uso correcto de 'ref' con 'db'
            update(radarRef, {
              votos_positivos: 0,
              votos_negativos: 0
            }).then(() => {
              console.log(`Votos de radar ${id} restablecidos.`);
            }).catch((error) => {
              console.error(`Error al restablecer los votos del radar ${id}:`, error);
            });
          }
          alert('Todos los votos han sido restablecidos.');
          loadRadares(); // Recargar los radares después de restablecer
        } else {
          console.log('No se encontraron radares.');
        }
      }).catch((error) => {
        console.error('Error al cargar los radares:', error);
      });
    }

    // Asociamos la función resetAllVotos al botón "Restablecer Todos los Votos"
    const resetAllButton = document.getElementById('reset-button');
    resetAllButton.addEventListener('click', resetAllVotos);

    // Función para cerrar sesión
    const logout = () => {
      signOut(auth)
        .then(() => {
          console.log("Sesión cerrada exitosamente.");
          window.location.href = 'login2'; // Redirigir a la página de login después de cerrar sesión
        })
        .catch((error) => console.error("Error al cerrar sesión:", error));
    };
};
  </script>
</body>
</html>