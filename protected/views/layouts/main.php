<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Ben Cooksley">

	<!-- Le styles -->
	<!--<link href="http://files.kde.org/neverland/css/bootstrap.css" rel="stylesheet">-->
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css" rel="stylesheet">
	<!--<link href="http://files.kde.org/neverland/css/bootstrap-responsive.css" rel="stylesheet">-->
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-responsive.css" rel="stylesheet">
	<!--<link href="http://files.kde.org/neverland/css/bootstrap-solena.css" rel="stylesheet"> -->
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
			<div class="container-fluid">
				<a class="brand" href="<?php echo Yii::app()->controller->createUrl('/site/index'); ?>"><img src="http://files.kde.org/neverland/img/logo.plain.png"/></a>
				<a class="btn menu" data-toggle="collapse" data-target=".nav-collapse">
					Menu
					<span class="caret"></span>
				</a>
				<div class="nav-pills pull-right nav-collapse">
				<?php $this->widget('application.components.NeverMenu',array(
					'items'=>array(
						array('label' => 'People', 'url' => array('/people/index'), 'glyphIcon' => 'user'),
						array('label' => 'Groups', 'url' => array('/groups/index'), 'glyphIcon' => 'th-list'),
						array('label' => 'Registrations', 'url' => array('/registration/list'), 'glyphIcon' => 'pencil', 'visible' => Yii::app()->user->checkAccess('sysadmins')),
						array('label' => 'Developer Applications', 'url' => array('/developerApplication/list'), 'glyphIcon' => 'file', 'visible' => Yii::app()->user->checkAccess('sysadmins')),
						array('label' => 'My Account', 'url' => array('/people/view', 'uid' => Yii::app()->user->id), 'glyphIcon' => 'user', 'visible' => !Yii::app()->user->isGuest),
						array('label' => 'Login', 'url'=>array('/site/login'), 'glyphIcon' => 'cog', 'visible' => Yii::app()->user->isGuest),
						array('label' => 'Register', 'url'=>array('/registration/index'), 'glyphIcon' => 'pencil', 'visible' => Yii::app()->user->isGuest),
						array('label' => 'Logout ('.Yii::app()->user->name.')', 'url' => array('/site/logout'), 'glyphIcon' => 'remove-sign', 'visible' => !Yii::app()->user->isGuest)
					),
					'htmlOptions' => array('class'=>'nav'),
					)); ?>
				</div>
			</div>
		</div>
	</div>
	
<div class="container-fluid Neverland" id="page">
	<div id="pageRow" class="row-fluid">

		<?php echo $content; ?>

	</div>
	<div id="footerRow" class="row-fluid">
		<div class="navbar navbar-bottom Neverland">
			<div class="navbar-inner">
				<div class="row-fluid">
					<ul class="breadcrumb">
						<li>
						<?php if(isset($this->breadcrumbs)):?>
							<?php $this->widget('zii.widgets.CBreadcrumbs', array(
								'links'=>$this->breadcrumbs,
							)); ?><!-- breadcrumbs -->
						<?php endif?>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<footer class="Neverland">
			Maintained by <a href="https://bugs.kde.org/enter_sysadmin_request.cgi">KDE Sysadmin.</a> Design by <a href="mailto:kde-www@kde.org">KDE Webteam.</a><br>
			KDE<sup>&reg;</sup> and <a href="/media/images/trademark_kde_gear_black_logo.png">the K Desktop Environment<sup>&reg;</sup> logo</a> 
			are registered trademarks of <a title="Homepage of the KDE non-profit Organization" href="http://ev.kde.org/">KDE e.V.</a> |
			<a href="http://www.kde.org/community/whatiskde/impressum.php">Legal</a>
		</footer>
	</div>
</div><!-- page -->

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="http://files.kde.org/neverland/js/bootstrap-transition.js"></script>
<script src="http://files.kde.org/neverland/js/bootstrap-dropdown.js"></script>
<script src="http://files.kde.org/neverland/js/bootstrap-scrollspy.js"></script>
<script src="http://files.kde.org/neverland/js/bootstrap-tooltip.js"></script>
<script src="http://files.kde.org/neverland/js/bootstrap-popover.js"></script>
<script src="http://files.kde.org/neverland/js/bootstrap-button.js"></script>
<script src="http://files.kde.org/neverland/js/bootstrap-collapse.js"></script>
<script src="http://files.kde.org/neverland/js/bootstrap-carousel.js"></script>
<script src="http://files.kde.org/neverland/js/bootstrap-neverland.js"></script>
</body>
</html>