# Cranleigh People

This is the plugin used by the Cranleigh websites. It creates the Staff Custom Post Type and staff group taxonomies. It also puts in the custom meta fields needed. 

## Setup
By default, when you activate this plugin it only loads relevant widgets and shortcodes. If you want to install the full plugin with the custom post type, taxonomies, and auto complete staff roles, you'll need to visit the Settings page. 

## Shortcodes

### [person_card]
Attributes:

	* `user`: the staff member's Cranleigh Username
	* `type`: the type of card you want to display (common choices are either `house` or `small`)
	* `title`: used if the `type` is `house`. 

### [card_list]
Attributes: 

	* `people`: a comma separated list of Cranleigh Usernames
	* `type`: same as `type` for the `[person_card]`. Get's defaulted to `small` if you leave it blank.
	
## Changelog
### 1.0.0 "Bare Bones"
* Initial Plugin Developed
* No further instructions for scope


## Developers
* [Fred Bradley](mailto:frb@cranleigh.org)

