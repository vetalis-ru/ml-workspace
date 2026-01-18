<?php
function init_masks()
{
	if( !defined('MBLA_VERSION') ){
		add_action('mbl_options_items_after', 'mbla_settings_tab', 10);
		add_action('mbl_options_content_after', 'mbla_settings_content', 10);
		add_action('mbl_hw_tabs', 'homeworkStatsTab', 10);
		add_action('mbl_hw_content', 'homeworkStats', 10);
		//add_action('mbl_extra_profile_fields', 'coachAccess', 10, 2);
		add_action('mbl_category_autotraining_after_shift', 'addMBLAutotrainingOptions');
	}
	
	if( !defined('MBLR_VERSION') ){
		add_action('mbl_options_items_after', 'autoregistration_settings_tab', 20);
		add_action('mbl_options_content_after', 'autoregistration_settings_content', 20);
	}
	
	if( !defined('MBLMI_VERSION') ){
		add_action('mbl_options_items_after', 'mini_settings_tab', 30);
		add_action('mbl_options_content_after', 'mini_settings_content', 30);
	}
	
	if( !defined('MBLI3_VERSION') ){
		add_action('mbl_options_items_after', 'nav_settings_tab', 40);
		add_action('mbl_options_content_after', 'nav_settings_content', 40);
	}
	
	if( !defined('MBLP_VERSION') ){
		add_action('mbl_options_items_after', 'payments_settings_tab', 50);
		add_action('mbl_options_content_after', 'payments_settings_content', 50);
	}
	
	if( !defined('MBL_TESTS_VERSION') ){
		add_action('mbl_options_items_after', 'tests_settings_tab', 60);
		add_action('mbl_options_content_after', 'tests_settings_content', 60);
		
		add_action('mbl_admin_hw_after', 'setHomeworkType');
		add_action('mbl_admin_hw_test', 'testCreate');
	}
	
	if( !defined('MBL_WP_VERSION') ){
		add_action('mbl_options_items_after', 'protection_settings_tab', 70);
		add_action('mbl_options_content_after', 'protection_settings_content', 70);
	}
	
	if( !defined('MBL_DISCOUNTS_VERSION') ){
		add_action('mbl_options_items_after', 'discounts_settings_tab', 52);
		add_action('mbl_options_content_after', 'discounts_settings_content', 52);
	}
	
}

add_action('init', 'init_masks', 1);


function autoregistration_settings_tab()
{
	wpm_render_partial('masks/settings_tab_ma', 'admin');
}

function autoregistration_settings_content()
{
	$args['levels'] = get_terms('wpm-levels', ['hide_empty' => 0]);
	wpm_render_partial('masks/settings_content_ma', 'admin', $args);
}


function payments_settings_tab()
{
	wpm_render_partial('masks/settings_tab_mpp', 'admin');
}

function payments_settings_content()
{
	wpm_render_partial('masks/settings_content_mpp', 'admin');
}


function nav_settings_tab()
{
	wpm_render_partial('masks/settings_tab_mpn', 'admin');
}

function nav_settings_content()
{
	wpm_render_partial('masks/settings_content_mpn', 'admin');
}


function mini_settings_tab()
{
	wpm_render_partial('masks/settings_tab_mmini', 'admin');
}

function mini_settings_content()
{
	wpm_render_partial('masks/settings_content_mmini', 'admin');
}


function tests_settings_tab()
{
	wpm_render_partial('masks/settings_tab_mt', 'admin');
}

function tests_settings_content()
{
	wpm_render_partial('masks/settings_content_mt', 'admin');
}

function setHomeworkType()
{
	wpm_render_partial('masks/page_params_mt_type', 'admin');
}

function testCreate()
{
	wpm_render_partial('masks/page_params_mt_create', 'admin');
}


function mbla_settings_tab()
{
	wpm_render_partial('masks/settings_tab_mpt', 'admin');
}

function mbla_settings_content()
{
	wpm_render_partial('masks/settings_content_mpt', 'admin');
}

function homeworkStatsTab()
{
	wpm_render_partial('masks/hw_stats_mpt_tab', 'admin');
}

function homeworkStats()
{
	wpm_render_partial('masks/hw_stats_mpt_content', 'admin');
}

function coachAccess()
{
	wpm_render_partial('masks/profile_mpt_settings', 'admin');
}

function addMBLAutotrainingOptions()
{
	wpm_render_partial('masks/autotraining_mpt_settings', 'admin');
}

function protection_settings_tab()
{
	wpm_render_partial('masks/settings_tab_mwp', 'admin');
}

function protection_settings_content()
{
	wpm_render_partial('masks/settings_content_mwp', 'admin');
}

function discounts_settings_tab()
{
	wpm_render_partial('masks/settings_tab_discounts', 'admin');
}

function discounts_settings_content()
{
	wpm_render_partial('masks/settings_content_discounts', 'admin');
}