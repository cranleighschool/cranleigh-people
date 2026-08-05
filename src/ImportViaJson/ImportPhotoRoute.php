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

class ImportPhotoRoute
{
    use SaveMetaTrait;

    public WP_Post $post;

    public Carbon $lastUpdated;
    private string $username;

    /**
     * @throws Exception
     */
    public function __invoke(WP_REST_Request $data): array
    {
        if (Plugin::getPluginSetting('isams_controlled') !== 'yes') {
            throw new \Exception('The plugin is not set to be under ISAMS Control', 400);
        }

        // 1. We allow an empty body, but we want it to be null, rather than an empty string
        $image = empty($data->get_body()) ? null : $data->get_body();

        // 2. Use getimagesizefromstring to check if the image is valid
        $imageInfo = getimagesizefromstring($image);

        // 3. If the image is not null, and the image is not valid, die early, we're not wanting this
        if (!is_null($image) && $imageInfo === false) {
            throw new \Exception('Image is not valid', 400);
        }

        // 4. Set the username and lastUpdated properties
        $this->username = $data->get_param('initials');
        $this->lastUpdated = Carbon::parse($data->get_param('last_updated'));

        // 5. Get the WP Post (and set it to the $this->post property, if it fails throw an error and die early)
        if (is_wp_error($this->getWPPost())) {
            throw new Exception('Error getting WP Post', 500);
        }

        // 5. If the image is null, and the post currently has a thumbnail, remove the thumbnail
        if ($this->removeThumbnailIfNullImageProvided($image)) {
            return [
                'success' => 'Removed featured image',
            ];
        }

        // 6. If the post does not have a thumbnail, upload the image
        if (!has_post_thumbnail($this->post)) {
            // Needs a thumbnail, upload it here...
            $image = $this->importImage($image);
            return [
                'success' => 'Added featured image',
                'image' => $image,
            ];
        }

        // If we are here, the post has a thumbnail, and our image exists.

        // 6. If the post has a thumbnail, check if the image is newer than the last updated time
        $thumbnail = get_post_thumbnail_id($this->post);
        if ($thumbnail) {
            $thumbnail = get_post($thumbnail);
            if (Carbon::parse($thumbnail->post_modified)->greaterThanOrEqualTo($this->lastUpdated)) {
                // 6.1 If the image is not newer than the last updated time, return an error
                return [
                    'error' => 'Staff photo is newer than the last updated time',
                    'meta' => [
                        'your_image' => $this->lastUpdated,
                        'our_image' => Carbon::parse($thumbnail->post_modified),
                    ]
                ];
            } else {
                // 6.2 Otherwise, update the image
                delete_post_thumbnail($this->post);
                $image = $this->importImage($image);
                return [
                    'success' => 'Updated featured image',
                    'image' => $image,
                ];
            }
        }

        // 7. If we are here, something has gone wrong, throw an error
        throw new Exception('We should not be here', 500);
    }

    private function removeThumbnailIfNullImageProvided(?string $image): bool
    {
        if (is_null($image) && has_post_thumbnail($this->post)) {
            // We take this a directive to remove the featured image that is currently set.
            delete_post_thumbnail($this->post);
            return true;
        }
        return false;
    }

    /**
     * @param string $image
     * @return WP_Post
     * @throws Exception
     */
    private function importImage(string $image): WP_Post
    {
        $image = new MediaUploader($image, $this->post->ID, $this->post->post_title);

        return $image->upload();
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
