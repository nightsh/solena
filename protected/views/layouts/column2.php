<?php $this->beginContent('//layouts/main'); ?>
<div id="page" class="span8">
	<div>
		<?php
			foreach(Yii::app()->user->getFlashes() as $key => $message) {
				echo CHtml::tag('div', array('class' => 'flash-' . $key), CHtml::encode($message));
			}
		?>
		<?php echo $content; ?>
	</div>
</div>
<div class="span4">
	<div id="sidebar">
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
		-			'title'=>'Operations',));
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->menu,
				'htmlOptions'=>array('class'=>'nav nav-list Neverland'),
				'linkLabelWrapper' => 'h3',
			));
		$this->endWidget();
	?>
	</div><!-- sidebar -->
</div>
<?php $this->endContent(); ?>