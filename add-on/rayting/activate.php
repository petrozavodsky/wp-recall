<?phpglobal $wpdb;$table5 = RCL_PREF."rayting_post";if($wpdb->get_var("show tables like '". $table5 . "'") != $table5) {	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $table5 . "` (	  ID bigint (20) NOT NULL AUTO_INCREMENT,	  user INT(20) NOT NULL,	  	  post INT(20) NOT NULL,	  author_post INT(20) NOT NULL,	  status INT(20) NOT NULL,	  UNIQUE KEY id (id)	) DEFAULT CHARSET=utf8;");}$table6 = RCL_PREF."rayting_comments";if($wpdb->get_var("show tables like '". $table6 . "'") != $table6) {	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $table6 . "` (	  ID bigint (20) NOT NULL AUTO_INCREMENT,	  user INT(20) NOT NULL,	  	  comment_id INT(20) NOT NULL,	  author_com INT(20) NOT NULL,	  rayting INT(20) NOT NULL,	  time_action DATETIME NOT NULL,	  UNIQUE KEY id (id)	) DEFAULT CHARSET=utf8;");}$table7 = RCL_PREF."total_rayting_posts";if($wpdb->get_var("show tables like '". $table7 . "'") != $table7) {	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $table7 . "` (	  ID bigint (20) NOT NULL AUTO_INCREMENT,	  author_id INT(20) NOT NULL,	  post_id INT(20) NOT NULL,	  	  total INT(20) NOT NULL,	  UNIQUE KEY id (id)	) DEFAULT CHARSET=utf8;");	}$table8 = RCL_PREF."total_rayting_comments";if($wpdb->get_var("show tables like '". $table8 . "'") != $table8) {	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $table8 . "` (	  ID bigint (20) NOT NULL AUTO_INCREMENT,	  author_id INT(20) NOT NULL,	  comment_id INT(20) NOT NULL,	  	  total INT(20) NOT NULL,	  UNIQUE KEY id (id)	) DEFAULT CHARSET=utf8;");	}$table9 = RCL_PREF."total_rayting_users";if($wpdb->get_var("show tables like '". $table9 . "'") != $table9) {	   $wpdb->query("CREATE TABLE IF NOT EXISTS `". $table9 . "` (	  ID bigint (20) NOT NULL AUTO_INCREMENT,	  user_id INT(20) NOT NULL,  	  total INT(20) NOT NULL,	  UNIQUE KEY id (id)	) DEFAULT CHARSET=utf8;");	}global $rcl_options;if(!isset($rcl_options['type_rayt_post'])){	$rcl_options['rayt_post_recall'] = 1;	$rcl_options['rayt_post_user_rayt'] = 1;	$rcl_options['rayt_comment_recall'] = 1;	$rcl_options['rayt_comment_user_rayt'] = 1;	$rcl_options['count_rayt_post'] = 1;	$rcl_options['count_rayt_comment'] = 1;}update_option('primary-rcl-options',$rcl_options);?>