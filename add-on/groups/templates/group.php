<?php /*$gr_data->group_id //идентификатор группы$gr_data->imade_id //идентификатор авы группы$gr_data->admin_id //идентификатор админа$gr_data->users_count //кол-во пользователей группы*/ global $gr_data;?><div id="group-<?php echo $gr_data->group_id; ?>" class="group-info <?php class_group(); ?>">	<div class="header-group">		<div id="avatar-gr"><?php images_group(); ?></div>		<h2 class="groupname"><?php group_name(); ?></h2>		<div id="meta-gr">			<p class="admin-group"><?php admin_group('Создатель: '); ?></p>			<p class="users-group">Участников: <?php echo $gr_data->users_count; ?></p>		</div>		<div class="desc_group"><?php desc_group(); ?></div>		<div class="group_content"></div>		<?php after_header_group(); ?>	</div>	<div class="options-group">		<?php options_group(); ?>	</div>	<div class="buttons-group">		<?php buttons_group(); ?>	</div>	<div class="userslist-group">		<?php userlist_group(); ?>	</div>	<div class="gallery-group">		<?php imagelist_group(); ?>	</div>	<div class="content-group">		<?php content_group(); ?>	</div>	<div class="form-group">		<?php form_group(); ?>	</div>	<div class="footer-group">		<?php footer_group(); ?>	</div></div>