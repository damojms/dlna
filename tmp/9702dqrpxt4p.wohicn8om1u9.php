
<nav aria-label="MORE">
	<ul class="pagination">
		<li<?php echo $result['pos'] == 0 ? ' class="disabled"' :''; ?>>
			<a href="<?php echo $browse_base/$PARAMS['id']; ?>" aria-label="Previous">
				<span aria-hidden="true">&laquo;</span>
			</a>
		</li>
		<?php foreach (($pagination?:[]) as $pg): ?>
				<li<?php echo $pg['pos'] == $PARAMS['page'] ? ' class="active"' : ''; ?>><a href="<?php echo $browse_base/$PARAMS['id']/$pg['value']; ?>"><?php echo $pg['value']; ?></a></li>
		<?php endforeach; ?>

		<li<?php echo $result['pos'] == ($result['count'] - 1) ? ' class="disabled"' :''; ?>>
			<a href="<?php echo $browse_base/$PARAMS['id']/$pages['count']; ?>" aria-label="Next">&raquo;</a>
		</li>
	</ul>
</nav>
		