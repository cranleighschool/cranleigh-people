<?php


	namespace CranleighSchool\CranleighPeople;



	class View
	{
		public static function view(string $template, array $vars)
		{
			extract($vars);

			return include trailingslashit(dirname(CRAN_PEOPLE_FILE_PATH)) . "src/Views/" . $template . ".php";
		}

		/**
		 * -------------------------------------
		 * Render a Template.
		 * -------------------------------------
		 *
		 * @param      $templateName - The name of the php file in Views folder.
		 * @param null $viewData     - any data to be used within the template.
		 *
		 * @return string -
		 *
		 */
		public static function render(string $templateName, array $viewData = NULL): string
		{


			// Was any data sent through?
			($viewData) ? extract($viewData) : NULL;

			ob_start();
			include self::get_template_path($templateName);
			$template = ob_get_contents();
			ob_end_clean();

			wp_reset_postdata();
			wp_reset_query();

			return $template;
		}

		public static function the_post_thumbnail()
		{
			Plugin::switch_to_blog(Plugin::getPluginSetting('load_from_blog_id'));
			if (has_post_thumbnail()) :
				the_post_thumbnail(Plugin::PROFILE_PHOTO_SIZE_NAME, ['class' => 'img-responsive']);
			elseif (Plugin::get_default_attachment_id() !== NULL) :
				$photo = wp_get_attachment_image(
					Plugin::get_default_attachment_id(),
					Plugin::PROFILE_PHOTO_SIZE_NAME,
					false,
					['class' => 'img-responsive']
				);
				echo $photo;
			endif;
			Plugin::restore_current_blog();
		}

		private static function get_template_path(string $templateName): string
		{
			$templateFile = trailingslashit(dirname(CRAN_PEOPLE_FILE_PATH)) . "src/Views/" . $templateName . ".php";
			if (file_exists($templateFile)) {
				return $templateFile;
			} else {
				throw new \Exception("Cranleigh People View '".$templateName."' is not found.", 500);
			}

		}
	}

