<?php
	/*
		Developer :- SIPL 
		Purpose :- members management at admin 
		date :- sep 5 2012 
	*/
	include('includes/header.php');
	include('../includes/config.php');
	include('../classes/database.php');
	
	$view = isset($_REQUEST['action'])?$_REQUEST['action']:'';
	if($view  == 'edit'){
		$mid = isset($_REQUEST['mid'])?$_REQUEST['mid']:'';
		$db = new Database();  
		$db->set_db_host(DATABASE_HOST);
		$db->set_db_user(DATABASE_USERNAME);
		$db->set_db_pass(DATABASE_PASSWORD);
		$db->set_db_name(DATABASE_NAME);

		$db->connect();
		$where = ' id = '.$mid.' and isdelete = 0 and type = "user" ';
		$db->setResult();
		if($db->select('users', ' id, username, email ' , $where, ' id LIMIT 1')){
			$total_result = $db->getResult();
			if(count($total_result)){
				$mid = $total_result[0]['id'];
				$username = $total_result[0]['username'];
				$email = $total_result[0]['email'];
			}else{
				$view = '';
				$_SESSION['error_message'] .= '<br/>something went wrong ';
			}
		}else{
			$view = '';
			$_SESSION['error_message'] .= '<br/>something went wrong ';
		}
	}
?>
    <div class="wrapper">
		
	
		<div class="blue_head"><h1>Member</h1></div>
    	<div class="main_menu">
	        <?php	include('includes/header_menu.php'); ?>
        </div>
        <div class="clear"></div>
        <?php if(isset($_SESSION['error_message'])){?>
            <div class="success_message">
                <?php echo $_SESSION['error_message']; session_unregister('error_message');?>
            </div>
        <?php }?>
        <span class="clr">&nbsp;</span>
            <div class="sub_links">
                <ul>
                    <li> <a href="members.php" <?php if($view == ''){echo 'class="selected"' ;}?>>List</a></li>
                    <li> <a href="members.php?action=add" <?php if($view == 'add'){echo 'class="selected"' ;}?>>Add</a></li>
                </ul>
            </div>
            <span class="clr">&nbsp;</span>
		<?php 
			switch($view){
				case 'add':
		?>
        	<script type="text/javascript">
            	$(document).ready(function(e) {
					$('#add_memeber_form').validate({
						rules: {
							username: {	minlength: 5,required: true},
							email: {required: true,email :true},
							password :{	minlength: 5,required: true},
							confirm_password :{required: true,equalTo : '#password'}
						},
						messages:{username : '*',email : '*',password : '*',confirm_password : '*'},
					});

            	});
            </script>
        		<div class="user_add_form">
                	<fieldset class="fieldset">
                    	<legend>Add</legend>
                	<form action="action.php" method="post" id="add_memeber_form">
                    	<input type="hidden" name="actiontype" value="member"/>
                    	<input type="hidden" name="action" value="add"/>
                        <table cellspacing="3" cellpadding="3" border="0" width="100%">
                        	<tr>
                            	<td width="20%"><label>Username :</label></td>
                            	<td><input type="text" name="username" id="Username" placeholder="Username "/></td>
                            </tr>
                        	<tr>
                            	<td width="20%"><label>Email :</label></td>
                            	<td><input type="text" name="email" placeholder="Email"/></td>
                            </tr>
                        	<tr>
                            	<td width="20%"><label>Password :</label></td>
                            	<td> <input type="password" name="password" id="password" placeholder="Password " title="Min 5 character"/></td>
                            </tr>
                        	<tr>
                            	<td width="20%"><label>Confirm Password :</label></th>
                            	<td> <input type="password" name="confirm_password" placeholder="Confirm Password"/></td>
                            </tr> 
                            <tr>
                            	<td width="20%">&nbsp;</td>
                            	<td>
                                	<input type="submit" value="Add Member" title="Add Member" name="add_member"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                    </fieldset>
                </div>
       		
        <?php		
				break;
				
				case 'edit':
		?>
        	<script type="text/javascript">
            	$(document).ready(function(e) {
					$('#update_memeber_form').validate({
						rules: {
							username: {minlength: 5,required: true},
							email: {required: true,	email :trues}
						},
						messages:{username : '*',email : '*'},
					});

            	});
            </script>
        		<div class="user_add_form">
                	<fieldset class="fieldset">
                    	<legend>Edit</legend>
                	<form action="action.php" method="post" id="update_memeber_form">
                    	<input type="hidden" name="actiontype" value="member"/>
                    	<input type="hidden" name="action" value="edit"/>
                    	<input type="hidden" name="mid" value="<?php echo $mid;?>"/>
                        <table cellspacing="3" cellpadding="3" border="0" width="100%">
                        	<tr>
                            	<td width="20%"><label>Username :</label></td>
                            	<td> <input type="text" name="username" value="<?php echo $username;?>"/></td>
                            </tr>
                        	<tr>
                            	<td width="20%"><label>Email :</label></td>
                            	<td> <input type="text" name="email" value="<?php echo $email;?>"/></td>
                            </tr>
                            <tr>
                            	<td width="20%">&nbsp;</td>
                            	<td>
                                	<input type="submit" value="Update Member" name="add_member"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                    </fieldset>
                </div>
       		
        <?php		
				break;
				default :
		?>
		<script src="../js/tablesorter.js"></script>
        <script src="../js/tablesorter_filter.js"></script>
        <script type="text/javascript">
          jQuery(document).ready(function() {
			  
			$('#q-box').change(function(){
					if($('#q-box').val == "" )
						$('.pagination').css('display','block');
					else if($('#q-box').val != "" )
						$('.pagination').css('display','none');
				});
			
            $("#membertable")
              .tablesorter({debug: false, widgets: ['zebra'], sortList: [[0,0]]})
              .tablesorterFilter({filterContainer: $("#q-box"),
                                  filterClearContainer: $("#filter-clear-button"),
                                  filterColumns: [0, 1, 2],
                                  filterCaseSensitive: false});
          });
          function change_user_setting(action , id){
			  var result = false ;
			  if(action == "delete"){
				  result = confirm('Do you really want to delete ?');
			  }else if(action == "active"){
				  result = confirm('Do you really want to Active ?');
			  }else if(action == "inactive"){
				  result = confirm('Do you really want to Inactive?');
			  }
			  if(result)
	              window.location = 'action.php?actiontype=member&action='+action+'&mid='+id;
          }

				function call_search(search_value){
					if(search_value != '')
						$('.pagination').css('display','none');
					else	
						$('.pagination').css('display','block');
				}
				
				function call_submitform(){
					var q_value = $('#q-box').val();
					if (q_value != '' ){
						$('#search_form').submit();
					}
				}

        </script>
        <?php 
			// Set the database object 
			$db = new Database();  
			$db->set_db_host(DATABASE_HOST);
			$db->set_db_user(DATABASE_USERNAME);
			$db->set_db_pass(DATABASE_PASSWORD);
			$db->set_db_name(DATABASE_NAME);
			
			$db->connect();
			
			if(isset($_REQUEST['q'])){
				$_SESSION['admin_users_q'] = trim($_REQUEST['q']);
				$search_string = ' AND ( username LIKE "%'.$_SESSION['admin_users_q'].'%" OR email LIKE "%'.$_SESSION['admin_users_q'].'%" ) ';
			}elseif(isset($_SESSION['admin_users_q'])){
				$search_string = ' AND ( username LIKE "%'.$_SESSION['admin_users_q'].'%" OR email LIKE "%'.$_SESSION['admin_users_q'].'%" ) ';
			}else{
				$search_string = ' ';
			}
			
			if(isset($_REQUEST['reset'])){
				session_unregister('admin_users_q');
				$search_string = ' ';
			}

			$where = ' type = "user" and isdelete = 0  '.$search_string;
			$db->select('users', '*' , $where, 'id');
			$total_result = $db->getResult();

			$db->setResult();
			$total_member = count($total_result);
			$page_count = (int) isset($_REQUEST['page'])?$_REQUEST['page'] : 1 ;
			$pages = ceil($total_member / (int)ADMIN_MEMBER_PERPAGE );
			if($page_count ==0 || $pages < $page_count ){
				$page_count = 1; 
			}

			$lower_limit = ((int)ADMIN_MEMBER_PERPAGE * ( (int)$page_count - 1 )) ;

			$where = ' type = "user" and isdelete = 0 '.$search_string;
			$order = ' id LIMIT  '.$lower_limit.', '.ADMIN_MEMBER_PERPAGE;
			$res  = $db->select('users', '*' , $where, $order );
			$res = $db->getResult();
			// Check the users 
			?>
        	<div class="clear"></div>
            <div class="search">
                <form action="" method="post" id="search_form">
                	<label>Search :</label>
                    <input name="q" id="q-box" class="q-box-class" value="<?php echo isset($_SESSION['admin_users_q'])?$_SESSION['admin_users_q']:'';?>" 
                    placeholder="Search" maxlength="30" size="30" type="text" onkeyup="call_search(this.value)"/>&nbsp;&nbsp;
                    <input type="button" value="Search" title="Search" onclick="call_submitform();"/>&nbsp;&nbsp;
                    <input type="submit" value="Reset" title="Reset" name="reset"/>
                </form>
            </div>
            <span class="clr">&nbsp;</span>
			<?php if(count($res)){?>	
        	<div class="memberlist">
            <!-- search function -->
            	<table id="membertable" class="grid" cellspacing="0" cellpadding="0" width="100%">
                  <thead>
                	<tr class="grid_header">
                    	<th>S. No.</th>
                    	<th>Name</th>
                    	<th>Email</th>
                    	<th colspan="3" align="center"> Action </td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $member_count = 1;  foreach ($res as $r){ ?>						
                   	<tr>
	                   	<td><b><?php echo $member_count; $member_count++;?></b></td>
	                   	<td><?php echo $r['username'];?></td>
	                   	<td><?php echo $r['email']?></td>
                        <td><a href="members.php?action=edit&mid=<?php echo $r['id'];?>"> <img src="images/edit_icon.png" width="22" height="22" border="0" alt="" title="edit" /> </a></td>
                        <td>
                        	<a href="javascript:void(0);" 
                               onclick="change_user_setting('<?php if($r['isactive'] == 0){ echo 'active';}else{echo 'inactive';} ?>', <?php echo $r['id'];?>)"> <?php if($r['isactive'] == 0){ echo '<img src="images/inactive.png" width="22" height="22" border="0" alt="" title="inactive" />';}else{echo '<img src="images/active.png" width="22" height="22" border="0" alt="" title="active" />';} ?>
                            </a>
                        </td>
                        <td> <a href="javascript:void(0);" onclick="change_user_setting('delete', <?php echo $r['id'];?>)"><img src="images/delete_icon.png" width="17" height="22" border="0" alt="" title="delete" /></a></td>
                    </tr>
					<?php } ?>
                    </tbody>
                </table>
                <!-- Pagination start-->
                <div class="pagination">
                	<?php for($i = 1 ; $i <= $pages ; $i ++){ ?>
						<a href="members.php?page=<?php echo $i;?>" <?php if($i== $page_count) echo 'class="selected"';?> ><?php echo $i;?></a>
					<?php }?>
                </div>
                
            </div>
        
        <?php }else{ ?>
        		<div class="no_data"> No data found !!! </div>
		<?php } 
			break;
			}
        ?>
    </div>
<?php include('includes/footer.php'); ?>