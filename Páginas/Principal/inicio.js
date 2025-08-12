    
    const boton = document.getElementById('botoninicio');
    const flechita = document.getElementById('flechita');
    const flechita2 = document.getElementById('flechita2');
    const boton2 = document.getElementById('boton2');

    function mostrarSiguiente() {
    document.getElementById('Primeraseccion').style.display = 'none';
    document.getElementById('Segundaseccion').style.display = 'block';
    document.getElementById('Terceraseccion').style.display = 'none';
}
    function mostrarSiguiente2() {
    document.getElementById('Primeraseccion').style.display = 'none';
    document.getElementById('Segundaseccion').style.display = 'none';
    document.getElementById('Terceraseccion').style.display = 'block';
}
function regresar1() {
    document.getElementById('Primeraseccion').style.display = 'block';
    document.getElementById('Segundaseccion').style.display = 'none';
    document.getElementById('Terceraseccion').style.display = 'none';
}
function regresar2() {
    document.getElementById('Primeraseccion').style.display = 'none';
    document.getElementById('Segundaseccion').style.display = 'block';
    document.getElementById('Terceraseccion').style.display = 'none';
}


    boton.addEventListener("click", function() {
        mostrarSiguiente();
    });
        boton2.addEventListener("click", function() {
        mostrarSiguiente2();
    });

flechita.addEventListener("click", function(){
    regresar1();

});

flechita2.addEventListener("click", function(){
    regresar2();

});