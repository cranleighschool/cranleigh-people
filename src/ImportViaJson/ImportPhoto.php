<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\Exceptions\StaffNotFoundException;
use CranleighSchool\CranleighPeople\Exceptions\TooManyStaffFound;
use CranleighSchool\CranleighPeople\Importer\MediaUploader;
use Exception;
use WP_Post;

class ImportPhoto
{
    use SaveMetaTrait;

    private WP_Post $post;

    public function __construct(protected readonly PersonMap $person)
    {


    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        // Do the Profile Pic
        $image = $this->featureImageLogic();
        if ($image instanceof WP_Post) {
            // Updated / Created Featured Image;
        } elseif ($image === true) {
            // Removed Image, because no image was on People Manager
        } elseif ($image === NULL) {
            // No logic was hit, changing nothing.
        } else {
            throw new Exception('Error whilst checking featureImageLogic. Type: ' . gettype($image), 500);
        }
    }

    /**
     * @return bool|WP_Post
     */
    private function featureImageLogic(): bool|WP_Post
    {
        /**
         * First, save the `photo_updated` property from People Manager into WordPress.
         */
        $this->saveMeta('mugshot_updated_time', $this->person->photo_updated);

        /**
         * If People Manager has no photo, but WordPress does, then remove the Featured Image.
         */
        if ($this->person->photo_uri === NULL && has_post_thumbnail($this->post)) {
            // TODO: Should we delete the Media Library item as well as remove the thumbnail meta link?

            return delete_post_thumbnail($this->post); // True on success, false on failure
        }

        /**
         * If People Manager has a photo, and WordPress does not, then give WordPress the photo.
         */
        if ($this->person->photo_uri !== NULL && !has_post_thumbnail($this->post)) {
            // Run importer
            return self::importImage($this->person->photo_uri, $this->post);
        }

        /**
         * Now we get nitty gritty.
         *
         * If People Manager has a photo AND WordPress has a photo,
         * AND if the People Manager photo is newer than the Featured Image photo...
         */
        if ($this->person->photo_uri !== NULL && has_post_thumbnail($this->post) && self::api_photo_is_newer_than_wp_featured_image($this->person, $this->post)) {
            // TODO: Should we delete the Media Library item as well as remove the thumbnail meta link?
            // Run Importer
            return self::importImage($this->person->photo_uri, $this->post);
        }
        return false;
    }

    /**
     * @param string $url
     * @param WP_Post $parent_post
     * @param string|null $image_description
     *
     * @return WP_Post
     * @throws Exception
     */
    private function importImage(string $url, WP_Post $parent_post, string $image_description = NULL): WP_Post
    {
        $image = new MediaUploader($url, $this->post->ID, $this->post->post_title);

        return $image->upload();
    }

    private function getWPPost(): WP_Post|\WP_Error
    {
        try {
            $this->post = (new FindStaffPost($this->person->school_initials))->find();
            return $this->post;
        } catch (TooManyStaffFound $exception) {
            $msg = 'Too many staff found for ' . $this->person->school_initials . ', aborting.';
            (new SlackMessage($msg))->send();
            return new \WP_Error(400, $msg);
        } catch (StaffNotFoundException $exception) {
            $msg = 'Staff Member not found: ' . $this->person->school_initials . ', aborting.';
            (new SlackMessage($msg))->send();
            return new \WP_Error(400, $msg);
        }
    }
}
