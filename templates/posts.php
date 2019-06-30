<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wp-hunt">
	<ul>
		<?php foreach ( $edges as $nodes ) : ?>
			<?php foreach ( $nodes as $node ) : ?>
				<li>
					<a class="inner" href="<?php echo $node['url']; ?>">
						<?php if ( 'image' === $node['thumbnail']['type'] ) : ?>
							<img alt="image" class="img-responsive" src="<?php echo $node['thumbnail']['url']; ?>" />
						<?php else : ?>
							<video><source src="<?php echo $node['thumbnail']['videoUrl']; ?>"></video>
						<?php endif; ?>
					</a>
					<h3><<a class="inner" href="<?php echo $node['url']; ?>"><?php echo $node['name']; ?></a></h3>
					<p>
						<?php echo $node['tagline']; ?>
						<br />Launched: <?php echo  date( 'Y-m-d', strtotime( $node['createdAt'] ) ); ?>
						<br />Rating: <?php echo  $node['reviewsRating']; ?>
						<br />Votes: <?php echo  $node['votesCount']; ?>
						<br /><?php echo  $node['description'] . '</p>'; ?>
						<br />
					<table cellspacing="0">
						<tr>
							<?php foreach ( $node['makers'] as $maker ) : ?>
								<td>
									<a href="<?php echo $maker['url']; ?>" ><img alt="<?php echo $maker['username']; ?>" src="<?php echo $maker['profileImage']; ?>" />
										<p>@<?php echo $maker['username']; ?></p>
									</a>
								</td>
							<?php endforeach; ?>
						</tr>
					</table>
				</li>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</ul>
</div>
