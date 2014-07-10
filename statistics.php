<?php
	/*
		Developer :- SIPL 
		Purpose :- mail statistics at admin 
		date :- sep 18 2012 
	*/
	include('includes/header.php');
	include('../includes/config.php');
	include('../classes/database.php');
	
	$db = new Database();  
	$db->set_db_host(DATABASE_HOST);
	$db->set_db_user(DATABASE_USERNAME);
	$db->set_db_pass(DATABASE_PASSWORD);
	$db->set_db_name(DATABASE_NAME);

	$db->connect();
	$db->setResult();

	if(isset($_REQUEST['q'])){
		$_SESSION['admin_member_q'] = trim($_REQUEST['q']);
		$search_string = ' WHERE u.username LIKE "%'.$_SESSION['admin_member_q'].'%" OR u.email LIKE "%'.$_SESSION['admin_member_q'].'%"  ';
	}elseif(isset($_SESSION['admin_member_q'])){
		$search_string = ' WHERE u.username LIKE "%'.$_SESSION['admin_member_q'].'%" OR u.email LIKE "%'.$_SESSION['admin_member_q'].'%" ';
	}else{
		$search_string = ' ';
	}

	if(isset($_REQUEST['reset'])){
		session_unregister('admin_member_q');
		$search_string = ' ';
	}
	// query for pagination 
	$db->query('SELECT u.id as uid, m.id as mid, username , email , u.createddate , COUNT(contentid) from mailtracking as m JOIN users as u ON u.id = m.toid '.$search_string.' GROUP BY  u.id ORDER BY  u.createddate DESC');
	$res = $db->getResult();
	$db->setResult();
	// query for per page records 
	$total_member = count($res);
	$page_count = (int) isset($_REQUEST['page'])?$_REQUEST['page'] : 1 ;
	$pages = ceil($total_member / (int)ADMIN_MEMBER_PERPAGE );
	if($page_count ==0 || $pages < $page_count ){
		$page_count = 1; 
	}
	$lower_limit = ((int)ADMIN_MEMBER_PERPAGE * ( (int)$page_count - 1 )) ;
	$limit = ' LIMIT  '.$lower_limit.', '.ADMIN_MEMBER_PERPAGE;
	$db->query('SELECT u.id as uid, m.id as mid, username , email , u.createddate , COUNT(contentid) from mailtracking as m JOIN users as u ON u.id = m.toid '.$search_string.' GROUP BY  u.id ORDER BY  u.createddate DESC '.$limit );
	$res = $db->getResult();
?>
	<script src="../js/tablesorter.js"></script>
    <script src="../js/tablesorter_filter.js"></script>
	<script>
		$(document).ready(function(e) {
            $("#membertable")
              .tablesorter({debug: false, widgets: ['zebra'], sortList: [[0,0]]})
              .tablesorterFilter({filterContainer: $("#q-box"),
                                  filterClearContainer: $("#filter-clear-button"),
                                  filterColumns: [0, 1, 2],
                                  filterCaseSensitive: false});
        });
		
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
    <div class="wrapper">
		<?php if(isset($_SESSION['error_message'])){?>
            <div class="error_message">	
                <?php echo $_SESSION['error_message']; session_unregister('error_message');?>
            </div>
        <?php }?>
	
		<div class="blue_head"><h1>Statistics</h1></div>
    	<div class="main_menu">
	        <?php include('includes/header_menu.php'); ?>
        </div>
      	<div class="clear"></div>
		<div class="content">
        
        	<div class="message_list_to_user">
            	<div class="search">
                <form action="" method="post" id="search_form">
                <label>Search :</label>
                <input name="q" id="q-box" class="q-box-class" value="<?php echo isset($_SESSION['admin_member_q'])?$_SESSION['admin_member_q']:'';?>" placeholder="Search" maxlength="30" size="30" type="text" onkeyup="call_search(this.value)"/>&nbsp;&nbsp;
				<input type="button" value="Search" title="Search" onclick="call_submitform();"/> &nbsp;&nbsp;<input type="submit" value="Reset" title="Reset" name="reset"/>
                </form>
                </div>
                <?php if(count($res)){?>
            <div class="memberlist">
            	<h3 class="text_msg"> Number of messages sent to members: </h3>
                <table id="membertable" class="grid" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <thead>
                        <tr class="grid_header">
                            <th>S. No.</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Today</th>
                            <th>Average per day</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php /*print_r($res);*/ 
                            $mail_count  = 1;  
                            foreach ($res as $r){ 
                        ?>
                        <tr>
                            <td><b><?php echo $mail_count; $mail_count++;?></b></td>
                            <td><?php echo $r['username'];?></td>
                            <td><?php echo $r['email'];?></td>
                            <td><?php echo todays_mail( $db, $r['uid']);?></td>
                            <td><?php echo floor((int)$r['COUNT(contentid)'] / (int)( date_difference( $r['createddate']  , date('Y-m-d H:i:s')) + 1) ); ?></td>
                            <td><?php echo $r['COUNT(contentid)'];?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            
            <?php }else{ ?>
            	<div> No data found !!!</div>
            <?php }?>
                <!-- Pagination start-->
                <div class="pagination">
                	<?php for($i = 1 ; $i <= $pages ; $i ++){ ?>
						<a href="statistics.php?page=<?php echo $i;?>" <?php if($i== $page_count) echo 'class="selected"';?>><?php echo $i;?></a>
					<?php }?>
                </div>
                </div>
            </div>
        </div>        	
    </div>
<?php
	include('includes/footer.php');
	
	// Function for calculate the difference between date
	function date_difference($from , $to){
		$to_timestamp = strtotime($to) ;
		$from_timestamp = strtotime($from);
		if($from_timestamp < $to_timestamp){
			$days = ($to_timestamp - $from_timestamp) / (60 * 60 * 24);
			return floor( $days);
		}else{
			return -1; 
		}
	}
	// function to call today's mail
	function todays_mail( $db, $id){
		$res = $db->setResult();
		$db->query('SELECT COUNT(contentid) from mailtracking as m JOIN users as u ON u.id = m.toid  WHERE m.createddate BETWEEN "'.date('Y-m-d 00:00:00').'" AND "'.date('Y-m-d 23:59:59').'" AND u.id  = '.$id.' GROUP BY  u.id ORDER BY  u.createddate DESC ' );
		$res = $db->getResult();
		return isset($res[0]['COUNT(contentid)'])?$res[0]['COUNT(contentid)'] : 0;
	}
?>
