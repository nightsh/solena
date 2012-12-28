<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Ben Cooksley">

	<?php
	/* Stylesheet files for Neverland */
	Yii::app()->clientScript->registerCssFile('//cdn.kde.org/css/bootstrap.css');
	Yii::app()->clientScript->registerCssFile('//cdn.kde.org/css/bootstrap-responsive.css');
	Yii::app()->clientScript->registerCssFile('//cdn.kde.org/css/bootstrap-solena.css');

	/* Javascript files */
	Yii::app()->clientScript->registerCoreScript('jquery');
	Yii::app()->clientScript->registerScriptFile("//cdn.kde.org/js/bootstrap.js", CClientScript::POS_END);
	Yii::app()->clientScript->registerScriptFile("//cdn.kde.org/js/bootstrap-neverland.js", CClientScript::POS_END);
	Yii::app()->clientScript->registerScriptFile("//cdn.kde.org/nav/global-nav.js", CClientScript::POS_END);
	?>

	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<!-- Le fav and touch icons -->
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
</head>

<body data-spy="scroll">
	<div id="header" class="navbar navbar-static-top Neverland">
		<div class="navbar-inner">
			<div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
				<a class="brand" href="<?php echo Yii::app()->controller->createUrl('/site/index'); ?>">
					<img src="//cdn.kde.org/img/logo.plain.small.png" alt="Home"/>
					KDE Identity
				</a>
				<div class="nav-collapse">
					<div class="nav pull-right">
					<?php $this->widget('application.components.NeverMenu',array(
						'items'=>array(
							array('label' => 'People', 'url' => array('/people/index'), 'glyphIcon' => 'user'),
							array('label' => 'Groups', 'url' => array('/groups/index'), 'glyphIcon' => 'th-list'),
							array('label' => 'Registrations', 'url' => array('/registration/list'), 'glyphIcon' => 'pencil', 'visible' => Yii::app()->user->checkAccess('sysadmins')),
							array('label' => 'Developer Applications', 'url' => array('/developerApplication/list'), 'glyphIcon' => 'file', 'visible' => Yii::app()->user->checkAccess('sysadmins')),
							array('label' => 'Apply for Developer Access', 'url' => array('/developerApplication'), 'glyphIcon' => 'file', 'visible' => Yii::app()->user->checkAccess('users')),
							array('label' => 'Privacy Policy', 'url' => array('/site/page', 'view' => 'privacypolicy'), 'glyphIcon' => 'flag'),
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
	</div>
	
	<div id="pageRow" class="container">
		<div class="row">
			<?php echo $content; ?>
			<div class="clearfix"></div>
		</div>
		<div id="footerRow">
			<div class="navbar navbar-bottom Neverland">
				<div class="navbar-inner">
					<div class="container">
						<ul class="nav">
							<li>
							<?php if(isset($this->breadcrumbs)):?>
								<?php
									if(Yii::app()->user->isGuest) {
										$this->widget('application.widgets.NBreadcrumbs', array(
											'links'=>$this->breadcrumbs,
											'htmlOptions' => array(
												'class' => 'breadcrumb',
											),
											'homeLink' => '<li><a href="'.Yii::app()->controller->createUrl('/site/login').'"><i class="icon-home icon-white"></i>Home</a></li>',
										));
									} else {
										$this->widget('application.widgets.NBreadcrumbs', array(
											'links'=>$this->breadcrumbs,
												'htmlOptions' => array(
												'class' => 'breadcrumb',
											),
											'homeLink' => '<li><a href="'.Yii::app()->controller->createUrl('/site/index').'"><i class="icon-home icon-white"></i>Home</a></li>',
									));
									}?><!-- breadcrumbs -->
							<?php endif?>
							</li>
						</ul>
						<ul class="nav pull-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-list icon-white"></i>KDE Links <b class="caret-up"></b></a>
								<ul id="global-nav" class="dropdown-menu bottom-up"></ul>
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
	</div><!-- container -->
</body>
</html>
