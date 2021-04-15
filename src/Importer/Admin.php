<?php

namespace CranleighSchool\CranleighPeople\Importer;

    use CranleighSchool\CranleighPeople\Cron;
    use CranleighSchool\CranleighPeople\Plugin;

    class Admin
    {
        public static function add_submenu_page()
        {
            add_action('admin_menu', function () {
                add_submenu_page(
                    'edit.php?post_type='.Plugin::POST_TYPE_KEY,
                    __('Manual Importer', 'cranleigh'),
                    __('Manual Importer', 'cranleigh'),
                    'manage_options',
                    'cranleigh_people_manual_importer',
                    [__CLASS__, 'importPage']
                );
            });
        }

        /**
         * @return bool
         * @throws \Exception
         */
        public static function importPage()
        {
            if (isset($_POST['usernames'])) {
                $usernames = array_map('trim', explode(',', strtoupper($_POST['usernames'])));
                Importer::import($usernames);
            }
            if (isset($_POST['thewholelot'])) {
                Importer::import();
            } ?>
			<div class="wrap">
				<h2>Cranleigh People Importer</h2>
				<?php
                    if (! Plugin::getPluginSetting('importer_api_endpoint', true)) {
                        echo "<div class='error notice-error notice'><p><strong>Error: </strong>You haven't set an API endpoint. You'll need to do that first.</p></div>";

                        return false;
                    } ?>
				<p>Please use this manually run the script to import staff members from <a
						href="https://people.cranleigh.org/">Cranleigh People Manager</a>.</p>

				<h3>Import Specific Staff Members</h3>
				<form method="post">
					<table class="form-table">
						<tbody>
						<tr>
							<th scope="row">Usernames to Find and Import/Update</th>
							<td>
								<input type="text" class="regular-text" name="usernames"/>
								<p class="description">A single, or comma separated list of usernames that you wish to
									find and import.</p>
							</td>
						</tr>
						</tbody>
					</table>
					<input type="submit" class="button button-primary button-large"/>
				</form>
				<hr/>
				<h3>Screw that, just import the lot!</h3>
				<p>This process happens magically every day, but if you want to run it manually you can do so here. It
					takes a while though...</p>
				<p>This is next scheduled in: <strong><?php echo Cron::next_scheduled_sync(); ?></strong></p>
				<form method="post">
					<input type="hidden" name="thewholelot"/>
					<input type="submit" class="button button-primary button-large"/>
				</form>
			</div>
			<?php
        }
    }
