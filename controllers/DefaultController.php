<?php

class DefaultController extends Controller
{

	protected $runner = null;


	protected function initCommandRunner()
	{

		if( $this->runner ) return;
		
		$r = new CConsoleCommandRunner();

		// --- load commands ------------
		$r->addCommands( Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands' );
		$r->addCommands( Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands' );

		// --- check for migrate cmd ----
		if( array_key_exists( 'migrate', $r->commands ) ) {
			$this->runner = $r;
		} else {
			throw new CException("Can't find 'migrate' command!");
		}
	}
	
	
	protected function getOldMigrations( $limit = null )
	{
		$this->initCommandRunner();
		
		if( $limit ) {
			$args = array('yiic', 'migrate', 'history', $limit, '--interactive=0');
		} else {
			$args = array('yiic', 'migrate', 'history', '--interactive=0');
		}
		ob_start();
		$this->runner->run($args);
		$mresponse = ob_get_clean();
		$lines = explode("\n", $mresponse);
		
		$migrations = array();

		foreach( $lines as $line) {	// latest first
			if( preg_match("/\((.*)\)\s*(m(\d\d)(\d\d)(\d\d)_(\d\d)(\d\d)(\d\d)_(.*))/", $line, $m) ) {
				$migrations[$m[2]] = array(
					'applied'	=> $m[1],
					'created'	=> $m[3] . '-' . $m[4] . '-' . $m[5] . ' ' . $m[6] . ':' . $m[7] . ':' . $m[8],
					'name'		=> implode( " ", explode( "_", $m[9] ) ),
				);
			}
		}
		
		echo $mresponse;
		return array_reverse($migrations);
	}
	
	
	protected function getNewMigrations()
	{
		$this->initCommandRunner();
		$args = array('yiic', 'migrate', 'new', '--interactive=0');
		ob_start();
		$this->runner->run($args);
		$mresponse = ob_get_clean();
		$lines = explode("\n", $mresponse);
		
		$migrations = array();

		foreach( $lines as $line) {	// oldest first
			if( preg_match("/((m)(\d\d)(\d\d)(\d\d)_(\d\d)(\d\d)(\d\d)_(.*))/", $line, $m) ) {
				$migrations[$m[1]] = array(
					'applied'	=> 'pending',
					'created'	=> $m[3] . '-' . $m[4] . '-' . $m[5] . ' ' . $m[6] . ':' . $m[7] . ':' . $m[8],
					'name'		=> implode( " ", explode( "_", $m[9] ) ),
				);
			}
		}
		
		return $migrations;
	}
	
	
	protected function getLatestMigration()
	{
		return array_keys( $this->getOldMigrations( 1 ) )[0];
	}

	
	protected function getMigrations()
	{
		return array(
			'mlist'		=> array_merge( $this->getOldMigrations(), $this->getNewMigrations()),
			'latest'	=> $this->getLatestMigration(),
		);
	}

	
	public function actionIndex()
	{
		$this->render( 'index', $this->getMigrations() );
	}

	
	public function actionMigrate()
	{

		if(isset($_POST['Post']))
		{
			$model->attributes=$_POST['Post'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->post_id));
		}


		$this->render('index');
	}

	
	public function actionMark()
	{
		$this->render('index');
	}
	
	
	public function actionRefresh()
	{
		$this->renderPartial( '_summary', $this->getMigrations() );
	}
}