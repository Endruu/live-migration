<h2>Actual: <?php echo $latest; ?></h2>
	
<div id="livemigration-summary">

	<?php echo $this->renderPartial("_summary", array('mlist' => $mlist, 'latest' => $latest), true); ?>

</div>