<?php

class DefaultController extends Controller
{

	protected $runner	= null;
	protected $mpath	= null;

	protected function beforeAction($action)
	{

		if( $this->runner ) return;
		
		$r = new CConsoleCommandRunner();

		// --- load commands ------------
		$r->addCommands( Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands' );
		$r->addCommands( Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands' );

		// --- check for migrate cmd ----
		if( array_key_exists( 'migrate', $r->commands ) ) {
			$this->runner	= $r;
			$this->mpath	= Yii::getPathOfAlias( $r->createCommand('migrate')->migrationPath ) . DIRECTORY_SEPARATOR;
			return true;
		} else {
			throw new CException("Can't find 'migrate' command!");
		}
	}
	
	
	protected function getOldMigrations( $limit = null )
	{
		
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
					'status'	=> 'applied'
				);
				if( !file_exists( $this->mpath.$m[2].'.php' ) ) $migrations[$m[2]]['status'] = 'missing';
			}
		}
		
		if( array_key_exists( 'm000000_000000_base', $migrations ) ) {
			$migrations['m000000_000000_base']['status'] = 'base';
		}
		
		return array_reverse($migrations);
	}
	
	
	protected function getNewMigrations()
	{
		
		$args = array('yiic', 'migrate', 'new', '--interactive=0');
		ob_start();
		$this->runner->run($args);
		$mresponse = ob_get_clean();
		$lines = explode("\n", $mresponse);
		
		$migrations = array();

		foreach( $lines as $line) {	// oldest first
			if( preg_match("/((m)(\d\d)(\d\d)(\d\d)_(\d\d)(\d\d)(\d\d)_(.*))/", $line, $m) ) {
				$migrations[$m[1]] = array(
					'applied'	=> 'n/a',
					'created'	=> $m[3] . '-' . $m[4] . '-' . $m[5] . ' ' . $m[6] . ':' . $m[7] . ':' . $m[8],
					'name'		=> implode( " ", explode( "_", $m[9] ) ),
					'status'	=> 'pending'
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

	protected function migrateTo( $migration )
	{
	
		$latest		= $this->getLatestMigration();
		$direction	= 'up';
		$error		= 0;
		$title		= '';

		
		// check integrity
		$mgs = $this->getOldMigrations();
		foreach( $mgs as $id => $p ) {
			if( $migration == $id ) $direction = 'down';
			if( $direction == 'down' && $p['status'] == 'missing' ) {
				$mresponse	= "Error: Can't migrate down! Missing migration file(s)!";
				$error		= 1;
				$actual		= $latest;
			}
		}
		
		if( $migration == $latest ) {
			$args = array('yiic', 'migrate', 'redo', '1', '--interactive=0');
			$title = "Redoing migration: " . $migration;
		} else {
			$args = array('yiic', 'migrate', 'to', $migration, '--interactive=0');
			$title = "Migrating " . $direction . "<br />from: " . $latest . " <br />to: " . $migration;
		}

		if( !$error ) {
			ob_start();
			$this->runner->run($args);
			$mresponse = ob_get_clean();
			
			$actual = $this->getLatestMigration();
			if( $actual != $migration ) $error = 1;
		}

		return array(
			'mlist'		=> array_merge( $this->getOldMigrations(), $this->getNewMigrations()),
			'latest'	=> $actual,
			'title'		=> $title,
			'error'		=> $error,
			'response'	=> $mresponse
		);
		
	}

	
	public function actionIndex()
	{
		$this->render( 'index', $this->getMigrations() );
	}

	
	public function actionMigrate()
	{

		if(isset($_POST['selected']))
		{
			$selected = $_POST['selected'];
				$this->renderPartial( '_migrate', $this->migrateTo($selected) );
		}

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