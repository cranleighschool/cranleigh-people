<?php

namespace FredBradley\CranleighSlacker;

class Slacker {

	private static $webhookEndpoint = false;
	public static $room = false;
	public static $attachments = array();
	public static $fields = array();
	public static $authorName;
	public static $title;
	public static $username = "Slacker Script";
	public static $attachmentColor = "#0c223f";

	private static $_instance = null;

    public function __construct($webhook = null, $room = null) {
    	if ($webhook !== null) {
			self::setWebhook($webhook);
	    }
	    if ($room !== null) {
    		self::setRoom($room);
	    }
    }

    public static function getInstance ()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

	public static function post(string $message=null) {
		$room = self::$room;

		$data = array(
			"channel" => "#{$room}",
			"text" => $message,
			"mrkdown" => true,
			"username" => self::getUsername()
		);

		if (!empty(self::$attachments)) {
			$data["attachments"] = self::$attachments;
		}

		if ($_SERVER['HTTP_USER_AGENT']==="nagios-check") {
			return false;
		}

		return self::curler($data);
    }

    private static function curler(array $data) {
	    $data = "payload=" . json_encode($data);

        $ch = curl_init(self::$webhookEndpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

	public static function getAuthor() {
		if (self::$authorName==null) {
			return gethostname();
		} else {
			return self::$authorName;
		}
	}

	public static function setWebhook($webhook) {
    	self::$webhookEndpoint = $webhook;

    	return new self;
	}
	public static function setRoom($room) {
		self::$room = $room;

		return new self;
	}

	public static function setAuthor($name) {
		self::$authorName = $name;

		return new self;
	}
	public static function setUsername($name) {
		self::$username = $name;
		return new self;
	}
	public static function getUsername() {
		return self::$username;
	}

	public static function getTitle() {
		if (self::$title==null) {
			return "Sync Script Report";
		} else {
			return self::$title;
		}
	}

	public static function setTitle($name) {
		self::$title = $name;

		return new self;
	}

	public static function addAttachmentField($title, $value) {
		self::$fields[] = array(
			"title" => $title,
			"value" => $value,
			"short" => true
		);

		return new self;
	}
	public static function setAttachmentColor($color) {
    	self::$attachmentColor = $color;

    	return new self;
	}
	public static function addAttachment($text) {
		self::$attachments[] = array(
			"pretext" => $text,
			"fallback" => $text,
			"title" => $text,
			"title_link" => "https://www.cranleigh.org",
			"color" => self::$attachmentColor,
			"author_name" => self::getAuthor(),
			"ts" => time(),
			"footer" => "Fred's Slacker Class",
			"fields" => self::$fields,
			"mrkdwn_in" => ["text", "pretext"]
		);
		return new self;
	}
}

