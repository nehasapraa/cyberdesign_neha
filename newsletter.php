<?php
	/*
		Developer :- SIPL 
		Purpose :- members management at admin 
		date :- sep 5 2012 
	*/
	include('includes/header.php');
	include('../includes/config.php');
	include('../classes/database.php');

	$view = isset($_REQUEST['action'])? $_REQUEST['action'] : 'list';
	$nid = isset($_REQUEST['nid'])? $_REQUEST['nid'] : 0;
	
	if($view == 'edit'){
		$db = new Database();  
		$db->set_db_host(DATABASE_HOST);
		$db->set_db_user(DATABASE_USERNAME);
		$db->set_db_pass(DATABASE_PASSWORD);
		$db->set_db_name(DATABASE_NAME);
		
		$db->connect();
		$where = ' id = '.$nid.' and userid = '.$_SESSION['admin_id'].' and isdelete = 0  ';
		$db->setResult();
		if($db->select('templates', 'id, subject , message' , $where, ' id LIMIT 1')){
			$total_result = $db->getResult();
			if(count($total_result)){
				$tid = $total_result[0]['id'];
				$subject = $total_result[0]['subject'];
				$message = $total_result[0]['message'];
			}else{
				$view = '';
				$_SESSION['error_message'] .= '<br/>something Went wrong ';
			}
		}else{
			$view = '';
			$_SESSION['error_message'] .= '<br/>something Went wrong ';
		}
	}
?>
    <!-- Ck editor files -->
    <script type="text/javascript" src="../js/ckeditor.js"></script>
    <script type="text/javascript" src="../js/sample.js" ></script>
    <link href="../js/sample.css" rel="stylesheet" type="text/css" />
    <style>
	    .newsletter_sub_menu li {float:left;margin:20px;}
    </style>
    <div class="wrapper">
      <div class="blue_head">
        <h1>News Letter</h1>
      </div>
      <div class="main_menu">
        <?php include('includes/header_menu.php'); ?>
      </div>
      <div class="clear"></div>
      <?php if(isset($_SESSION['error_message'])){?>
      <div class="success_message"> <?php echo $_SESSION['error_message']; session_unregister('error_message');?> </div>
      <?php }?>
      <span class="clr">&nbsp;</span>
      <div class="sub_links">
        <ul>
          <li> <a href="newsletter.php?action=list" title="List" <?php if($view=='list'){echo 'class="selected"';}?>>List</a></li>
          <li> <a href="newsletter.php?action=add" title="Add" <?php if($view=='add'){echo 'class="selected"';}?>>Add</a></li>
        </ul>
      </div>
      <div class="clear"></div>
      <?php 
		switch ($view){
			case 'list' : 
      ?>
      <div class="newsletter_list_content">
        <script src="../js/ntip.js"></script> 
        <script src="../js/tablesorter.js"></script> 
        <script src="../js/tablesorter_filter.js"></script> 
        <script type="text/javascript">
			jQuery(document).ready(function() {
				$("#newstable")
				.tablesorter({debug: false, widgets: ['zebra'], sortList: [[0,0]]})
				.tablesorterFilter({
					filterContainer: $("#q-box"),
					filterClearContainer: $("#filter-clear-button"),
					filterColumns: [0, 1 ],
					filterCaseSensitive: false
				});
			});

			function admineditnewsletter(action , id ){
				if(confirm('Are you really want to '+action+' ?'))
					window.location = 'action.php?actiontype=editnewsletter&type='+action+'&id='+id;
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
			
			$(document).ready(function(){
				$(".newletterinfo").tooltip({tooltipcontentclass:"mycontent"})
			});
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
                        $_SESSION['admin_newsletter_q'] = trim($_REQUEST['q']);
                        $search_string = ' AND ( t.subject LIKE "%'.$_SESSION['admin_newsletter_q'].'%" OR t.message LIKE "%'.$_SESSION['admin_newsletter_q'].'%" ) ';
                    }elseif(isset($_SESSION['admin_newsletter_q'])){
                        $search_string = ' AND ( t.subject LIKE "%'.$_SESSION['admin_newsletter_q'].'%" OR t.message LIKE "%'.$_SESSION['admin_newsletter_q'].'%" ) ';
                    }else{
                        $search_string = ' ';
                    }
    
                    if(isset($_REQUEST['reset'])){
                         session_unregister('admin_newsletter_q');
                        $search_string = ' ';
                    }
    
                     $query = 'SELECT t.id as tid , t.subject as tsubject FROM templates as t left join contents as c ON t.id = c.templateid WHERE t.isdelete = 0 AND  t.templatetypeid = 2 AND t.userid = '.$_SESSION['admin_id'].' '.$search_string.'ORDER BY t.id ';
                    
                    $news_result  = $db->query($query);
                    $totalnews_result = $db->getResult();
        
                    $total_news = count($totalnews_result);
                    $page_count = (int) isset($_REQUEST['page'])?$_REQUEST['page'] : 1 ;
                    $pages = ceil($total_news/ (int)ADMIN_NEWSLETTER_PERPAGE );
                    if($page_count ==0 || $pages < $page_count ){
                        $page_count = 1;
                    }
                    $lower_limit = ((int)ADMIN_NEWSLETTER_PERPAGE * ( (int)$page_count - 1 )) ;
                    $totalnews_result = $db->setResult();
                    
                    $query = 'SELECT t.id as tid , t.subject as tsubject , t.isactive as tisactive , t.message as tmessage FROM templates as t left join contents as c ON t.id = c.templateid WHERE t.isdelete = 0 AND  t.templatetypeid = 2 AND t.userid = '.$_SESSION['admin_id'].' '.$search_string.'ORDER BY t.id LIMIT  '.$lower_limit.', '.ADMIN_NEWSLETTER_PERPAGE;   
                    $news_result  = $db->query($query);
    
                    $res = $db->getResult();
    
                    // Check the users 
                    if(count($res)){
                ?>
        <div class="clear"></div>
        <div class="newslist"> 
          <!-- search function -->
          <div class="search">
            <form method="post" id="search_form">
              <label>Search :</label>
              <input name="q" id="q-box" class="q-box-class" value="<?php echo isset($_SESSION['admin_newsletter_q'])?$_SESSION['admin_newsletter_q']:'';?>" placeholder="Search" maxlength="30" size="30" type="text" onkeyup="call_search(this.value)"/ > &nbsp;&nbsp;
              <input id="filter-clear-button" type="button" name="reset" value="Search" title="Search" onclick="call_submitform();"/> &nbsp;&nbsp;
              <input type="submit" name="reset" value="Reset" title="Reset" />
            </form>
          </div>
          <div class="memberlist">
          <table id="newstable" class="grid" cellspacing="0" cellpadding="0" width="100%">
            <thead>
              <tr class="grid_header">
                <th>S. No.</th>
                <th>Name</th>
                <th colspan="3" align="center"> Action </th>
              </tr>
            </thead>
            <tbody>
              <?php $member_count = 1;  foreach ($res as $r){ ?>
              <tr>
                <td><b><?php echo $member_count; $member_count++;?></b></td>
                <td><span class="newletterinfo word_wrap1"><?php echo $r['tsubject'];?><span style="display:none" class="mycontent"> <?php echo $r['tmessage'];?> </span></span></td>
                <td><a href="javascript:void(0);" onclick="admineditnewsletter('edit',<?php echo $r['tid'];?>)"><img src="images/edit_icon.png" width="22" height="22" border="0" alt="" title="edit" /></a></td>
                <td><a href="javascript:void(0);" onclick="admineditnewsletter('<?php if($r['tisactive']==0){ echo 'active';}else{echo 'inactive';}?>',<?php echo $r['tid'];?>)">
                  <?php if($r['tisactive']==0){ echo '<img src="images/active.png" width="22" height="22" border="0" alt="" title="active" />';}else{echo '<img src="images/inactive.png" width="22" height="22" border="0" alt="" title="inactive" />';}?>
                  </a></td>
                <td><a href="javascript:void(0);" onclick="admineditnewsletter('delete',<?php echo $r['tid'];?>)"><img src="images/delete_icon.png" width="17" height="22" border="0" alt="" title="delete" /></a></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          
          <!-- Pagination start-->
          <div class="pagination">
            <?php for($i = 1 ; $i <= $pages ; $i ++){ ?>
                <a href="newsletter.php?action=list&page=<?php echo $i;?>" <?php if($i== $page_count) echo 'class="selected"';?> ><?php echo $i;?></a>
            <?php }?>
          </div>
          </div>
          
          </div>
        </div>
        <?php }else{ ?>
        <div class="no_data"> No data found !!! </div>
        <?php } ?>
      </div>
      <?php			
                        break;
            case 'edit' : 
      ?>
      <div class="newsletter-content">
      <fieldset class="fieldset">
          <legend>Edit</legend>
        <form method="post" action="action.php">
          <input type="hidden" name="actiontype"  value="newsletter" />
          <input type="hidden" name="action"  value="update" />
          <input type="hidden" name="tid" value="<?php echo $tid;?>"/>
          <table cellspacing="3" cellpadding="3" border="0" width="100%">
            <tr>
              <td><label>Subject :</label></td>
              <td><input type="text" name="subject" size="100" value="<?php echo $subject?>"/></td>
            </tr>
            <tr>
              <td><label>Message :</label></td>
              <td><textarea class="ckeditor" cols="80" id="editor1" name="message" rows="10"><?php echo $message ;?></textarea></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><input type="submit" value="Update" title="Update" name="send"/></td>
            </tr>
          </table>
        </form>
        </fieldset>
      </div>
		<?php			
			break;
			case 'add' :
        ?>
        <script>
			$(document).ready(function(e) {
				$('#newsletter_add').click(function(){
					var subject  = $.trim($('input[name=subject]').val());
					var editor_data = $.trim(CKEDITOR.instances.editor1.getData());
					var return_result = false;
					if(subject != '' && editor_data !=''){
						return_result  = true ; 
					}else{
						if(subject == ''){
							$('.newsletter_subject').html('*');
						}else {
							$('.newsletter_subject').html('');
						}
						if(editor_data ==''){
							$('.newsletter_message').html('*');
						}else{
							$('.newsletter_message').html('');
						}
						return_result  = false ; 
					}
					return return_result;
				});
				
            });
        </script>
      <div class="newsletter-content">
      <fieldset class="fieldset">
          <legend>Add</legend>
        <form method="post" action="action.php" id="add_newsletter">
          <input type="hidden" name="actiontype"  value="newsletter" />
          <input type="hidden" name="action"  value="add" />
          <input type="hidden" name="formname" placeholder="formname"/>
          <input type="hidden" name="replyto" placeholder="replyto"/>
          <table cellspacing="3" cellpadding="3" border="0" width="100%">
            <tr>
              <td><label>Subject :</label></td>
              <td><input type="text" name="subject" size="100"/> <span class="newsletter_subject"></span></td>
            </tr>
            <tr>
              <td><label>Message :</label></td>
              <td><textarea class="ckeditor" cols="80" id="editor1" name="message" rows="10"></textarea><span class="newsletter_message"></span></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><input type="submit" value="Save" title="Save" name="send" id="newsletter_add"/></td>
            </tr>
          </table>
        </form>
        </fieldset>
      </div>
      <?php
                        break;
                    }
                ?>
    </div>
    <?php
        include('includes/footer.php');
    ?>