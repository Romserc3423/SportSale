    
    const boton = document.getElementById('botoninicio');
    const flechita = document.getElementById('flechita');


    function mostrarSiguiente() {
    document.getElementById('Primeraseccion').style.display = 'none';
    document.getElementById('Segundaseccion').style.display = 'block';
}
function regresar1() {
    document.getElementById('Primeraseccion').style.display = 'block';
    document.getElementById('Segundaseccion').style.display = 'none';
}

    boton.addEventListener("click", function() {
        mostrarSiguiente();
    });

flechita.addEventListener("click", function(){
    regresar1();

});