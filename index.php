
<?php
   $driver= 'mysql';
   $host = '127.0.0.1';
   $db_name   = 'iteh2lb1var4';
   $user = 'root';
   $pass = 'root';
   $charset = 'utf8';
   $options = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION];


   try{
   	$pdo = new PDO("$driver:host=$host;dbname=$db_name;charset=$charset",$user,$pass,$options);

   }
catch(PDOException $e)
{
	die("Не удалось подключится к базе данных");	
}

$result= $pdo-> query('SELECT * FROM nurse');

echo"<h3>Получение данных о смене медсестры:</h3>
    <form method='post'  >
     <select name = 'nurse'>";
while($namenurs = $result->fetch(PDO::FETCH_ASSOC)) {    
    echo '<option value="'.$namenurs['id_nurse'].'" >'.$namenurs['name'].' </option>';
    }
echo "<input type='submit'></input>";
echo "</select></form>";

    $rn = (int)$_POST['nurse'];
    if($rn!=null){      
    $nurseward=$pdo->query ("SELECT DISTINCT W.name, N.name
    FROM nurse N, ward W , nurse_ward NW
    WHERE W.id_ward= NW.fid_ward AND N.id_nurse = NW.fid_nurse AND N.id_nurse=$rn"); 
    $outnurseward=$nurseward->fetchAll(PDO::FETCH_COLUMN);

    $nn=$pdo->query("SELECT N.name FROM nurse n WHERE N.id_nurse=$rn");
    $outnursename=$nn->fetch(PDO::FETCH_ASSOC);     
       
    echo"<table>
         <tr>
            <th>Имя медсестры</th>
            <th>Палаты</th>
         </tr>
         <tr><td> $outnursename[name]</td>";
        
    for($i=0;$i<count($outnurseward);$i++){
     echo" <td>$outnurseward[$i]</td> ";
    }
    echo"</tr></table>";   
}
/*------------ медсестры выбранного отделения; ----------*/

$dp= $pdo-> query("SELECT department FROM nurse");

echo"<h3>медсестры выбранного отделения:</h3>
    <form method='post'  >
     <select name = 'department'>";
while($departments = $dp->fetch(PDO::FETCH_ASSOC)) {    
    echo '<option value="'.$departments['department'].'" >'.$departments['department'].' </option>';
    }
echo "<input type='submit'></input>";
echo "</select></form>";


    $rd = (int)$_POST['department'];
    if($rd!=null){      
    $ndp=$pdo->query("SELECT name FROM nurse WHERE department= $rd"); 
    $outnurseOfDep=$ndp->fetchAll(PDO::FETCH_COLUMN);        
    echo"<table>
         <tr>
            <th>Отделение</th>
            <th>Медсестры</th>
         </tr>
         <tr><td> $rd</td>";
     
    
     for($i=0;$i<=count($outnurseOfDep);$i++){                   
     echo" <td>$outnurseOfDep[$i]</td> ";    
    }
    echo"</tr></table>";    
    }

/* дежурства (в любых палатах) в указанную смену.*/
$sh= $pdo-> query("SELECT shift FROM nurse");

echo"<h3>Дежурства (в любых палатах) в указанную смену:</h3>
    <form method='post'  >
     <select name = 'shift'>";
while($shifts = $sh->fetch(PDO::FETCH_ASSOC)) {    
    echo '<option value="'.$shifts['shift'].'" >'.$shifts['shift'].' </option>';
    }
echo "<input type='submit'></input>";
echo "</select></form>";

    $rs = $_POST['shift'];
    if($rs!=null){        
    $ndp=$pdo->query ("SELECT DISTINCT N.shift, W.name, N.date FROM nurse N, ward W, nurse_ward NW
                        WHERE W.id_ward= NW.fid_ward AND N.id_nurse = NW.fid_nurse AND N.shift='$rs'");          
     echo"<table>
         <tr>
            <th>сменa</th>
            <th>палата/дата</th>            
                        
         </tr>
        <tbody>
         <tr><td rowspan=5> $rs </td>";     
    
     while($outnurseOfShift=$ndp->fetch(PDO::FETCH_BOTH)){                   
     echo" <tr><td>$outnurseOfShift[name] 
      $outnurseOfShift[date]</td></tr> ";    
    }
    echo"</tr></tbody></table>";
    }


   /*  добавления новых медсестер */
    echo"<h3>Добавление медсестры</h3>
    <form name='addnurse' method='post'>
    <input name=namenurse placeholder='Введите фамилию медсестры'></input>
    <p></p>
    <input name= date type='date' ></input><p></p>
    <select name = 'departmentinp'>";    
    $dpinp= $pdo-> query("SELECT department FROM nurse");
while($departmentsinp = $dpinp->fetch(PDO::FETCH_ASSOC)) {    
    echo '<option value="'.$departmentsinp['department'].'" >'.$departmentsinp['department'].' </option>';
    }
    echo"</select><p></p>";
    $shinp= $pdo-> query("SELECT shift FROM nurse");
    echo"<select name = 'shiftinp'>";
while($shiftsinp = $shinp->fetch(PDO::FETCH_ASSOC)) {    
    echo '<option value="'.$shiftsinp['shift'].'" >'.$shiftsinp['shift'].' </option>';
    }
    echo"<input type='submit' value='добавить' ></input>    
    </form> ";
    $mid=$pdo->query("SELECT MAX(id_nurse) FROM nurse");
    $maxid=(int)$mid->fetch(PDO::FETCH_COLUMN);
    $newid=$maxid+1;
    $data=[
        'name'=>$_POST['namenurse'],
        'date'=>$_POST['date'],
        'dep'=>$_POST['departmentinp'],
        'shift'=>$_POST['shiftinp'],
        'id'=>$newid
    ];    
     if($data['name']!=null && $data['date']!=null){        
    $show=$pdo->exec("INSERT INTO `nurse` (`id_nurse`, `name`, `date`, `department`, `shift`) 
                     VALUES ('$data[id]', '$data[name]', '$data[date]', '$data[dep]', '$data[shift]')"); 
    echo "<span style='color:blue;'>Вы добавили медсестру с параметрами: фамилия: $data[name], смена: $data[shift], отделение: $data[dep], дата: $data[date]</span>";    
    }
    
    
    /* добавления новых палат  */

     echo"<h3>Добавление палаты</h3>
    <form name='addward' method='post'>
    <input name='wardname' placeholder='Введите имя палаты'></input><p></p>
    <input type='submit' value='добавить' ></input>";
    
    $wid=$pdo->query("SELECT MAX(id_ward) FROM ward");
    $wrid=(int)$wid->fetch(PDO::FETCH_COLUMN);
    $wardid=$wrid+1;
    $nameward=$_POST['wardname'];         
    if($nameward!=null){        
    $show=$pdo->exec("INSERT INTO `ward` (`id_ward`, `name`) VALUES ('$wardid', '$nameward')"); 
    echo "<p><span style='color:blue;'>Вы добавили палату: $nameward</span></p>";    
    }
   
    
    /* назначение выбранной медсестры в указанную палату */
    $resultN = $pdo-> query('SELECT id_nurse, name FROM nurse');
    echo"<h3>Hазначение выбранной медсестры в указанную палату:</h3>
    <form method='post'  >
    <p>выберите медсестру 
     <select name = 'assingnurse'></p>";
    while($namenurs = $resultN->fetch(PDO::FETCH_ASSOC)) {    
    echo '<option value="'.$namenurs['id_nurse'].'" >'.$namenurs['name'].' </option>';
    }
    echo"</select><p>выберите палату <select name = 'assingward'>";
    $resultW = $pdo->query('SELECT * FROM ward');
    while($nameward=$resultW->fetch(PDO::FETCH_ASSOC)){
        echo'<option value="'.$nameward['id_ward'].'" >'.$nameward['name'].' </option>';
    }
echo "</p><input type='submit' value='назначить'></input></form>";
    $nurs=$_POST['assingnurse'];
    $ward=$_POST['assingward'];
    if($nurs!=null&&$ward!=null){        
        $show=$pdo->exec("INSERT INTO nurse_ward(fid_nurse,fid_ward) VALUES ($nurs,$ward)"); 
        echo "<p><span style='color:blue;'>Операция выполненна успешно</span></p>";    
        }

?>