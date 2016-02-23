<?php
/*
# HTTP Referer Block
# Originally for siph0n forum.
# Coded by: sn
*/

if(!defined("IN_MYBB"))
{
	die("No direct access allowed.");
}

$plugins->add_hook('postbit', 'limit_intro_main');
$plugins->add_hook('parse_quoted_message', 'limit_intro_quote');
$plugins->add_hook('printthread_post', 'limit_intro_print');
$plugins->add_hook('reputation_start', 'limit_intro_reputation');


function limit_intro_info()
{
	return array(
		"name" 			=> "Limit Introductions",
		"description" 	=> "Limits the Introductions forum.",
		"website"		=> "http://siph0n.in",
		"author"		=> "sn",
		"authorsite"	=> "http://siph0n.in",
		"version"		=> "1.0",
		"guid"			=> "",
		"codename"		=> str_replace('.php', '', basename(__FILE__)),
		"compatibility"	=> "18*"
	);
}

function limit_intro_reputation()
{
	global $mybb;
	if($mybb->user['usergroup'] == $mybb->settings['limit_intro_groupid']) {
		error("You do not have permission to access this page. This is for the following reason:<ol></br><li>Your account is in a restricted usergroup.</li></ol></br>You are currently logged in with the username: 'admin' ");
	}
}

function limit_intro_quote(&$post)
{
	global $forum, $mybb;
	if($forum['fid'] == $mybb->settings['limit_intro_forumid']) {
		if($mybb->user['usergroup'] == $mybb->settings['limit_intro_groupid']) {
			if($post['username'] != $mybb->user['username']) {
				$post['username'] = "[siph0n]";
			}
		}
	}
}

function limit_intro_print()
{
	global $forum, $mybb;
	if($forum['fid'] == $mybb->settings['limit_intro_forumid']) {
		if($mybb->user['usergroup'] == $mybb->settings['limit_intro_groupid']) {
			error("You do not have permission to access this page. This is for the following reason:<ol></br><li>Your account is in a restricted usergroup.</li></ol></br>You are currently logged in with the username: 'admin' ");
		}
	}
}



function limit_intro_main(&$post)
{	
	global $mybb;
	if($post['fid'] == $mybb->settings['limit_intro_forumid']) {
		if($mybb->user['usergroup'] == $mybb->settings['limit_intro_groupid']) {
			if($post['username'] != $mybb->user['username']) {
				if($post['usergroup'] == 4) {
					$title = "siph0n Administrator";
					$username = "[siph0n_administrator]";
				} elseif($post['usergroup'] == 3) {
					$title = "siph0n Moderator";
					$username = "[siph0n_moderator]";
				} elseif($post['usergroup'] == 2) {
					$title = "siph0n Member";
					$username = "[siph0n_member]";
				}
				if($post['editusername'] != NULL) {
					$post['editusername'] = "";
				}
				$post['useravatar'] = '<div class="author_avatar"><a href=""><img src="http://127.0.0.1/v2/images/default_avatar.png" alt="" width="55" height="55" /></a></div>';
				$post['button_pm'] = '<a href="" title="Send this user a private message" class="postbit_pm"><span>PM</span></a>';
				$post['button_find'] = '<a href="" title="Find all posts by this user" class="postbit_find"><span>Find</span></a>';
				$post['button_email'] = '<a href="" title="Send this user an email" class="postbit_email"><span>Email</span></a>';
				$post['userreputation'] = "-";
				$post['usertitle'] = $title;
				$post['profilelink'] = $username;
				$post['username_formatted'] = $username;
			}
		}
	}
}

function limit_intro_install() // Called when "Install" button is pressed
{
	global $db, $mybb, $templates;
	$settings_group = array(
    	'name' => 'limit_intro',
    	'title' => 'Limit Intro',
    	'description' => 'This is my plugin and it does some things',
    	'disporder' => 5, // The order your setting group will display
    	'isdefault' => 0
	);
	$gid = $db->insert_query("settinggroups", $settings_group);
	$setting_array = array(
    	'limit_intro_enable' => array(
        	'title' => 'Limit Intro',
        	'description' => 'Do we want to activate this plugin?:',
        	'optionscode' => 'yesno',
        	'value' => '1', // Default
        	'disporder' => 1
    	),
	    'limit_intro_forumid' => array(
	        'title' => 'Introduction Forum:',
	        'description' => 'Please enter the introduction forum id:',
	        'optionscode' => "text",
	        'value' => "",
	        'disporder' => 2
	    ),
	    'limit_intro_groupid' => array(
	    	'title' => 'Group ID:',
	    	'description' => 'Which group is the trial group?:',
	    	'optionscode' => 'text',
	    	'value' => '',
	    	'disporder' => 3
	    ),
	);
	foreach($setting_array as $name => $setting)
	{
    	$setting['name'] = $name;
    	$setting['gid'] = $gid;
	    $db->insert_query('settings', $setting);
	}
	rebuild_settings();
}

function limit_intro_is_installed()
{
	global $mybb;
	if($mybb->settings["limit_intro_enable"])
	{
		return true;
	}
	return false;
}

function limit_intro_uninstall()
{
	global $db;

	$db->delete_query('settings', "name IN ('limit_intro_enable','limit_intro_forumid','limit_intro_groupid')");
	$db->delete_query('settinggroups', "name = 'limit_intro'");
	rebuild_settings();
}

function limit_intro_activate()
{

}

function limit_intro_deactivate()
{

}

