<?php 
	if (isset($page->model_id)) {

		switch ($page->model_id) {
			case '1':
				echo $this->element("inicial");
				break;
			case '2':
				echo $this->element("modelo1");
				break;
			case '3':
				echo $this->element("modelo2");
				break;
			case '4':
				echo $this->element("modelo3");
				break;
			case '5':
				echo $this->element("contato");
				break;	
			case '6':
				echo $this->element("modelo-blog");
				break;	
			default:
				echo $this->element("page");
		}

	} else if (isset($PostsPage->id)){
		echo $this->element("page-post");
	} else {
		echo $this->element("404");	
	}

?>