
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('cake.generic');
		echo $this->Html->css('styles');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

		echo $this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js'); 
		echo $this->Html->script('https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'); 
		echo $this->Html->css('https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css'); 
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			
		</div>
		<?php echo $this->element('sidebar'); ?>
		<div id="content">
			<?php echo $this->Flash->render(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">

		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>

<?php echo $this->Html->script('crud_form', array('block' => 'script')); ?>
<?php echo $this->fetch('script'); ?>

</body>
</html>
