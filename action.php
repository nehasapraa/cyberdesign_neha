<?php
	// DEVELOPER :- SIPL 
	// PURPOSE :- ACTION PAGE
	// DATE :- 4 sep 2012

	ob_start();
	session_start();
	include('../includes/config.php');
	include('../classes/database.php');
	require_once '../classes/class.phpmailer.php';
	
	// Set the database object 
	$db = new Database();  
	$db->set_db_host(DATABASE_HOST);
	$db->set_db_user(DATABASE_USERNAME);
	$db->set_db_pass(DATABASE_PASSWORD);
	$db->set_db_name(DATABASE_NAME);
	
	$db->connect();
	
	$actiontype = isset($_REQUEST['actiontype'])?$_REQUEST['actiontype']:'';

	
	// check admin 
	if(!isset($_SESSION['admin_id']) && !preg_match('/index.php/',$_SERVER['SCRIPT_FILENAME'])){

		switch($actiontype){
			case 'adminaction':
			$action  = isset($_REQUEST['action'])?$_REQUEST['action']:'';
			$email = isset($_REQUEST['email'])?$_REQUEST['email']:'';
			
			switch($action){
				// forgot password
				case 'forgot_password':
					$where = 'email = "'.$email.'" and type= "admin" and isactive = 1 and isdelete = 0 ' ;
					$row = ' id , username ';
					$res = $db->setResult();
					$db->select('users', $row , $where, '');
					$res = $db->getResult();
					if(count($res) == 1){
						$password = $res[0]['username'].rand(654789,987654);  

						$update = array('password'=> md5($password));
						$where = array('email'=>$email , "type" => "admin" , "isactive" => 1 ,"isdelete" => 0  );
						if($db->update('users', $update, $where)){
							
							//$message = 'Hello '. $res[0]['username'].'<br/><br/>You forgot your password, but fear not. We have reset your password . <br/><br/> Your password is - <b>'.$password.'</b><br/><br/>-- <br/>Thanks';
							$message = 'Hello '. $res[0]['username'].'<br/><br/>We have reset your password . <br/><br/> Your password is - <b>'.$password.'</b><br/><br/>-- <br/>Thanks';
							
							$mail = new PHPMailer(true);
							$mail->AddReplyTo('admin@sipl.com','SIPL');
							$mail->AddAddress($email ,$res[0]['username']);
							$mail->SetFrom('from@sipl.com', 'sipl');
							$mail->Subject = 'Password Reset ';
							$mail->MsgHTML($message);
							if($mail->Send()){
								
								echo ' Password has been reset . Please check your email . ';
							}else{
								echo ' Please Try again later . ';
							}
						}
					}
					die('');			
				break;
			}
			
		break;
		}
		
		header('location: index.php');
	}


	switch($actiontype){
		
		// for admin action 
		
		
		// for admin login 
		case 'adminlogin':
			$username = isset($_REQUEST['username'])?trim($_REQUEST['username']):'';
			$password = isset($_REQUEST['password'])?$_REQUEST['password']:'';
			
			$where = 'username = "'.$username.'" and password = "'.md5($password).'" and type = "admin" and isactive = 1 and isdelete = 0 ' ;
			$row = 'id , username, email, password, lastlogin';
			$db->select('users', $row , $where, '');
			$res = $db->getResult();
			
			if(!empty($res)){
				if(isset($res[0]['id'])){
					$_SESSION['admin_id'] = $res[0]['id'];
					$_SESSION['admin_username'] = $res[0]['username'];
					$_SESSION['admin_email'] = $res[0]['email'];
					$_SESSION['admin_lastlogin'] = $res[0]['lastlogin'];
					$_SESSION['admin_currentlogin_time'] = date('Y-m-d H:i:s');
					$_SESSION['error_message'] = 'Login successfully ';	
					header('location:home.php');
				}else{
					$_SESSION['error_message'] = 'Username / Password incorrect !!';	
					header('location:index.php');
				}
			}else{
				$_SESSION['error_message'] = 'Username / Password incorrect !!';	
				header('location:index.php');
			}
		break;
		
		// For logout features of the admin 
		case 'logout':
			$db = new Database();  
			$db->set_db_host(DATABASE_HOST);
			$db->set_db_user(DATABASE_USERNAME);
			$db->set_db_pass(DATABASE_PASSWORD);
			$db->set_db_name(DATABASE_NAME);
			$db->connect();

			$update = array('lastlogin'=> $_SESSION['admin_currentlogin_time']);
			$where = array('id'=>$_SESSION['admin_id']);
			$db->update('users', $update, $where);

			$db->disconnect();

			session_destroy();
			header('location:index.php?action=logout');
		break;
		
		// For massmail features of the admin section 
		case 'adminmassmail':
		
			$subject = isset($_REQUEST['subject'])?$_REQUEST['subject']:'';
			$message = isset($_REQUEST['message'])?$_REQUEST['message']:'';
			
			if($subject != '' && $message != ''){

				$Values = array($_SESSION['admin_id'],4 , $subject , $message  , 1 ,date('Y-m-d H:i:s') );
				$Rows = 'userid , templatetypeid, subject , message , isactive, createddate';
				$InsertResult = $db->insert('templates', $Values , $Rows);  
				$LastInsertTemplateId =  $db->getMysqlInsertId();
				
				if($InsertResult){
					$MysqlInsertId =  $db->getMysqlInsertId();
					//$_SESSION['error_message'] = ' Data has been sent ';

					$Values = array($_SESSION['admin_id'], 0, $LastInsertTemplateId, 'admin', $subject, $_REQUEST['formname'], $_REQUEST['replyto'] , 'now',  date('Y-m-d H:i:s'),  1 , 0 , date('Y-m-d H:i:s') );
					$Rows = ' userid , listid, templateid , name , subject, formname , replyto ,schedulingtype , schedulingtime , isactive ,isdeleted, createddate';
					$InsertResult = $db->insert('contents', $Values , $Rows);  
					$LastInsertContentId =  $db->getMysqlInsertId();
					MassMail($LastInsertTemplateId , $LastInsertContentId , $db);
					$test  = ' Mail has been sent to all member ......';
				}else{
					$test  = ' Error occuring try again later .. ';
				}
			}else{
				$test  = ' Something missing ......';
			}
			echo $test  ;
			die('');
		break;
		
		// for change the password 
		case 'password':
			$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
			
			switch ($action){
				
			}
		break ;
		
		case 'setting' :
		// For setting action of the admin 
			$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
			$id = isset($_SESSION['admin_id'])?$_SESSION['admin_id']:0;
			$password = isset($_REQUEST['password'])?trim($_REQUEST['password']):'';

			if(!$db->select('users','id', ' type ="admin" and id = '.$id , ' id LIMIT 1 ')){
				$_SESSION['error_message'] = 'Access denied ...';
				die();
			}

			// for set username 
			switch ($action){
				
				case 'oldpassword':
				// check old password 
					if($db->select('users',' password ', ' type ="admin" and password ="'.md5($password).'" and id = '.$id , ' id LIMIT 1 ')){
						$db->setResult();
						$db->select('users',' password ', ' type ="admin" and password ="'.md5($password).'" and id = '.$id , ' id LIMIT 1 ');
						$res =  $db->getResult();
						echo count($res);
					}else{
						echo '0';
					}
					die(); 
				break;

				case 'update_password':
				// update password 
					$npassword = isset($_REQUEST['npassword'])?md5(trim($_REQUEST['npassword'])):'';
					if($db->update('users',array('password'=>$npassword),'id = '.$_SESSION['admin_id'])){
						echo 'Password has been Updated...';	
					}else{
						echo 'Error while updating password';	
					}
					die();
				break;				
				
				case 'username':
				// check username 
					$username = isset($_REQUEST['username'])?$_REQUEST['username']:'';
					$db->setResult();
					if($db->select('users',' username ', ' type ="admin" and username ="'.$username.'" and id = '.$id, ' id LIMIT 1 ')){
						echo count($db->getResult());
					}else{
						echo -1;
					}
					die();
				break ;
				
				case 'email':
				// check email address of the admin 
					$email = isset($_REQUEST['email'])?$_REQUEST['email']:'';
					$db->setResult();
					if($db->select('users',' email ', ' type ="admin" and email ="'.$email.'" and id = '.$id, ' id LIMIT 1 ')){
						echo count($db->getResult());
					}else{
						echo -1;
					}
					die();
				break ;
				
				case 'update_generalsetting':
				// update general setting 
					$username = isset($_REQUEST['username'])?$_REQUEST['username']:'';
					$email = isset($_REQUEST['email'])?$_REQUEST['email']:'';
					$db->setResult();
					
					$update = array('username'=> $username , "email" => $email );
					$where = array('id'=>$_SESSION['admin_id'] , "type" => 'admin');
					if($db->update('users', $update, $where)){
						echo 'Data Update successfully';
						$_SESSION['error_message'] = 'Data Has Been Updated successfully..';
					}else{
						echo 'Try again later.';
						$_SESSION['error_message'] = ' Error occurred , Plz try again later .. ';
					}
					die();
				break ;
			}
		break ;
		
		// For edit newsletter features of the admin section 
		case 'editnewsletter':
			$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
			$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
			if( isset($id) && $id != 0 ){
				
				$db->setResult();
				$where = ' userid = '.$_SESSION['admin_id'].' and id ='.$id;
				$db->select('templates', 'id' , $where, 'id');
				$check_template = $db->getResult();
				if(isset($check_template[0]['id'])){
					switch($type){
						case 'edit':
							header('location: newsletter.php?action=edit&nid='.$id);
							die();					
						break;
						case 'inactive':
							if($db->update('templates',array('isactive'=>'0'),'id = '.$id)){
								$_SESSION['error_message']=  'Data update successfully ';
							}else{
								$_SESSION['error_message']=  'Something went wrong try again later....';
							}
						break;
						case 'active':
							if($db->update('templates',array('isactive'=>'1'),'id = '.$id)){
								$_SESSION['error_message']=  'Data update successfully ';
							}else{
								$_SESSION['error_message']=  'Something went wrong try again later....';
							}
						break;
						case 'delete':
							if($db->update('templates',array('isdelete'=>'1'),'id = '.$id)){
								$_SESSION['error_message']=  'Data delete successfully ';
							}else{
								$_SESSION['error_message']=  'Something went wrong try again later....';
							}
						break;
					}
				}else{
					$_SESSION['error_message']=  'Invalid User ....';
				}
				header('location: newsletter.php?action=list');
			}
		break;
		// For add and update features of the admin section 
		case 'newsletter':
			$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
			$subject = isset($_REQUEST['subject'])?$_REQUEST['subject']:'';
			$message = isset($_REQUEST['message'])?$_REQUEST['message']:'';
			$tid = isset($_REQUEST['tid'])?$_REQUEST['tid']:0;
			
			switch($action){
				// For add newsletter
				case 'add':
					if($subject != '' && $message != ''){
						
						$Values = array($_SESSION['admin_id'],2 , $subject , $message  , 1 ,date('Y-m-d H:i:s'));
						$Rows = 'userid , templatetypeid, subject , message , isactive, createddate';
						$InsertResult = $db->insert('templates', $Values , $Rows);  
						$LastInsertTemplateId =  $db->getMysqlInsertId();
						
						if($InsertResult){
							$MysqlInsertId =  $db->getMysqlInsertId();
							$_SESSION['error_message'] = ' Data has been sent ';
							$Values = array($_SESSION['admin_id'], 0, $LastInsertTemplateId, 'admin', $subject, $_REQUEST['formname'], $_REQUEST['replyto'] , 'now',  date('Y-m-d H:i:s'),  1 , 0 , date('Y-m-d H:i:s') );
							$Rows = ' userid , listid, templateid , name , subject, formname , replyto ,schedulingtype , schedulingtime , isactive ,isdeleted, createddate';
							$InsertResult = $db->insert('contents', $Values , $Rows);  
							$LastInsertContentId =  $db->getMysqlInsertId();
							//MassMail($LastInsertTemplateId , $LastInsertContentId , $db);
						}else{
							$_SESSION['error_message'] = ' Error occuring try again later .. ';
						}
					}else{
						$_SESSION['error_message'] = ' Something missing ......';
					}
					header('location: newsletter.php');		
				break;
				// For update the newsletter
				case 'update':
					if($tid != 0 && $subject != '' && $message != ''){
						$update_data = array('subject'=>$subject , 'message'=>$message);
						if($db->update('templates',$update_data,'id = '.$tid)){
							$_SESSION['error_message'] = 'Data Has Been Update successfully..';
							header('location: newsletter.php?action=list');
						}else{
							$_SESSION['error_message'] = 'Error in database connection try again later..';
							header('location: newsletter.php?action=list');
						}
					}else{
							$_SESSION['error_message'] = 'Data Missing ';
					}
					header('location: newsletter.php?action=list');
				break;
			}
		break ;

		case 'member':
		// operation for member section only 
			$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
			$mid = isset($_REQUEST['mid'])?$_REQUEST['mid']:0;
			$username = isset($_REQUEST['username'])?trim($_REQUEST['username']):'';
			$email = isset($_REQUEST['email'])?trim($_REQUEST['email']):'';
			$password= isset($_REQUEST['password'])?$_REQUEST['password']:'';
			
			
			if( ($action == 'edit' || $action == 'active'  || $action == 'inactive' || $action == 'delete')){
				if(!$db->select('users','id', 'id = '.$mid , ' id LIMIT 1 ')){
					$_SESSION['error_message'] = 'Invalid user Operation......';
					header('location: members.php');
					die();
				}
			}

			if($action == 'edit' && ($username == "" || $email == "") ){
				if($username == "" && $email == "")
					$_SESSION['error_message'] = 'username and email should not be empty ';
				elseif($username == "")
					$_SESSION['error_message'] = 'username should not be empty ';
				elseif($email == "")
					$_SESSION['error_message'] = 'email should not be empty ';

				header('location: '. $_SERVER['HTTP_REFERER']);
				die();
			}
		
			switch($action){
				case 'add':
				// add member 
					$Values = array($username,$email, md5($password), 'user' , 1 ,date('Y-m-d H:i:s'));
					$Rows = 'username , email , password , type , isactive, createddate';
					$InsertResult = $db->insert('users', $Values , $Rows);  
					if($InsertResult){
						$_SESSION['error_message'] = 'Users has been saved......';
					}else{
						$_SESSION['error_message'] = 'Something went wrong......';
					}
				break;
				case 'edit':
				// edit memeber 
					$data = array('username' => $username , 'email' => $email ); 
					if($db->update('users' , $data ,'id = '.$mid)){
						$_SESSION['error_message']=  'Data has been updated successfully.....';
					}else{
						$_SESSION['error_message']=  'Something went wrong try again later....';
					}
					
				break;
				case 'delete':
				// delete member
					if($db->update('users',array('isdelete'=>'1'),'id = '.$mid)){
						$_SESSION['error_message']=  'Data has been updated successfully.....';
					}else{
						$_SESSION['error_message']=  'Something went wrong try again later....';
					}
				break;
				case 'active':
				//active member 
					if($db->update('users',array('isactive'=>'1'),'id = '.$mid)){
						$_SESSION['error_message']=  'Data has been updated successfully.....';
					}else{
						$_SESSION['error_message']=  'Something went wrong try again later....';
					}
				
				break;
				case 'inactive':
				// inactive member 
					if($db->update('users',array('isactive'=>'0'),'id = '.$mid)){
						$_SESSION['error_message']=  'Data has been updated successfully.....';
					}else{
						$_SESSION['error_message']=  'Something went wrong try again later....';
					}
				break;
				default:
				// default operation 
						$_SESSION['error_message']=  'Invalid operation....';
				break;
			}
			header('location: members.php');
		break;

		default:
			header('location:index.php');
		break;
	}
	
// function start from here
	function MassMail($TemplateId , $ContentId , $db){
		
		// get content from the database
		$where = 'id = '.$TemplateId.' ' ;
		$row = ' subject , message ';
		$db->setResult();
		$db->select('templates', $row , $where, '');
		$res = $db->getResult();
		$subject = isset($res[0]['subject'])? $res[0]['subject'] :'';
		$message = isset($res[0]['message'])?$res[0]['message']:'';
		
		// get user from database 
		$where = ' isactive = 1 and isdelete = 0 and type = "user" ' ;
		$row = ' id , email ';
		$db->setResult();
		$db->select('users', $row , $where, '');
		$res = $db->getResult();
		
		foreach($res as $r) {
			$mail = new PHPMailer(true);
			$mail->AddReplyTo('admin@sipl.com','SIPL');
			$mail->AddAddress($r['email'],$r['username'] );
			$mail->SetFrom('from@sipl.com', 'sipl');
			$mail->Subject = $subject;
			$mail->MsgHTML($message);
			
			if($mail->Send()){
				$Values = array($_SESSION['admin_id'], $r['id'], 1 , $ContentId, date('Y-m-d H:i:s') );
				$Rows = ' fromid , toid, mailstatus , contentid , createddate ';
				$InsertResult = $db->insert('mailtracking', $Values , $Rows);
			}else{
				$Values = array($_SESSION['admin_id'], $r['id'], 0 , $ContentId, date('Y-m-d H:i:s') );
				$Rows = ' fromid , toid, mailstatus , contentid , createddate ';
				$InsertResult = $db->insert('mailtracking', $Values , $Rows);
			}

		}
	}
?>