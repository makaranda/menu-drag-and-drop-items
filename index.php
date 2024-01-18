<?php

	function connect(){
		$username = 'root';
		$password = '';
		$mysqlhost = 'localhost';
		$dbname = 'project_dragdropdb';
		
		$conn = new PDO("mysql:host=$mysqlhost;dbname=$dbname", $username, $password);
		if($conn){
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		}else{
			die('Can not connect database');
		}
	}
	
	$sql = connect();

	if(isset($_POST['project_ids'])){
		$ids = $_POST['project_ids'];
		$ids = explode(',',$ids);
		$names = $_POST['sub_menu_names'];
		$names = explode(',',$names);
		
		//var_dump($names);
		
		$i = 1;		
		foreach($ids as $projectid){
			$stmt = $sql->prepare('UPDATE `sub_menus` SET `priority`=? WHERE `id`=?');
			$stmt->bindParam(1,$i);
			$stmt->bindParam(2,$projectid);
			$stmt->execute();
			$i++;
		}
		
		$k = 1;		
		foreach($names as $names_val){
			$names_val = explode('/',$names_val);
			$names_id = $names_val[0];
			$names_value = $names_val[1];
			$stmt2 = $sql->prepare('UPDATE `sub_menus` SET `name`=:name WHERE `id`=:id');
			$stmt2->bindParam(':name', $names_value);
			$stmt2->bindParam(':id', $names_id);
			//$stmt2->bindParam(1,$names_value);
			//$stmt2->bindParam(2,$names_id);
			$stmt2->execute();
			//var_dump($names_value);
			$k++;
		}
	}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Drag and Drop Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
	  <style>
	  #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
	  #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto;cursor:pointer; }
	  #sortable li span { position: absolute; margin-left: -1.3em; }
	  .sortable{cursor:pointer;}
	  li.sub-cat.list-group-item.ui-state-default.ml-4.ui-sortable-handle {
			margin-left: 40px;
		}
	  </style>
	<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  </head>
  <body>
    
	<div class="container">
	
		<div class="row justify-content-start mt-5">
			<div class="col-12 col-md-6">
				<h1>Menu Drag and Drop Items</h1>
			</div>
			<hr>
			<div class="col-12 col-md-4">
<?php
			
?>			
				<ul class="list-group list-group-flush sortable" id="sortable1">
				<?php	
					$stmt = $sql->prepare('SELECT * FROM `main_menu` ORDER BY `priority` ASC');
					$stmt->execute();
					$menus = $stmt->fetchAll();
						
					foreach($menus as $menu){

						$menuNo = 1;
						
						$menu_id = $menu["id"];
						$menu_name = $menu["menu_name"];
						
						echo '<li data-id="'.$menu_id.'" class="main-cat list-group-item ui-state-default">';
						echo '<input type="text" class="form-control" menu-pro-id="'.$menu_id.'" value="'.$menu_name.'" />';
						
						$stmt44 = $sql->prepare('SELECT * FROM `sub_menus` WHERE `menu_id`=:id ORDER BY `priority` ASC');
						$stmt44->bindParam(':id', $menu_id);
						$stmt44->execute();
						$sub_menus = $stmt44->fetchAll();
						
						//echo '<ul class="list-group list-group-flush ml-4 sortable" id="sortable'.$menu_id.'">';	
						foreach($sub_menus as $sub_menu){
											
							$shortNo = 1;
							
					?>
						<li data-id="<?php echo $sub_menu['id'];?>" class="sub-cat list-group-item ui-state-default ml-4">
							<input type="text" class="form-control" pro-id="<?php echo $sub_menu['id'];?>" value="<?php echo $sub_menu['name'];?>" />
						</li>
					<?php
							
							
							$shortNo++;
						}
						/*
						echo '<script>
									  $( function() {
										$( "#sortable'.$menu_id.'" ).sortable();
									  } );
							</script>';
							*/
						//echo '</ul>';
						echo '</li>';
						$menuNo++;
					
					}
				?>	
				</ul>
			</div>
			<div class="col-12 col-md-12">
				<form action="" method="POST">
					<input type="hidden" name="project_ids" id="project_ids"/>
					<input type="hidden" name="sub_menu_names" id="sub_menu_names"/>
					<button type="submit" class="btn btn-sm btn-primary mt-4" id="savebtn">Update</button>
				</form>
			</div>
		</div>
	
	</div>
	
	
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
	  <script>
	  $( function() {
		//$( "#sortable" ).sortable();
		$( ".sortable" ).sortable();
	  } );
	  
	  $("#savebtn").click(function(){
		 var array = [];
		 $.each($('#sortable').find('li'),function(){
			array.push($(this).data('id')); 
		 });		 
		 $('#project_ids').val(array.toString());
		 
		 var array2 = [];
		 //alert($('input').attr('project-id').val());
		 $('#sortable input').each(function(){
			let name_id = ($(this).attr('pro-id'));
			array2.push(name_id+'/'+$(this).val()); 
		 });		 
		 $('#sub_menu_names').val(array2.toString());
	  });
	  </script>
	
  </body>
</html>