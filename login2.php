<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="robots" content="noindex, nofollow" />
  <title>LOGIN - AHORRA UNA MULTA</title>

  <!-- Firebase SDK en la versión 8.x.x -->
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-firestore.js"></script>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="https://ahorraunamulta.com/favicon.png" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    /* General Styles */
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #4caf50, #81c784);
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #333;
    }

    h1, h2 {
      margin: 0;
      padding: 0;
    }

    .login-container {
      width: 100%;
      max-width: 400px;
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      text-align: center;
    }

    .login-container h2 {
      color: #333;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .login-container label {
      font-size: 14px;
      color: #555;
      display: block;
      margin-bottom: 5px;
      text-align: left;
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
      font-size: 14px;
      transition: border 0.3s ease;
    }

    .login-container input:focus {
      border: 1px solid #4caf50;
      outline: none;
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      background-color: #4caf50;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .login-container button:hover {
      background-color: #45a049;
      transform: translateY(-2px);
    }

    .login-container .error-message {
      color: #d32f2f;
      font-size: 14px;
      margin-top: 10px;
    }

    /* Footer Styles */
    footer {
      padding: 60px;
    }

    .btn {
      display: inline-block;
      padding: 10px 20px;
      font-size: 16px;
      color: white;
      background-color: #007bff;
      text-decoration: none;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      text-align: center;
    }

    .btn:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>INICIAR SESIÓN</h2>

    <form id="loginForm">
      <label for="email">CORREO ELECTRÓNICO:</label>
      <input type="email" id="email" placeholder="ejemplo@correo.com" required>

      <label for="password">CONTRASEÑA:</label>
      <input type="password" id="password" placeholder="••••••••" required>

      <button type="submit">INICIAR SESIÓN</button>

      <div class="error-message" id="error-message"></div>
    </form>
  </div>

  <footer>
    <a href="https://www.ahorraunamulta.com" class="btn">IR A <b>AHORRA UNA MULTA</b></a>
  </footer>

  <script>
    // Configuración de Firebase
    const firebaseConfig = {
      apiKey: "AIzaSyAX4uy3ON91cwK3Tt9r5Eqpucyf4sfv0No",
      authDomain: "login-radares.firebaseapp.com",
      projectId: "login-radares",
      storageBucket: "login-radares.firebasestorage.app",
      messagingSenderId: "661760692554",
      appId: "1:661760692554:web:2da6e767592800380eb1b3",
      measurementId: "G-S2ZCB85HX1"
    };

    // Inicializa Firebase
    firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth();

    // Manejo del formulario
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('error-message');

    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      auth.signInWithEmailAndPassword(email, password)
        .then((userCredential) => {
          console.log("Inicio de sesión exitoso:", userCredential.user);
          window.location.href = 'restablecer_votos';
        })
        .catch((error) => {
          const errorCode = error.code;
          const errorText = {
            'auth/invalid-email': "El correo electrónico no es válido.",
            'auth/wrong-password': "La contraseña es incorrecta.",
            'auth/user-not-found': "No se encontró un usuario con ese correo."
          }[errorCode] || "Error al iniciar sesión, probablemente el correo electrónico o la contraseña sean incorrectos.";
          errorMessage.textContent = errorText;
        });
    });
  </script>

</body>
</html>
