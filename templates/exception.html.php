<?php /** @var $this \League\Plates\Template\Template */ ?>
<?php /** @var $exception \Throwable */ ?>
<?php $this->layout('base.html') ?>

<div class="alert alert-warning">
    <h4><?php if ($exception->getCode()) : echo $exception->getCode() . ' - '; endif; ?><?php echo $this->e(get_class($exception)); ?></h4>
    <p><?php echo $this->e($exception->getMessage()); ?></p>
</div>
