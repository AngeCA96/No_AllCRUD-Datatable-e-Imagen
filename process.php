<?php
    
    session_start();

    $mysqli = new mysqli('localhost', 'root', '', 'ejemplo') or die (mysql_error($mysqli));

    $id = 0;
    $update = false;
    $name = '';
    $location = '';
    $ruta = '';

    if (isset($_POST['save'])){
        $name = $_POST['name'];
        $location = $_POST['location'];
        
        $nombre_imagen = $_FILES['ruta']['name'];
        $tipo = $_FILES['ruta']['type'];
        $tamano = $_FILES['ruta']['size'];
        
        if(!empty($nombre_imagen) && ($_FILES['ruta']['size'] <= 20000000)){
            if(($_FILES['ruta']['type'] == "image/jpeg")
            || ($_FILES['ruta']['type'] == "image/jpg")
            || ($_FILES['ruta']['type'] == "image/png")) {
                
                $carpeta='multidata';
                $nuevo_nombre = uniqid().$nombre_imagen;//el uniqid() me sirve para agregar un id único a la imagen evitando así errores por tener el mismo nombre del archivo
                $ruta = $carpeta.'/'.$nuevo_nombre; 
                move_uploaded_file($_FILES['ruta']['tmp_name'], $ruta);
                
                $mysqli->query("INSERT INTO data (name, location, ruta) VALUES ('$name', '$location', '$ruta')") or die($mysqli->error);
                
                $_SESSION['message'] = "Record has been saved!";
                $_SESSION['msg_type'] = "success";
                
            } else {
                //echo "No se puede subir esta imagen porque no cumple con el formato";
                $_SESSION['message'] = "Wrong image type!";
                $_SESSION['msg_type'] = "warning";
            }
        } else {
            
            if($nombre_imagen == !NULL){
                //echo "La imagen es demasiado grande";
                $_SESSION['message'] = "Wrong image size!";
                $_SESSION['msg_type'] = "warning";
            }
            
        }
        
        /*
        $nombre_imagen = $_FILES['ruta']['name'];
        $temporal = $_FILES['ruta']['tmp_name'];
        
        $carpeta='multidata';
        $ruta = $carpeta.'/'.uniqid().$nombre_imagen; //el uniqid() me sirve para agregar un id único a la imagen evitando así errores por tener el mismo nombre del archivo
        move_uploaded_file($temporal, $ruta);
        
        $mysqli->query("INSERT INTO data (name, location, ruta) VALUES ('$name', '$location', '$ruta')") or die($mysqli->error);
        
        $_SESSION['message'] = "Record has been saved!";
        $_SESSION['msg_type'] = "success";
        */
        
        header("location: index.php");
    }

    if (isset($_GET['delete'])){
        $id = $_GET['delete'];
        
        $mysqli->query("DELETE FROM data WHERE id=$id") or die ($mysqli->error);
        unlink($ruta);
        
        $_SESSION['message'] = "Record has been deleted!";
        $_SESSION['msg_type'] = "danger";
        
        header("location: index.php");
    }

    if (isset($_GET['edit'])){
        $id = $_GET['edit'];
        $update = true;
        $result = $mysqli->query("SELECT * FROM data WHERE id=$id") or die ($mysqli->error());
        
        if (count(array($result)) == 1){
            $row = $result->fetch_array();
            $name = $row['name'];
            $location = $row['location'];
            $ruta = $row['ruta'];
            // echo "<img class='img-thumbnail' width='60' src='" . $row['ruta'] . "'>" ;
            
        }
    }

    if (isset($_POST['update'])){
        $id = $_POST['id'];
        $name = $_POST['name'];
        $location = $_POST['location'];
        
        $mysqli->query("UPDATE data SET name='$name', location='$location', ruta='$ruta' WHERE id='$id'") or die ($mysqli->error);
        
        $_SESSION['message'] = "Record has been updated!";
        $_SESSION['msg_type'] = "warning";
        
        header("location: index.php");
    }
?>
