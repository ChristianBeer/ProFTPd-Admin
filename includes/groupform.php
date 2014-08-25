<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 *
 */
$zero = 0; // this is needed so phpDocumentor recognizes the docBlock above
?>

<table>
	<form method="post">
		<tr>
			<td colspan="2"></td>
		</tr>
		<tr bgcolor="<?php print ($cfg['tpbgcolor']) ?>">
			<td><b>Label</b></td>
			<td><b>Input</b></td>
		</tr>
		<tr bgcolor="<?php print ($cfg['dwbgcolor1']) ?>">
			<td>Group name *:</td>
			<td>
				<input type="hidden" size="10" name="action" value="newgroup" />
				<input type="text" size="20" name="new_group_name" />
			</td>
		</tr>
		<tr bgcolor="<?php print ($cfg['dwbgcolor2']) ?>">
			<td>GID (&gt; 1000) *:</td>
			<td>
				<input type="text" size="20" name="new_group_gid">
			</td>
		</tr>
		<tr>
			<td>*<i>required</i>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Create"></td>
		</tr>
	</form>
</table>
