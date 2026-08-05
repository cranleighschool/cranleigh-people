<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

class PersonMap
{

    public int $id;
    public ?string $system_status;
    public string $school_initials;
    public ?array $title;
    public ?string $forename;
    public ?string $prename;
    public ?string $surname;
    public ?string $email;
    public ?string $label_salutation;
    public ?string $phone;
    public ?array $job_titles;
    public ?array $departments;
    public ?array $houses;
    public ?array $roles;
    public ?array $qualifications;
    public ?array $subjects;
    public ?string $biography;
    public ?bool $teacher;
    public ?bool $tutor;
    public ?string $photo_uri;
    public ?string $photo_updated;
    public ?bool $hide_from_website;


    public function __construct(array $person)
    {
        $keys = array_keys(get_class_vars(self::class));

        foreach ($keys as $key) {
            $this->$key = $person[$key] ?? null;
            /*
                        if (is_array($person['title'])) {
                            $this->title = $person['title']['name'];
                        } else {
                            $this->$key = $person[$key];
                        }*/
        }
        $this->sanitize();
    }

    private function sanitize()
    {
        foreach (['roles', 'departments', 'houses', 'job_titles', 'subjects'] as $key) {
            if (is_null($this->$key)) {
                $this->$key = [];
            }
        }
    }

}
