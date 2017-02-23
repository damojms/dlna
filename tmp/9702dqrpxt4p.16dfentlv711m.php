<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h2 class="sub-header">Browse DLNA</h2>
	<div class="table-responsive">
		<table class="table table-striped">
		<thead>
			<tr><th>Browse</th></tr>
		</thead>
		<tbody>
		<?php if ($PARAMS['id'] != 0 && $PARAMS['id'] != ''): ?>
		
		<tr><td class="upfolder"><a href="/browse/<?php echo $up; ?>">Up</a></td></tr>
		
		<?php endif; ?>

		<?php foreach (($result['subset']?:[]) as $item): ?>

		<?php if ($item['CLASS'] == 'container.storageFolder'): ?>
			
			<tr><td class="folder"><a href="/browse/<?php echo $item['OBJECT_ID']; ?>"><?php echo $item['NAME']; ?></a></td></tr>
			
			<?php else: ?>
			<tr><td class="video"><img src="/thumb/<?php echo $item['OBJECT_ID']; ?>" /><a href="/detail/<?php echo $item['OBJECT_ID']; ?>"><?php echo $item['NAME']; ?></a></td></tr>
			
		<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
		</table>
	</div>
	<?php if ($result['count'] > 1): ?>
		
			<?php echo $this->render('partials/pagination.html',$this->mime,get_defined_vars(),0); ?>
		
	<?php endif; ?>
</div>
