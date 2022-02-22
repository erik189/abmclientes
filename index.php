<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// paso 4 leer si el archivo existe y almacenar el contenido en una variable
if(file_exists("archivo.txt")){  //si el archivo existe
    $contenido = file_get_contents("archivo.txt");//leo el archivo
    //convertir
    $data = json_decode($contenido,true);//json_decode trasnforma de string a array asociativo
    


}//sino generar el array

else{
    $data = array();
                     //una matriz u objeto PHP en una representación JSON.
}
//print_r($data);
//si quiero ver en la url una query string uso el metodo Get,puedo indicar en la query de la url lo que quieor ver
if (isset($_GET["id"])){
$id = $_GET["id"];
}
else{
    $id = "";

}
//si es eliminar
if(isset($_GET["do"]) && $_GET["do"] == "eliminar"){
    //elimino la poscicion data $data[$id]
    //print_r("ENTRA");
    if(file_exists("imagenes/".$data[$id]["imagen"])){
        unlink ("imagenes/".$data[$id]["imagen"]);
        }


    unset ($data[$id]);
     //convertir el array en json
    $clientesJson = json_encode($data);
    //Actualizar el nuevo archivo con el array de clientes.
    file_put_contents("archivo.txt", $clientesJson);

    header("location:index.php");

}
  

//print_r($data[$id]["dni"]);

//paso 2 tomar los datos
if($_POST){//validar un formulario con el metodo post
    $dni = trim($_POST["txtDni"]);//captura los datos del metodo post y los almacena en la variable dni.
    $nombre = trim($_POST["txtNombre"]);
    $telefono = trim($_POST["txtTelefono"]);
    $correo= trim($_POST["txtCorreo"]);

    //subir un archivo:si viene una imagen adjunta lo guardo
    if($_FILES["archivo"]["error"] == UPLOAD_ERR_OK){
        //ultimo paso de actualizar imagen,si tengo una y cambio por otra imagen elimino la anterior.
        if(isset($data[$id]["imagen"]) && $data[$id]["imagen"] != ""){
            if(file_exists("imagenes/".$data[$id]["imagen"])){
                unlink ("imagenes/".$data[$id]["imagen"]);
                }

        }

        $nombreAleatorio = date("Ymdhmsi");
        $archivo_tmp= $_FILES["archivo"]["tmp_name"];
        $nombreArchivo= $_FILES["archivo"]["name"];
        $extension= pathinfo($nombreArchivo,PATHINFO_EXTENSION);
        $imagen ="$nombreAleatorio.$extension";

        //validamos las extensiones
        if($extension == "png" || $extension == "jpg" || $extension == "jpeg"){
        move_uploaded_file($archivo_tmp,"imagenes/$imagen");
          
           }   
        }
        //sino imagen es vacio
        //si viene id entonces estoy actualizando
        else{
            if($id>=0)
             $imagen= $data[$id]["imagen"];
             else{
             $imagen="";
            }
        }

    //paso 1: crear un array con todos los datos(antes de hacer el if para ver si viene un id)

    if($id >= 0){//si viene un id actualizo
    $data[$id] = array("dni" => $dni,
    "nombre" => $nombre,
    "telefono" => $telefono,
    "correo" => $correo,
    "imagen" => $imagen );
}
else{
//sino es un nuevo cliente
    $data[] = array("dni" => $dni,
                        "nombre" => $nombre,
                        "telefono" => $telefono,
                        "correo" => $correo,
                        "imagen" => $imagen );
}

    //paso 3 comvertir el array a jason
      $clientesJson = json_encode($data);
      //almacenar el json en un archivo
      file_put_contents("archivo.txt",$clientesJson);


    
    }
    
   

?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"> 
        <script src="https://kit.fontawesome.com/40e341f8f7.js" crossorigin="anonymous"></script>
        <link rel="stylesheet"  href="css/estilos.css">
        <title>clientes</title>
    </head>
    
    <body>
    <main class="container">
        <div class="row">
    <div class="col-12  my-5 text-center">
        <h1>Registro de Clientes</h1>
    </div>
    </div>
        <div class="row">
            <div class="col-6">
                <form  action="" method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="">DNI: *</label>
                        <input type="text" name="txtDni" id="txtDni" class="form-control" required value = "<?php echo isset($data[$id]["dni"])? $data[$id]["dni"] :"";?>" ><?php//aca mostramos datos si setemos en el query de la url por id?>
                    </div>
                    <div>
                        <label for="">Nombre: *</label>
                        <input type="text" name="txtNombre" id="txtNombre" class="form-control" value = "<?php echo isset($data[$id]["nombre"])? $data[$id]["nombre"] :"";?>">
                    </div>
                    <div>
                        <label for="">Telefono:</label>
                        <input type="text" name="txtTelefono" id="txtTelefono" class="form-control" value = "<?php echo isset($data[$id]["telefono"])? $data[$id]["telefono"] :"";?>" >
                    </div>
                    <div>
                        <label for="">Correo: *</label>
                        <input type="text" name="txtCorreo" id="txtCorreo" class="form-control" required value = "<?php echo isset($data[$id]["correo"])? $data[$id]["correo"] :"";?>">
                    </div>
                    <div>
                        <label for="txtArchivo"> Archivo adjunto:</label>
                      
                    <input type="file" name="archivo" id="archivo"  aceppt=".jpg, .png, .jpeg"><br>
                    <small class="d-block">archivos admitidos:.jpg,.jpeg,.png</small>
                    </div>
                    
                    <div>

                        <button type="submit" class="btn btn-success my-3">Guardar</button>
                        <a href="index.php" class="btn btn-danger my-2"> NUEVO</a> 
                    </div>
                
                    
                </form>
                </div>

                <div class="col-6">
                    
                <table class="table table-hover border">
                <tr class="color">    
                <th>
                    IMAGEN
                </th>
                <th>
                    DNI
                </th>
                <th>
                    NOMBRE
                </th>
                <th>
                    CORREO
                </th>
                <th>
                    ACCIONES
                </th>
                </tr>
               
               <?php foreach( $data as $pos => $cliente): ?> 
               <tr class= ctext>
               <td><img src="imagenes/<?php echo $cliente["imagen"];?>" class="img-thumbnail"></td>
               <td> <?php  echo $cliente["dni"];?></td> 
               <td> <?php  echo $cliente["nombre"];?></td> 
               <td> <?php  echo $cliente["correo"];?></td> 
                <td>
                <a href="?id=<?php echo $pos; ?>"><i class="fas fa-edit"></i></a>

                </td>
                <td>
                <a href="?id=<?php echo $pos; ?>&do=eliminar"><i class="fas fa-trash-alt"></i></a>
                </td>
               </tr>
               
               <?php endforeach; ?>
            </table>  
          </div>    
        </div>
     </main>
</body>
</html>        
           
            
                    
                
           