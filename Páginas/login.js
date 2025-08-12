 const togglePassword = document.getElementById("togglePassword");

const togglePassword2 = document.getElementById("togglePassword2");
const passwordInput = document.getElementById("contrase");
const passwordInput2 = document.getElementById("contrase2");
const passwordInput3 = document.getElementById("contrase3");
const passwordInput4 = document.getElementById("contrase4");
const passwordInput5 = document.getElementById("contrase5");
togglePassword.addEventListener('click', function(){
 if (passwordInput.value == "") {
    alert("No puedes usar el boton de mostrar contraseña si no has ingresado ninguna contraseña");
 } else {
    
  const isPassword = passwordInput.type === "password";
  passwordInput.type = isPassword ? "text" : "password";

  togglePassword.classList.toggle("fa-eye");
  togglePassword.classList.toggle("fa-eye-slash");
;}});

togglePassword2.addEventListener('click', function(){
 if (passwordInput2.value == "") {
    alert("No puedes usar el boton de mostrar contraseña si no has ingresado ninguna contraseña");
 } else {

  const isPassword2 = passwordInput2.type === "password";
  passwordInput2.type = isPassword2 ? "text" : "password";

  togglePassword2.classList.toggle("fa-eye");
  togglePassword2.classList.toggle("fa-eye-slash");
;}});

togglePassword3.addEventListener('click', function(){
 if (passwordInput3.value == "") {
    alert("No puedes usar el boton de mostrar contraseña si no has ingresado ninguna contraseña");
 } else {

  const isPassword3 = passwordInput3.type === "password";
  passwordInput3.type = isPassword3 ? "text" : "password";

  togglePassword3.classList.toggle("fa-eye");
  togglePassword3.classList.toggle("fa-eye-slash");
;}});
      
togglePassword4.addEventListener('click', function(){
 if (passwordInput4.value == "") {
    alert("No puedes usar el boton de mostrar contraseña si no has ingresado ninguna contraseña");
 } else {

  const isPassword4 = passwordInput4.type === "password";
  passwordInput4.type = isPassword4 ? "text" : "password";

  togglePassword4.classList.toggle("fa-eye");
  togglePassword4.classList.toggle("fa-eye-slash");
;}});

togglePassword5.addEventListener('click', function(){
 if (passwordInput5.value == "") {
    alert("No puedes usar el boton de mostrar contraseña si no has ingresado ninguna contraseña");
 } else {

  const isPassword5 = passwordInput5.type === "password";
  passwordInput5.type = isPassword5 ? "text" : "password";

  togglePassword5.classList.toggle("fa-eye");
  togglePassword5.classList.toggle("fa-eye-slash");
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