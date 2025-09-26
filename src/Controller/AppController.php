<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\ORM\TableRegistry;
use Cake\Datasource\FactoryLocator;
use Cake\Event\EventInterface;

class AppController extends Controller
{
	public function initialize(): void
	{
		parent::initialize();

		$this->loadComponent('Flash');
		$this->loadComponent('Global');
		// $this->loadComponent('RequestHandler');

		if (!defined('HOME')) define('HOME', "http://" . $_SERVER['SERVER_NAME'] . "/");
	}

	public function beforeRender(EventInterface $event)
	{
		$prefix = null;

		if ($this->request->getParam('prefix'))
			$prefix = $this->request->getParam('prefix');

		$Settings = FactoryLocator::get('Table')->get('settings');

		$Config = $Settings->find('all')->first();

		$months_list = ['1' => 'Janeiro', '2' => 'Fevereiro', '3' => 'Março', '4' => 'Abril', '5' => 'Maio', '6' => 'Junho', '7' => 'Julho', '8' => 'Agosto', '9' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];

		if ($prefix == 'Admin') {

			$Usuarios = TableRegistry::getTableLocator()->get('Users');
			$InitFiles = TableRegistry::getTableLocator()->get('Files');

			if ($Config->maintenance) {
				$this->viewBuilder()->setLayout('maintenance');

				if (!stristr($this->request->getParam('action'), "maintenance")) {
					$this->redirectApp('maintenance', 'not');
				}
			} else if (stristr($this->request->getParam('action'), "ajax")) {
				$this->viewBuilder()->setLayout('ajax');

				if (stristr($this->request->getParam('action'), "maintenance")) {
					$this->redirectApp('login', 'not');
				}
			} else {
				$this->viewBuilder()->setLayout('admin');

				if (stristr($this->request->getParam('action'), "maintenance")) {
					$this->redirectApp('login', 'not');
				}
			}

			$session = $this->request->getSession();

			if ($session->check('logado')) {

				if ($this->request->getQuery('r')) {
					$this->redirectApp(base64_decode($this->request->getQuery('r')));
				}

				$logado = $session->read('logado');

				$permissions_all = $this->listPermission();

				$Usuario = $Usuarios->get($logado->id);

				$products_groupps = [];

				if ($logado->groupp->type_id == 3) {
					$users_accesses = 0;
					$size_amount = 0;

					$InitGroupps = TableRegistry::getTableLocator()->get('Groupps');

					$groupp = $InitGroupps->get($logado->groupp->id);

					if ($Usuario->products) {
						$products_groupps =    json_decode($Usuario->products, true);
					} else {
						if ($groupp->pruducts) {

							$products_groupps =    json_decode($groupp->pruducts, true);
						}
					}
				} else {

					$files = $InitFiles->find('all')->toArray();

					$size_amount = 0;

					if (count($files) > 0) {
						foreach ($files as $file) {
							$size_amount += $file->size;
						}
					}

					if ($logado->groupp->type_id == 2) {
						$users_accesses = $Usuarios->find('all')->where(['groupp_id' => $logado->groupp_id, 'blocked' => '0'])->count();
					} else {
						$users_accesses = $Usuarios->find('all')->where(['groupp_id' => 2, 'blocked' => '0'])->count();
					}
				}

				$Usuario->online = 2;
				$Usuario->typing = 0;
				$Usuario->user_to = 0;

				$Usuarios->save($Usuario);
				$checkUserApp = $this->checkUser();

				$this->set(compact('logado', 'products_groupps', 'checkUserApp', 'permissions_all', 'users_accesses', 'size_amount'));
			} else {

				if ($Config->maintenance) {
					$this->viewBuilder()->setLayout('maintenance');

					if (!stristr($this->request->getParam('action'), "maintenance")) {
						$this->redirectApp('maintenance', 'not');
					}
				} else {

					$this->viewBuilder()->setLayout('login');

					if (stristr($this->request->getParam('action'), "maintenance")) {
						$this->redirectApp('login', 'not');
					}
				}
			}

			$this->set(compact('Config', 'months_list'));

			$this->set('title', $Config->title);
		} elseif ($prefix == 'print') {
			$this->viewBuilder()->setLayout('print');
		} elseif ($prefix == 'Api') {
			$this->viewBuilder()->setLayout('documentation');
		} else {
			$this->viewBuilder()->setLayout('site');

			$Pages = TableRegistry::getTableLocator()->get('Pages');

			$pages = $Pages->getMenu();

			$this->set(compact('pages'));

			$this->set('title', $Config->title);
		}
	}

	public function redirectApp($url = "", $type = 'admin')
	{
		if ($type == 'admin') {
			$url_redirect = HOME . 'admin' . $url;
		} else {

			$url_redirect = HOME . $url;
		}
		echo "<script>window.location.href = '" . $url_redirect . "';</script>";
		exit;
	}

	public function checkSession()
	{

		$session = $this->request->getSession();

		$logado = $session->read('logado');

		if (isset($logado->id)) {

			$InitUsers = TableRegistry::getTableLocator()->get('Users');

			$userdb = $InitUsers->get($logado->id);

			if ($userdb->session_id) {

				if ($this->request->getSession()->id() != $userdb->session_id) {

					$session->delete('logado');
					$this->Flash->error(__('Outro acesso foi detectado em sua conta, efetue o login novamente.'));
					$this->redirect(['controller' => 'users', 'action' => 'login']);
				}
			}
		} else {

			if ($this->request->getQuery('r')) {
				$this->redirectApp('login?r=' . $this->request->getQuery('r'), 'logout');
			} else {

				$this->redirect(['controller' => 'users', 'action' => 'login']);
			}
		}
	}

	public function checkUser()
	{
		$this->checkSession();

		$session = $this->request->getSession();
		$logado = $session->read('logado');

		$retorno = array();

		if (isset($logado->profile_id)) {

			$PermissionsProfiles = TableRegistry::getTableLocator()->get('PermissionsProfiles');

			$permissions = array();

			$permissionsDb = $PermissionsProfiles->find('all')->where(["profile_id" => $logado->profile_id])->toArray();

			if (count($permissionsDb) > 0) {

				foreach ($permissionsDb as $permission) {

					$permissions[] = $permission->permission_id;
				}
			}

			$retorno['permissions'] = $permissions;
			$retorno['user'] = $logado;
		} else {
			$retorno['permissions'] = [];
			$retorno['user'] = new \stdClass();
			$retorno['user']->groupp = new \stdClass();
			$retorno['user']->groupp->type_id = '';
			$retorno['user']->groupp->id = '';
		}

		return $retorno;
	}

	public function saudacao()
	{
		$Retorno = "boa noite";

		if (date("H") >= 2) {
			$Retorno = "bom dia";
		}

		if (date("H") >= 12) {
			$Retorno = "boa tarde";
		}

		if (date("H") >= 19) {
			$Retorno = "boa noite";
		}

		return $Retorno;
	}

	public function sluga($str)
	{
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		$str = str_ireplace($a, $b, $str);

		$str = strtolower(trim($str));
		$str = preg_replace('/[^a-z0-9-]/', '-', $str);
		$str = preg_replace('/-+/', "-", $str);

		return trim($str, "-");
	}

	public function getIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	public function listPermission()
	{
		$this->checkSession();

		$session = $this->request->getSession();

		$logado = $session->read('logado');
		$permissions_all = array();

		if (isset($logado->profile_id)) {

			$InitPermissionsProfiles = TableRegistry::getTableLocator()->get('PermissionsProfiles');

			$permissionsdb = $InitPermissionsProfiles->find('all')->where(['profile_id' => $logado->profile_id])->toArray();

			foreach ($permissionsdb as $permissiondb) {
				$permissions_all[] = $permissiondb->permission_id;
			}
		}

		return $permissions_all;
	}

	public function checkPermission($permission, $tipo = "php")
	{

		$session = $this->request->getSession();

		$logado = $session->read('logado');

		$permissions_all = $this->listPermission();

		if (isset($logado->id)) {

			if (!in_array($permission, $permissions_all)) {

				if ($tipo ==  "php") {

					$this->Flash->error(__('Acesso negado, seu perfil de usuário não tem permissão necessária.'));

					$this->redirect(['controller' => 'welcome', 'action' => 'index']);
				} else {

					return false;
				}
			}
		} else {
			$this->redirect(['controller' => 'welcome', 'action' => 'index']);
		}

		return true;
	}

	public function isValidTokenApi()
	{
		require_once(ROOT .  DS  . 'vendor' . DS  . 'jwt' . DS . 'vendor' . DS . 'autoload.php');

		$data = new \stdClass;

		$data->status = 0;

		$requestHeaders = apache_request_headers();

		try {

			if (isset($requestHeaders['Authorization']) && $requestHeaders['Authorization']) {

				$token = str_replace('Bearer ', '', $requestHeaders['Authorization']);

				$key = Configure::read('SECURITY_SALT');

				$data->results = JWT::decode($token, new Key($key, 'HS256'));
				$data->status = 1;
			} else {
				$data->message = 'Request error.';
				$this->response = $this->response->withStatus(401);
			}
		} catch (\Throwable $e) {
			$data->message = $e->getMessage();
		}

		return $data;
	}

	public function accessApi($groupp_id = null)
	{

		$InitGroupps = TableRegistry::getTableLocator()->get('Groupps');

		$grouppDB = $InitGroupps->get($groupp_id);

		return $grouppDB->enable_api;
	}

	protected function isAdmin()
	{
		$user = $this->getCurrentUser();
		return $user && $user->role === 'admin';
	}

	protected function isStudent()
	{
		$user = $this->getCurrentUser();
		return $user && $user->role === 'student';
	}

	protected function getCurrentUser()
	{
		$session = $this->request->getSession();
		$logado = $session->read('logado');
		return $logado;
	}

	protected function getCurrentStudentId()
	{
		$user = $this->getCurrentUser();
		if ($user && $user->role === 'student') {
			return $user->student_id;
		}
		return null;
	}

	public function sendCurl($url = "", $data = [])
	{

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return json_decode($output);
	}

	public function postFile($file = null)
	{

		set_time_limit(0);

		$Settings = TableRegistry::getTableLocator()->get('Settings');

		$Config = $Settings->find('all')->first();

		$cfile = curl_file_create($file['tmp_name'], $file['type'], $file['name']); // try adding

		$imgdata = array('file' => $cfile);

		$headers = array("Content-Type" => "multipart/form-data");

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $Config->aws_api . '/' . 'upload');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POST, true); // enable posting
		curl_setopt($curl, CURLOPT_POSTFIELDS, $imgdata); // post images
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // if any redirection after upload
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_ENCODING, '');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate

		$results = curl_exec($curl);

		curl_close($curl);

		return json_decode($results);
	}
}
