<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use Carbon\Carbon;
use CranleighSchool\CranleighPeople\Exceptions\StaffNotFoundException;
use CranleighSchool\CranleighPeople\Exceptions\TooManyStaffFound;
use CranleighSchool\CranleighPeople\Importer\MediaUploader;
use CranleighSchool\CranleighPeople\Plugin;
use Exception;
use WP_Post;
use WP_REST_Request;

class RemovePerson
{
    use SaveMetaTrait;

    public WP_Post $post;

    private string $username;

    /**
     * @throws Exception
     */
    public function __invoke(WP_REST_Request $data): array
    {
        if (Plugin::getPluginSetting('isams_controlled') !== 'yes') {
            throw new \Exception('The plugin is not set to be under ISAMS Control', 400);
        }

        // 1. Get the image from the request
        $this->username = $data->get_param('initials');


        // 2. Get the WP Post (and set it to the $this->post property, if it fails throw an error and die early)
        if (is_wp_error($this->getWPPost())) {
            throw new Exception('Error getting WP Post', 500);
        }

        // 3. Delete the post
        $delete = wp_delete_post($this->post->ID);

        // 4. If we get a WP_Post object back, we have successfully deleted the post
        if ($delete instanceof WP_Post) {
            return [
                'status' => 'success',
                'message' => 'Person removed',
            ];
        }

        // 5. If we get here we have failed.
        throw new Exception('Error deleting staff post', 500);
    }


    private function getWPPost(): WP_Post|\WP_Error
    {
        try {
            $this->post = (new FindStaffPost($this->username))->find();
            return $this->post;
        } catch (TooManyStaffFound $exception) {
            $msg = 'Too many staff found for ' . $this->username . ', aborting.';
            (new SlackMessage($msg))->send();
            return new \WP_Error(400, $msg);
        } catch (StaffNotFoundException $exception) {
            $msg = 'Staff Member not found: ' . $this->username . ', aborting.';
            (new SlackMessage($msg))->send();
            return new \WP_Error(400, $msg);
        }
    }
}
