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
    public function __invoke(WP_REST_Request $data): \WP_REST_Response
    {
        if (Plugin::getPluginSetting('isams_controlled') !== 'yes') {
            throw new \Exception('The plugin is not set to be under ISAMS Control', 400);
        }

        // 1. Get the image from the request
        $this->username = $data->get_param('initials');


        // 2. Get the WP Post (and set it to the $this->post property, if it fails throw an error and die early)
        $post = $this->getWPPost();
        if (is_wp_error($post)) {
            $errorCodes = [
                422 => 'unprocessable_entity',
                404 => 'not_found'
            ];

            $errorCode = $post->get_error_code();
            $status = $errorCodes[$errorCode] ?? null;

            if ($status) {
                return new \WP_REST_Response([
                    'status' => $status,
                    'message' => $post->get_error_message(),
                ], $errorCode);
            }

            throw new Exception('Error getting WP Post', 500);
        }

        // 3. Delete the post
        $delete = wp_delete_post($this->post->ID);

        // 4. If we get a WP_Post object back, we have successfully deleted the post
        if ($delete instanceof WP_Post) {
            return new \WP_REST_Response([
                'status' => 'success',
                'message' => 'Person removed',
            ], 200);
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
            $msg = 'Too many staff found for ' . $this->username . ', failed to delete.';
            (new SlackMessage($msg))->send();
            return new \WP_Error(422, $msg);
        } catch (StaffNotFoundException $exception) {
            $msg = 'Staff Member not found: ' . $this->username . ', nothing to delete.';
            (new SlackMessage($msg))->send();
            return new \WP_Error(404, $msg);
        }
    }
}
