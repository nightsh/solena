<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Ben Cooksley">

	<!-- Le styles -->
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-solena.css" rel="stylesheet">
	
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<!-- Le fav and touch icons -->
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
</head>

<body data-spy="scroll">

	<div id="header" class="navbar navbar-top Neverland">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="/"><img src="<?php echo Yii::app()->request->baseUrl; ?>/img/logo.plain.png"/></a>
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<div class="nav-pills pull-right nav-collapse">
				<?php $this->widget('zii.widgets.CMenu',array(
					'items'=>array(
						array('label' => 'People', 'url' => array('/people/index'), 'template' => '<i class="icon-user"></i>{menu}'),
						array('label' => 'Groups', 'url' => array('/groups/index'), 'template' => '<i class="icon-th-large"></i>{menu}'),
						array('label' => 'Registrations', 'url' => array('/registration/list'), 'template' => '<i class="icon-pencil"></i>{menu}', 'visible' => Yii::app()->user->checkAccess('sysadmins')),
						array('label' => 'Developer Applications', 'url' => array('/developerApplication/list'), 'template' => '<i class="icon-file"></i>{menu}', 'visible' => Yii::app()->user->checkAccess('sysadmins')),
						array('label' => 'My Account', 'url' => array('/people/view', 'uid' => Yii::app()->user->id), 'template' => '<i class="icon-user"></i>{menu}', 'visible' => !Yii::app()->user->isGuest),
						array('label' => 'Login', 'url'=>array('/site/login'), 'visible' => Yii::app()->user->isGuest),
						array('label' => 'Register', 'url'=>array('/registration/index'), 'template' => '<i class="icon-pencil"></i>{menu}', 'visible' => Yii::app()->user->isGuest),
						array('label' => 'Logout ('.Yii::app()->user->name.')', 'url' => array('/site/logout'), 'template' => '<i class="icon-remove-sign"></i>{menu}', 'visible' => !Yii::app()->user->isGuest)
					),
					'htmlOptions' => array('class'=>'nav'),
					)); ?>
				</div>
			</div>
		</div>
	</div>
	
<div class="container Neverland" id="page">

	<?php echo $content; ?>

	<div id="footerRow" class="row">
		<div class="navbar navbar-bottom Neverland">
			<div class="navbar-inner">
				<div class="container">
					<ul class="breadcrumb">
						<li>
						<?php if(isset($this->breadcrumbs)):?>
							<?php $this->widget('zii.widgets.CBreadcrumbs', array(
								'links'=>$this->breadcrumbs,
							)); ?><!-- breadcrumbs -->
						<?php endif?>
						</li>
					</ul>
					<ul class="nav pull-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret-up"></b></a>
							<?php $this->widget('zii.widgets.CMenu',array(
								'items'=>array(
									array('label' => 'Home', 'url' => array('/site/index'),
									array('label' => 'People', 'url' => array('/people/index')),
									array('label' => 'Groups', 'url' => array('/groups/index')),
									array('label' => 'Registrations', 'url' => array('/registration/list'), 'visible' => Yii::app()->user->checkAccess('sysadmins')),
									array('label' => 'Developer Applications', 'url' => array('/developerApplication/list'), 'visible' => Yii::app()->user->checkAccess('sysadmins')),
									array('label' => 'My Account', 'url' => array('/people/view', 'uid' => Yii::app()->user->id), 'visible' => !Yii::app()->user->isGuest),
									array('label' => 'Login', 'url'=>array('/site/login'), 'visible' => Yii::app()->user->isGuest),
									array('label' => 'Register', 'url'=>array('/registration/index'), 'visible' => Yii::app()->user->isGuest),
									array('label' => 'Logout ('.Yii::app()->user->name.')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest)
								),
								'htmlOptions' => array('class'=>'dropdown-menu bottom-up'),
							)); ?>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<footer class="Neverland">
			<p>Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
			All Rights Reserved.<br/>
			<?php echo Yii::powered(); ?></p>
		</footer>
	</div>
</div><!-- page -->

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-transition.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-dropdown.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-scrollspy.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-tooltip.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-popover.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-button.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-collapse.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-carousel.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap-neverland.js"></script>
</body>
</html>