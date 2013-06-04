<?php

	echo CHtml::ajaxSubmitButton(
		'Migrate',
		array('migrate'),
		array(
			'update'	=> '#livemigration-container',
			'async'		=> false,
		)
	);
	
	echo CHtml::ajaxSubmitButton(
		'Mark',
		array('mark'),
		array(
			'update'	=> '#livemigration-container',
			'async'		=> false,
		)
	);
	
	echo CHtml::ajaxSubmitButton(
		'Refresh',
		array('refresh'),
		array(
			'update'	=> '#livemigration-container',
			'async'		=> false,
		)
	);
	
	echo CHtml::endForm();

?>