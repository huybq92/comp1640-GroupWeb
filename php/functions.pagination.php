<?php
	function generatePagination($page, $max_page) {
		echo '<ul style="list-style-type:none;">';
		for( $i = 1; $i<=$max_page; $i++ ) {
		   	if($i==$page) {
				echo'<li style="float:left;padding:5px;font-size:13px;"><b>'.$i.'</b></li>';
			} else {
			 	echo'<li style="float:left;padding:5px;font-size:12px;"><a href="main.php?page='.$i.'">'.$i.'</a></li>';
			}
		}
		echo '</ul>';
	}

	function generatePaginationWithIds($idea_id, $idea_acc_id, $page, $max_page) {
		echo '<ul style="list-style-type:none;">';
		for( $i = 1; $i<=$max_page; $i++ ) {
		   	if($i==$page) {
				echo'<li style="float:left;padding:5px;font-size:13px;"><b>'.$i.'</b></li>';
			} else {
			 	echo'<li style="float:left;padding:5px;font-size:12px;"><a href="comments.php?idea_id='. $idea_id. '&idea_acc_id='. $idea_acc_id. '&page='.$i.'">'.$i.'</a></li>';
			}
		}
		echo '</ul>';
	}
?>