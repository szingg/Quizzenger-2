<?php
	if (! is_null ( $this->_ ['reports'] )) {
		foreach ( $this->_ ['reports'] as $entry )  { ?>
		<tr>
			<td>
				<?= htmlspecialchars($entry['username']); ?>
			</td>
			<td>
				<?= htmlspecialchars($entry['date']); ?>
			</td>
			<td>
				<?= htmlspecialchars($entry['message']); ?>
			</td>
		</tr><?php
		}
	}
?>