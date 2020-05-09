<?php

namespace helena\controllers\logs;

use helena\classes\Menu;
use helena\classes\Paths;
use helena\classes\Session;
use helena\controllers\common\cController;
use minga\framework\Context;
use minga\framework\Date;
use minga\framework\IO;
use minga\framework\Mail;
use minga\framework\Params;
use minga\framework\Str;
use minga\framework\System;

class cTests extends cController
{
	public function Show()
	{
		if ($app = Session::CheckIsMegaUser())
			return $app;

		$filesFramework = array_filter(IO::GetFiles(Context::Paths()->GetFrameworkTestsPath(), '.php'), [__CLASS__, 'EndsWithTest']);
		$files = array_filter(IO::GetFilesRecursive(Paths::GetTestsLocalPath(), '.php'), [__CLASS__, 'EndsWithTest']);

		$tests = [];
		foreach($files as $file)
		{
			$group = 'general';
			if(Str::Contains($file, '/'))
			{
				$parts = explode('/', $file);
				$group = $parts[0];
			}
			$tests[$group][] = [
				'url' => '/logs/tests?file=' . $file . '&group=' . $group,
				'name' => basename($file, '.php'),
			];
		}
		foreach($filesFramework as $file)
		{
			$tests['framework'][] = [
				'url' => '/logs/tests?group=framework&file=' . $file,
				'name' => basename($file, '.php'),
			];
		}

		if(Params::SafeGet('group') != '' || Params::SafeGet('file') != '')
		{
			$this->RunTest($tests);
			return;
		}

		$this->AddValues([
			'isSelf' => true,
			'tests' => $tests,
			'all_tests' => '/logs/tests?group=all',
			'group_tests' => '/logs/tests?group=',
			'version' => System::GetVersion(),
			'html_title' => 'Tests',
			'action_url' => '/logs/tests',
			'test_email' => 'pablodg@gmail.com',
		]);

		Menu::RegisterAdmin($this->templateValues);
		return $this->Render('tests.html.twig');
	}

	public function Post()
	{
		if ($app = Session::CheckIsMegaUser())
			return $app;

		$email = Params::SafePost('email');
		$mail = new Mail();
		$mail->to = $email;

		$mail->subject = 'Prueba de email en Mapas de Acta Académica - ' . Date::FormattedArNow();
		$mail->message =  "Contenido esdrújulo del mail.";
		$mail->Send(false, true);

		$this->AddValue('message', 'Email enviado');
		return $this->Show();
	}

	private function RunTest($tests)
	{
		$file = Params::SafeGet('file');
		$group = Params::SafeGet('group');
		$res = [];
		if($group == 'all')
			$res = System::RunCommandOnPath($this->GetCommand(), Context::Paths()->GetRoot());
		else if($group == 'framework' && $file == '')
			$res = System::RunCommandOnPath($this->GetCommand() . Context::Paths()->GetFrameworkTestsPath(), Context::Paths()->GetRoot());
		else if($group == 'framework' && $file != '')
			$res = System::RunCommandOnPath($this->GetCommand() . Context::Paths()->GetFrameworkTestsPath() . '/' . $file, Context::Paths()->GetRoot());
		else if($group != '' && $file == '')
			$res = System::RunCommandOnPath($this->GetCommand() . Paths::GetTestsLocalPath() . '/' . $group, Context::Paths()->GetRoot());
		else if($file != '')
			$res = System::RunCommandOnPath($this->GetCommand() . Paths::GetTestsLocalPath() . '/' . $file, Context::Paths()->GetRoot());
		else
			throw new \Exception('Error');

		echo '<!doctype html><html><head><meta charset="utf-8"><title>Test: ' . htmlentities($group) . '/' . htmlentities($file) . '</title></head><body><pre>';
		echo '<h2>Corriendo test: ' . htmlentities($group) . '/' . htmlentities($file) . '</h2>';
		echo implode("\n", $res);
		echo '</pre></body></html>';
		die;
	}

	private function GetCommand($path = '', $part = '')
	{
		return Paths::GetPHPUnitPath() . ' --verbose 2>&1 ';
	}

	private static function EndsWithTest($name)
	{
		return Str::EndsWith($name, 'Test.php');
	}

}