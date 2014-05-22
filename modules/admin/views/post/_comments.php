<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<?php foreach($comments as $comment): ?>
	<?= DetailView::widget([
        'model' => $comment,
        'attributes' => [
            'id',
            'author',
            'create_time:datetime',
            'content:ntext',
            
        ],
    ]) ?>
<?php endforeach; ?>