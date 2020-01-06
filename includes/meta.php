<?php
$color_meta_options = array(
	'type' => 'string',
	'description' => 'A meta key responsible for user nickname color',
	'single' => true,
	'show_in_rest' => false,
);

$rank_meta_options = array(
	'type' => 'string',
	'description' => 'A meta key responsible for user rank display',
	'single' => true,
	'show_in_rest' => false,
);

register_meta('user', 'nickname_color', $color_meta_options);
register_meta('user', 'rank', $rank_meta_options);
?>
