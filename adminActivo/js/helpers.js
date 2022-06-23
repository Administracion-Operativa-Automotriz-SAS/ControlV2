function mostrarModal(titulo, mensaje, callback, tiempo){
    tiempo = tiempo ? tiempo : 0; 
    var modal_notificacion = $("#modal-notificacion");
    var body = modal_notificacion.find('.modal-body p');
    var hTitlulo = modal_notificacion.find('.modal-title');

    modal_notificacion.modal('show');
    body.html(mensaje);
    hTitlulo.html(titulo);
    
    if(tiempo > 0)
    {
        $(".modal-body .btn").remove();
        setTimeout(function(){
            body.html("Recargando...");
            setTimeout(function(){
                callback();
            }, 2000);
        }, tiempo * 1000);
    }
    else
    {
          
    }
}