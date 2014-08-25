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

		<tr>
			<td colspan="2" align="center">
				<input type="submit" value="Update">
			</td>
		</tr>
	</form>
	<tr>
		<td colspan="2" align="center">
			<form method="post">
				<input type="hidden" name="uid" value="<?php print ($uid) ?>" />
				<input type="hidden" name="action" value="remove" />
				<input type="hidden" name="userid" value="<?php print ($userid) ?>" />
				<input type="submit" value="Remove" />
			</form>
		</td>
	</tr>
</table>
