<?php

class FormInput 
{
	private $name;
	private $type;
	private $required = true;
	private $label;
	private $value = null;
	
	public function __construct($name, $type, $required = true, $label = null)
	{
		$this->name = $name;
		$this->type = $type;
		$this->required = $required;
		$this->label = $label ?: ucfirst($name);
	}
	
	public function submit($value)
	{
		$this->value = trim($value);
		return $this->value;
	}
	
	public function getErrors()
	{
		$errors = [];
		if ($this->required && (! $this->value)) {
			$errors[] = sprintf("%s is required.", $this->label);
		}
		return $errors;
	}
}
 
class Form 
{
	private $method = 'post';
	private $inputs = [];
	private $data = null;
	
	public function isMethod($method)
	{
		return strtolower($method) == $this->method;
	}
	
	public function addInput($name, $type, $required = true)
	{
		$input = new FormInput($name, $type, $required);
		$this->inputs[$name] = $input;
		return $input;
	}
	
	public function submit(array $input)
	{
		$this->data = [];
		foreach ($input as $name => $value) {
			if (! isset($this->inputs[$name])) {
				continue;
			}
			$this->data[$name] = $this->inputs[$name]->submit($value);
		}
	}
	
	private function isSubmitted()
	{
		return null !== $this->data;
	}
	
	public function isValid() 
	{
		return $this->isSubmitted() && count($this->getErrors()) == 0;
	}
	
	public function getErrors()
	{
		if (! $this->isSubmitted()) {
			return [];
		}
		$errors = [];
		foreach ($this->inputs as $input) {
			$errors = array_merge($errors, $input->getErrors());
		}
		return $errors;
	}
	
	public function getValues()
	{
		return $this->data;
	}
}


$form = new Form();
$form->addInput('name', 'text');
$form->addInput('email', 'email');
$form->addInput('message', 'textarea');

if ($form->isMethod($_SERVER['REQUEST_METHOD'])) {
	$form->submit($_POST);
	if ($form->isValid()) {
		$v = $form->getValues();
		$to = 'laxtoyvr@gmail.com';
		$from = 'contactform@alicekodesign.com';
		$replyTo = sprintf("%s <%s>", $v['name'], $v['email']);
		$subject = "Email from $replyTo";
		$headers = [
			"From: $from",
			"Reply-To: $replyTo",
		];
		mail($to, $subject, $v['message'], join("\r\n", $headers));

		header("Location: ./contact-submit.php");
		exit;
	}
}

$errors = $form->getErrors();

?>

<?php include "header.php"; ?>
 
		<div id="main" class="contact grid_12 clearfix">
			
		
			<div id="content" class="grid_9 prefix_3">
					<h2 class="entry-title">Contact</h2>
					
					<h3>If you have any questions or would like to work together, please send me a message.</h3>	
					<ul class="errors">
					<?php foreach ($errors as $error): ?>
						<li><?php echo $error; ?></li>
					<?php endforeach; ?>
					</ul>
					
						<div id="contact-form" class="clearfix">
						<form method="post">

							<p>
								<label>Name</label><br>
								<input type="text" name="name" required /><br>
							</p>

							<p>
								<label>E-mail</label><br>
								<input type="email" name="email" required /><br>
							</p>

							<p>
								<label>Message</label><br>
								<textarea name="message" required ></textarea><br>
							</p>

							<p>
								<button type="submit" class="send-button">Send</button>
							</p>
							
						</form>
						</div> <!-- #contact-form -->
					<p><a href="http://twitter.com/intent/user?screen_name=laxtoyvr" target="_blank" title="Go to Alice's Twitter page">Follow @laxtoyvr</a></p>	
					<p><a href="https://ca.linkedin.com/in/aliko98" target="_blank" title="Check out Alice's LinkedIn profile">Connect on LinkedIn</a></p>
			</div> <!-- #content -->
			
			
			
		</div> <!-- #main -->



<?php include "footer.php"; ?>
