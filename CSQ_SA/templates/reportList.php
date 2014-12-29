<?php
	if (! is_null ( $this->_ ['reports'] )) {
		foreach ( $this->_ ['reports'] as $entry )  { ?>
		<tr>
			<td>
				<?= $entry['username']; ?>
			</td>
			<td>
				<?= $entry['date']; ?>
			</td>
			<td>
				<?= $entry['message']; ?>
			</td>
		</tr><?php
		}
	}
?>