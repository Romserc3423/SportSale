 const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("contrase");

togglePassword.addEventListener('click', function(){
 if (passwordInput.value == "") {
    alert("No puedes usar el boton de mostrar contraseña si no has ingresado ninguna contraseña");
 } else {
    
  const isPassword = passwordInput.type === "password";
  passwordInput.type = isPassword ? "text" : "password";

  togglePassword.classList.toggle("fa-eye");
  togglePassword.classList.toggle("fa-eye-slash");
;}});
      
    
function mostrarRegistro() {
    document.getElementById('login-box').style.display = 'none';
    document.getElementById('register-asesorgeren').style.display = 'none';
    document.getElementById('register-box').style.display = 'block';
}

function mostrarLogin() {
    document.getElementById('register-box').style.display = 'none';   
    document.getElementById('register-asesorgeren').style.display = 'none';
    document.getElementById('login-box').style.display = 'block';
}

function mostrarLogin2() {   
    document.getElementById('login-box').style.display = 'none';
    document.getElementById('register-box').style.display = 'none';
    document.getElementById('register-asesorgeren').style.display = 'block'; 
}