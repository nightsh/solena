<?php $this->beginContent('//layouts/main'); ?>
<div id="page" class="span-7">
	<div id="content">
		<?php
			foreach(Yii::app()->user->getFlashes() as $key => $message) {
				echo CHtml::tag('div', array('class' => 'flash-' . $key), CHtml::encode($message));
			}
		?>
		<?php echo $content; ?>
	</div><!-- content -->
	</div>
<div class="span-4">
	<div id="sidebar">
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
	</div><!-- sidebar -->
</div>
<?php $this->endContent(); ?>