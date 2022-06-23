function re_sesion()
{
  if(confirm('Su sesion ha expirado. Desea recuperar la sesion?'))
  {
      var caracteristicas = 'height=350, width=400, channelmode=0, dependent=1, chrome=yes, location=0, toolbar=0, directories=0,status=0, statusbar=0, linemenubar=0, menubar=0, modal=1, left=50, top=50, resizable=1, scrollbars=1';
      var NV = window.open('','Reingreso',caracteristicas);
      var doc = NV.document;
      doc.open('text/html', 'replace');
      doc.write("<HTML><head><meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>");
      doc.write("<meta http-equiv='Cache-Control' content='no-cache; must-revalidate; proxy-revalidate; max-age=10'>");
      doc.write("<style type='text/css'>@import url(inc/css/estilo.css);</style>");
      doc.write("<script language='javascript' src='inc/js/aqrenc.js'></script><script language='javascript' src='inc/js/funciones.js'></script></head>");
      doc.write("<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' onload='centrar(400,350);document.forma.Usuario.focus();' onunload='opener.location.reload();'>");
      doc.write("<form method='post' target='_self' name='forma' id='forma'>");
      doc.write("<table align='center' border=0 cellspacing=1 cellpadding=0 style='empty-cells:show;' bgcolor='eeeeee'>");
      doc.write("<tr><td>Usuario</td><td><input type='text' name='Usuario'></td></tr>");
      doc.write("<tr><td>Password</td><td><input type='password' name='Password'></td></tr>");
      doc.write("</table><center><input type='button' value='Continuar' onclick='var dato1=encripta(document.forma.Usuario.value,AqrSoftware); var dato2=encripta(document.forma.Password.value,AqrSoftware);re_ingresar(dato1,dato2);'>");
      doc.write("</center><iframe name='sp' id='sp' height='100' width='100%' scrolling='no' frameborder='no' style='visibility:visible;'></iframe></body></html>");
      doc.close();
  }
}