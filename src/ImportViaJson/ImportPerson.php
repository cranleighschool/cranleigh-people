<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\Exceptions\StaffNotFoundException;
use CranleighSchool\CranleighPeople\Exceptions\TooManyStaffFound;
use CranleighSchool\CranleighPeople\Metaboxes;
use CranleighSchool\CranleighPeople\Plugin;
use Exception;
use WP_Post;

class ImportPerson
{
    use SaveMetaTrait;
    private WP_Post $post;

    public function __construct(protected readonly PersonMap $person)
    {

    }

    /**
     * @throws Exception
     */
    public function handle(): string
    {
        try {
            $post_id = (new FindStaffPost($this->person->school_initials))->find()->ID;
        } catch (TooManyStaffFound $exception) {
            (new SlackMessage('Too many staff found for ' . $this->person->school_initials . ', aborting.'))->send();
            return $this->person->school_initials . ' [FAILED]';
        } catch (StaffNotFoundException $exception) {
            $post_id = 0;
            (new SlackMessage('Going to create ' . $this->person->school_initials))->send();
        }

        $this->updateOrCreate($post_id);

        return $this->person->school_initials;
    }

    /**
     * @param int $post_id
     *
     * @return void
     * @throws Exception
     */
    private function updateOrCreate(int $post_id = 0): void
    {
        error_log('Start ' . self::present_tense_verb($post_id) . ' ' . $this->person->school_initials);

        $post_title = $this->person->prename . ' ' . $this->person->surname;
        $post_content = is_null($this->person->biography) ? '' : $this->person->biography;

        $staff_post = wp_insert_post(
            array(
                'post_type' => Plugin::POST_TYPE_KEY,
                'post_status' => $this->get_wp_post_status(),
                'ID' => $post_id,
                'post_title' => $post_title,
                'post_content' => $post_content,
                'post_name' => sanitize_title($post_title),
            )
        );

        if (is_wp_error($staff_post)) {
            throw new Exception('Could not save post', 500);
        }
        $this->post = get_post($staff_post);

        /**
         * We only set the Username if we are creating a new Staff.
         */
        if ($post_id === 0) {
            $this->saveMeta('username', $this->person->school_initials);
        }

        $this->saveMeta('surname', $this->person->surname);
        $this->saveMeta('leadjobtitle', self::getLeadJobTitle($this->person->job_titles));
        $this->saveMeta('position', self::getOtherJobTitles($this->person->job_titles));
        $this->saveMeta('full_title', $this->person->label_salutation);
        $this->saveMeta('qualifications', self::qualificationsAsList($this->person->qualifications));
        $this->saveMeta('email_address', $this->person->email);
        $this->saveMeta('phone', $this->person->phone);
        $this->saveMeta('prefix', $this->person->title['name']);
        $this->saveMeta('prename', $this->person->prename);


        // Set the Taxonomy Objects
        (new SetStaffCategoryTaxonomy($this->post, $this->person))->handle();
        (new SetStaffHousesTaxonomy($this->post, $this->person))->handle();
        (new SetStaffSubjectsTaxonomy($this->post, $this->person))->handle();

        /** TODO: I think we'll do the Image in a separate request
        // Do the Profile Pic
        $image = self::featureImageLogic($staff_post, $person);
        if ($image instanceof WP_Post) {
            // Updated / Created Featured Image;
        } elseif ($image === true) {
            // Removed Image, because no image was on People Manager
        } elseif ($image === NULL) {
            // No logic was hit, changing nothing.
        } else {
            throw new Exception('Error whilst checking featureImageLogic. Type: ' . gettype($image), 500);
        }
         */
    }



    /**
     * Just a nice little helper function.
     *
     * @param int $post_id
     *
     * @return string
     */
    private static function present_tense_verb(int $post_id): string
    {
        if ($post_id === 0) {
            return 'creating';
        } else {
            return 'updating';
        }
    }

    /**
     * @return string
     */
    private function get_wp_post_status(): string
    {
        if ($this->person->system_status !== '1') {
            return 'private';
        }

        if ($this->person->hide_from_website !== NULL && strtotime($this->person->hide_from_website) > time()) {
            return 'pending';
        }

        return 'publish';
    }

    /**
     * @param array $jobTitles
     *
     * @return string|null
     */
    public static function getLeadJobTitle(?array $jobTitles): ?string
    {
        if (isset($jobTitles[0])) {
            return $jobTitles[0];
        }

        return NULL;
    }

    /**
     * @param array $jobTitles
     *
     * @return array
     */
    public static function getOtherJobTitles(?array $jobTitles): array
    {
        if (!is_array($jobTitles)) {
            return [];
        }
        array_shift($jobTitles);

        return $jobTitles;
    }

    /**
     * @param array $qualifications
     *
     * @return string
     */
    public static function qualificationsAsList(array $qualifications): string
    {
        return implode(', ', $qualifications);
    }

}
