<?php $this->pageTitle=Yii::app()->name; ?>
<div class="hero-unit">
	<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

	<p class="lead">Welcome to the home of the KDE Identity system. </p>
	<p>
		KDE Identity is the central account manager for the KDE.org infrastructure.
		<br />
		A KDE Identity account allows unified access to most KDE.org websites, and commit access to the various code repositories.
	</p>

	<?php if( Yii::app()->user->isGuest ) { ?>
		<h2>Login</h2>
		<?php $this->renderPartial('_login', array('model' => $model));
	} ?>
</div>