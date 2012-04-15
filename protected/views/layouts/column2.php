<?php $this->beginContent('//layouts/main'); ?>
<div id="page" class="span10">
	<div>
		<?php
			foreach(Yii::app()->user->getFlashes() as $key => $message) {
				echo CHtml::tag('div', array('class' => 'alert alert-' . $key), CHtml::encode($message));
			}
		?>
		<?php echo $content; ?>
	</div>
</div>
<div class="span2 pull-right">
	<div id="sidebar">
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'<h3>Operations</h3><hr/>',
		));
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->menu,
				'htmlOptions'=>array('class'=>'nav Neverland'),
				'linkLabelWrapper' => 'h5',
			));
		$this->endWidget();
	?>
	</div><!-- sidebar -->
</div>
<?php $this->endContent(); ?>