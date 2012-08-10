<style>
input, select { width: 225px; }
.button-primary { width: 150px; }
</style>
<div class="wrap">
<h2>CCFA Options</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

Below are the options for the CCFA plugin.

<table class="form-table">
<tr valign="top">
<th scope="row">Audio Category</th>
<td><?php wp_dropdown_categories('name=ccfa_opt_audiocat&show_count=1&hierarchical=1&selected=' . get_option('ccfa_opt_audiocat')); ?></td>
</tr>
<tr valign="top">
<th scope="row">Logo URL <br /><small>Shows above player</small></th>
<td valign="middle"><input type="text" name="ccfa_opt_logourl" value="<?php echo get_option('ccfa_opt_logourl'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row">Player Width <br /><small>Best set to the width of the logo image</small></th>
<td valign="middle"><input type="text" name="ccfa_opt_playerwidth" value="<?php echo get_option('ccfa_opt_playerwidth'); ?>" /></td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="ccfa_opt_audiocat,ccfa_opt_logourl,ccfa_opt_playerwidth" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Update Options') ?>" />
</p>

</form>
</div>
