<?php
namespace pl\forseti\cli;

use pl\forseti\reuse\FilesystemException as FSe;

/**
 * Loading and converting help file to CLI, MarkDown or web use
 */
class Help
{
	public static function printTerm($sn) {
		$help = self::getFile($sn);
		foreach (getTermSubsts() as $tag=>$subst) {
			$help = str_ireplace($tag, '\e['. $subst .'m', $help);
		}
		echo $help . '\e[0m';
		exit;
	}
	
	private static function getFile($sn) {
		$helpFile = dirname($sn).'/help/'. substr(basename($sn),0,strripos($sn, '.php')) . '.help';
		if (file_exists($helpFile)) {
			return include $helpFile;
		} else throw new FSe("Help file `$helpFile` not found", FSe::FILE_NOT_FOUND);
	}
	
	private static function getTermSubsts() {
		return array(
					'<b>'=>'\e[1m',
					'</b>'=>'\e[21m',
					'<i>'=>'\e[3m',
					'</i>'=>'\e[23m',
					'<u>'=>'\e[4m',
					'</u>'=>'\e[24m',
					'<code>'=>'\e[96m',
					'</code>'=>'\e[39m',
					'<cite>'=>'\e[37m',
					'</cite>'=>'\e[39m',
					'<h1>'=>'\e[93m\e[4m',
					'</h1>'=>'\e[39m\e[24m',
					'<h2>'=>'\e[1m\e[4m',
					'</h2>'=>'\e[21m\e[24m',
					'<h3>'=>'\e[4m',
					'</h3>'=>'\e[24m',
					'&gt;'=>'>',
					'&lt;'=>'<',
					);
	}
	
	private static function getMdSubsts() {
		return array(
				'<b>'=>'**',
				'</b>'=>'**',
				'<i>'=>'*',
				'</i>'=>'*',
				'<u>'=>'`',
				'</u>'=>'`',
				'<code>'=>'```',
				'</code>'=>'```',
				'<cite>'=>'',
				'</cite>'=>'',
				'<h1>'=>'#',
				'</h1>'=>'',
				'<h2>'=>'##',
				'</h2>'=>'',
				'<h3>'=>'###',
				'</h3>'=>'',
				'&gt;'=>'>',
				'&lt;'=>'<',
		);
	}
}

 ?>