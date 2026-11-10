<?php 
@session_start();
if (@$_SESSION['id'] == ""){
	echo '<script>window.location="../"</script>';
	exit();
}


if (@$_SESSION['aut_token_SaL1P'] != "25tNX1L1MSaL1P") {
	echo '<script>window.location="../"</script>';
	exit();
}
 ?>
