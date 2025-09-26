<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;
use Cake\Event\EventInterface;

class PagesController extends AppController
{

	public function beforeFilter(EventInterface $event)
	{
		parent::beforeFilter($event);
	}

	public function view($url)
	{
		$Settings = TableRegistry::getTableLocator()->get('Settings');
		$Config = $Settings->find('all')->first();

		if ($url == "index.php") {
			$url = "inicial";
		}

		if ($url == "inicial") {

			if ($Config->redirect_website) {

				if ($Config->url_site) {

					header("location: " . $Config->url_site);
					die;
				}
			}
		}

		$title_layout = $Config->title;

		$page = $this->Pages->getPaginaBySlug($url);

		$Posts = TableRegistry::getTableLocator()->get('Posts');
		$PostsPage = $Posts->getPostBySlug($url);

		$q = "";

		$faqs = array();
		$servicos = array();
		$comofunciona = array(); 
		$sobrenos = array();
		$faq = array();
		$contato = array();
		$documentos = array();
		$blogs = array();
		$blogsPopulares = array();
		$home = array();

		$documentos = $Posts->find('all', ['conditions' => ['categorie_id' => 4]])->toArray();

		if ($url == 'index' || $url == 'servicos' || $url == 'faq') {

			$servicos = $Posts->find('all', ['conditions' => ['categorie_id' => 2]]);

			$servicos = $servicos->toArray();

			$faqs = $Posts->find('all', ['conditions' => ['categorie_id' => 3]])->toArray();

			$home = $this->Pages->getPaginaById(1);
			$comofunciona = $this->Pages->getPaginaById(2);
			$sobrenos = $this->Pages->getPaginaById(3);
			$faq = $this->Pages->getPaginaById(4);
			$contato = $this->Pages->getPaginaById(5);
			$blogs = $Posts->find('all', ['conditions' => ['categorie_id' => 5], 'limit' => 3])->order(["created" => "DESC"])->toArray();
		}
		if ($url == 'blog') {

			$blogs = $Posts->find('all', ['conditions' => ['categorie_id' => 5], 'limit' => 6])->order(["created" => "DESC"])->toArray();
			$blogsPopulares = $Posts->find('all', ['conditions' => ['categorie_id' => 5], 'limit' => 5])->order(["view" => "DESC"])->toArray();
		}

		$noticias = [];
		if ($url == 'noticias') {

			$noticias = $Posts->find('all', ['conditions' => ['categorie_id' => 2]]);

			$noticia = $Posts->find('all', ['conditions' => ['categorie_id' => 2]])->first();

			$Config->image = $noticia->imagem;
		}

		if (!empty($page) && $page->slug != 'index') {
			$title_layout =  $title_layout . ' - ' . $page->title;
		}

		if (!empty($PostsPage)) {
			$title_layout = $PostsPage->title . ' - ' . $title_layout;;

			$postdb = $Posts->get($PostsPage->id);

			$savePost = array();
			$savePost['view'] = $PostsPage->view + 1;

			$postdb = $Posts->patchEntity($postdb, $savePost);

			$Posts->save($postdb);

			$Config->image = $PostsPage->image;
			$Config->title = $title_layout;
			$Config->description = strip_tags($PostsPage->summary);

			$blogsPopulares = $Posts->find('all', ['conditions' => ['categorie_id' => 5], 'limit' => 3])->order(["view" => "DESC"])->toArray();
		}

		$this->set(compact('q', 'page', 'PostsPage', 'noticias', 'title_layout', 'Config', 'comofunciona', 'sobrenos', 'faq', 'home', 'contato', 'faqs', 'servicos', 'documentos', 'blogs', 'blogsPopulares'));
	}

	public function blog($slug)
	{
		$Settings = TableRegistry::getTableLocator()->get('Settings');

		$Config = $Settings->find('all')->first();

		$Posts = TableRegistry::getTableLocator()->get('Posts');

		$page = $this->Pages->getPaginaBySlug('blog');

		$blogs = $Posts->find('all', ['conditions' => ['categorie_id' => 5], 'limit' => 6])->order(["created" => "DESC"])->toArray();
		$blogsPopulares = $Posts->find('all', ['conditions' => ['categorie_id' => 5], 'limit' => 5])->order(["view" => "DESC"])->toArray();

		$PostsPage = $Posts->getPostBySlug($slug);

		$title_layout = $Config->title;
		$this->set(compact('title_layout', 'Config', 'blogsPopulares', 'blogs', 'page'));
	}

	public function sendContact()
	{
		$this->autoRender = false;

		$res = new \stdClass();
		$res->status = 0;

		$Settings = TableRegistry::getTableLocator()->get('Settings');

		$Config = $Settings->find('all')->first();

		$title_layouit = $Config->title;

		if ($this->request->getData()) {
			$dados = $this->request->getData();

			if (count($dados) > 0) {

				if (isset($dados['full_name']) && $dados['full_name'] == '') {

					$html = @file_get_contents(HOME . 'email/contato.php');

					$html = str_ireplace(array(
						"%nome%",
						"%email%",
						"%telefone%",
						"%mensagem%",
					), array(
						$dados['contactsName'],
						$dados['contactsEmail'],
						$dados['contactsPhone'],
						$dados['contactsMassage'],
					), $html);

					$emails = array($Config->email);

					foreach ($emails as $theEmail) {

						$data = [];
						$data['destination'] = ($theEmail);
						$data['body'] = $html;
						$data['subject'] = 'Contato Site Corge Gestão de Seguros';
						$data['from'] = ("$Config->title <$Config->email>");

						$url = $Config->aws_api . "/send";

						$this->sendCurl($url, $data);
					}

					$html = @file_get_contents(HOME . 'email/agradece_contato.php');

					$html = str_ireplace(array(
						"%nome%",
					), array(
						$dados['contactsName'],
					), $html);

					$data = [];
					$data['destination'] = trim($dados['contactsEmail']);
					$data['body'] = $html;
					$data['subject'] = 'Obrigado pelo contato.';
					$data['from'] = ("$Config->title <$Config->email>");

					$url = $Config->aws_api . "/send";

					$this->sendCurl($url, $data);

					$res->status = 1;
				}
			}

			echo json_encode($res);

			exit;
		}
	}
}
