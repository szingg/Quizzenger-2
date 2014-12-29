<?php
class View{

	private $path = 'templates';
	private $template = 'default';

	// Container for the Template Variables / Content
	private $_ = array();

	// Link a variable with a key
	public function assign($key, $value){
		$this->_[$key] = $value;
	}

	public function setTemplate($template = 'default'){
		$this->template = $template;
	}

	public function loadTemplate(){
		$tpl = $this->template;
		$file = $this->path . DIRECTORY_SEPARATOR . $tpl . '.php';
		$exists = file_exists($file);

		if ($exists){
			// Output of script into buffer, not into output
			ob_start();
			// Load template and redirect its output into variable
			include $file;
			$output = ob_get_contents();
			ob_end_clean();
				
			return $output;
		}
		else {
			return 'could not find template'; // we will show default page
		}
	}
}
?>