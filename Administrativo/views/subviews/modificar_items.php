  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up Form</title>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script  src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

        <link rel="stylesheet" href="css/normalize.css">
        <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
    </head>
    <body>
<style>
*, *:before, *:after {
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  font-family: 'Nunito', sans-serif;
  color: #384047;
}

form {
  max-width: 300px;
  margin: 10px auto;
  padding: 10px 20px;
  background: #f4f7f8;
  border-radius: 8px;
}

h1 {
  margin: 0 0 30px 0;
  text-align: center;
}

input[type="text"],
input[type="password"],
input[type="date"],
input[type="datetime"],
input[type="email"],
input[type="number"],
input[type="search"],
input[type="tel"],
input[type="time"],
input[type="url"],
textarea,
select {
  background: rgba(255,255,255,0.1);
  border: none;
  font-size: 16px;
  height: auto;
  margin: 0;
  outline: 0;
  padding: 15px;
  width: 100%;
  background-color: #e8eeef;
  color: #8a97a0;
  box-shadow: 0 1px 0 rgba(0,0,0,0.03) inset;
  margin-bottom: 30px;
}

input[type="radio"],
input[type="checkbox"] {
  margin: 0 4px 8px 0;
}

select {
  padding: 6px;
  height: 32px;
  border-radius: 2px;
}

button {
  padding: 19px 39px 18px 39px;
  color: #FFF;
  background-color: #4bc970;
  font-size: 18px;
  text-align: center;
  font-style: normal;
  border-radius: 5px;
  width: 100%;
  border: 1px solid #3ac162;
  border-width: 1px 1px 3px;
  box-shadow: 0 -1px 0 rgba(255,255,255,0.1) inset;
  margin-bottom: 10px;
}

fieldset {
  margin-bottom: 30px;
  border: none;
}

legend {
  font-size: 1.4em;
  margin-bottom: 10px;
}

label {
  display: block;
  margin-bottom: 8px;
}

label.light {
  font-weight: 300;
  display: inline;
}

.number {
  background-color: #5fcf80;
  color: #fff;
  height: 30px;
  width: 30px;
  display: inline-block;
  font-size: 0.8em;
  margin-right: 4px;
  line-height: 30px;
  text-align: center;
  text-shadow: 0 1px 0 rgba(255,255,255,0.2);
  border-radius: 100%;
}

@media screen and (min-width: 480px) {

  form {
    max-width: 480px;
  }

}
.labelNuevo{
	display: flex;justify-content: space-around;
}
.inpustJuntos{
	display: flex;justify-content: space-between;
}
</style>

      <form action='zrequisicion.php' method='POST' name='forma' id='forma'>
      
        <fieldset>
          <legend><span class="number">1</span>Modificar items</legend>
        </fieldset>
        
        <fieldset>
          <div class="labelNuevo">
		  
		  <label for="item">Item :</label>
          <label for="tipoRequi">Tipo de requicision:</label>
		  
		  </div>
		  <div class="inpustJuntos">
		  
		  
          
          <label>&nbsp;&nbsp;&nbsp;</label>
          
		  <select id="tipoRequi" name="tipoRequi">
		  
		  <?php if($var->ntipo == 1 || $var->ntipo == ""){ ?><option selected value="0">NO DEFINIDO</option> <?php } ?>
		  
		  <?php while($i=mysql_fetch_object($var2)){ ?>
		  <option <?php if($var->ntipo == $i->nameItem){echo "selected";} ?> value='<?php echo $i->id?>'><?php echo $i->nombre ?></option>  
		  <?php } ?>
		  
		  </select>
		  <label>&nbsp;&nbsp;&nbsp;</label>
		  <select id="tipoBS" name="tipoBS">
		  <?php while($i=mysql_fetch_object($varTipo)){ ?>
		  <option <?php if($var->tipoBS == $i->nombre){echo "selected";} ?> value='<?php echo $i->id?>'><?php echo $i->nombre ?></option>  
		  <?php } ?>
		</select>
		  
		  
          
		  </div>
          
		  <label for="unidad_medida">Clase:</label>
          
		  <select id="nclase" name="nclase">
		  <?php if($var->nclase == null || $var->nclase == ""){ ?><option selected value="0">NO DEFINIDO</option> <?php } ?>
		  <?php while($i=mysql_fetch_object($varQueryClase)){ ?>
		  <option <?php if($var->nclase == $i->nombre){echo "selected";} ?> value='<?php echo $i->id?>'><?php echo $i->nombre ?></option>  
		  <?php } ?>
		  </select>
		  
		  <div class="labelNuevo">
		  <label for="tipo">Tipo Cobro:</label>
          <label for="factor">Factor:</label>
		  
		  </div>
		  
		  <div class="inpustJuntos">
		  <select id="tipo_cobro" name="tipo_cobro">
		  <option <?php if($var->tipo_cobro == 'S'){echo "selected";} ?> value='S'>Sin cobro</option>  
		  <option <?php if($var->tipo_cobro == 'C'){echo "selected";} ?> value='C'>Con cobro</option>  
		  
		  </select>
		  
		  <label>&nbsp;&nbsp;&nbsp;</label>
		  <select id="factor" name="factor">
		  <?php if($var->factor == "" || $var->factor = null || $var->factor == "NO DEFINIDA"){ ?>  <option selected value="NO DEFINIDA">NO DEFINIDO</option> <?php } ?>
		  <option <?php if($var->factor == 'Flota'){echo "selected";} ?> value='Flota'>Flota</option>  
		  <option <?php if($var->factor == 'Solicitudes de servicio'){echo "selected";} ?> value='Solicitudes de servicio'>Solicitudes de servicio</option>  
		  </select>
		  </div>
		  
		  <div class="labelNuevo">
		  <label for="valor_unitario">Valor unitario:</label>
          <label for="valor_unitario">Cantidad:</label>
		  </div>
		  <div class="inpustJuntos">
		  <input type="text" id="valor_unitario" OnKeyUp='Multiplicar()' name="valor_unitario" value='<?php echo $var->valor ?>'>
		  
		  <label>&nbsp;&nbsp;&nbsp;</label>
          <input type="text" id="cantidad" name="cantidad" OnKeyUp='Multiplicar()' value='<?php echo $var->cantidad ?>'>
		  </div>
		  	
		  <div class="labelNuevo">
		  <label for="valor_unitario">Valor total item:</label>
          <label for="proyecto_placa">Proyecto o placa:</label>
		  </div>
		  
		  <div class="inpustJuntos">
		  <input type="text" id="valor_total" name="valor_total" value='<?php 
		  $varRespuesta =  $var->valor * $var->cantidad;
		  //$var->valor_total
		  echo $varRespuesta ?>'>
		  <label>&nbsp;&nbsp;&nbsp;</label>
		  
		  
		  <select id="proyecto_placa" name="proyecto_placa">
		  <?php if($var->placa == null || $var->ntipo == ""){ ?><option selected value="0">NO DEFINIDO</option> <?php } ?>
		  <?php while($i=mysql_fetch_object($varQueryProPla)){ ?>
		  
		  <option <?php if($var->placa == $i->placa){echo "selected";} ?> value='<?php echo $i->id?>'><?php echo $i->placa ?></option>  
		  <?php } ?>
		  </select>
		  
		  
		  
		  
		  </div>
		  <script>
		  function Multiplicar(){
		let var1 = document.getElementById('valor_unitario').value;
		let var2 = document.getElementById('cantidad').value;
		
		let rest = parseInt(var1)* parseInt(var2);
		console.log(rest);
		document.forma.valor_total.value = rest;
	    }
		  </script>
		  
		  
          
		  
		  
		  <?php if($varEquals == 1){?>
		  
		  <label for="centro_op">Centro de operaciones:</label>
		  <select id="centrodeoperacion" name="centrodeoperacion">
		  <?php if($var->centrodeoperacion == null || $var->centrodeoperacion == ""){ ?><option selected value="0">NO DEFINIDO</option> <?php } ?>
		  <?php while($i=mysql_fetch_object($varQueryOpeCentro)){ ?>
		  
		  <option <?php if($var->centrodeoperacion == '$i->id'){echo "selected";} ?> value='<?php echo $i->id?>'><?php echo $i->nombre ?></option>  
		  <?php } ?>
		  </select>
		  
		  <label for="centro_cos">Centro de costos:</label>
		  
		  <select id="centrocosto" name="centrocosto">
		  <?php if($var->centrocosto == null || $var->centrocosto == ""){ ?><option selected value="0">NO DEFINIDO</option> <?php } ?>
		  <?php while($i=mysql_fetch_object($varQueryCostos)){ ?>
		  <option <?php if($var->centrocosto == $i->codigo){echo "selected";} ?> value='<?php echo $i->codigo?>'><?php echo $i->nombre ?></option>  
		  <?php } ?>
		  </select>
		  
		  <?php }?>
		  
		  
		  
		  
		  <label for="descripcion">Descripcion:</label>
          
		  <textarea   id="descripcion" name="descripcion" value=''><?php echo $var->observaciones ?></textarea >
		  
        </fieldset>
        
        <fieldset>
          <legend><span class="number">2</span>Pulsa el boton para modificar items</legend>
        </fieldset>
        
        <button type="submit" >Modificar</button>
		
		<input type='hidden' name='Acc' value='modificar_item_ok'>
		<input type='hidden' name='idItem' value='<?php echo $var->idItem ?>'>
		<input type='hidden' name='varEquals' value='<?php echo $varEquals ?>'>
		
		
		
		
      </form>
	  <script>
	$("#tipoRequi").select2();
	$("#proyecto_placa").select2();
	$("#centrodeoperacion").select2();
	$("#centrocosto").select2();
	
	
	  </script>
   
    </body>
</html>