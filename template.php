<?php

define('SECURITY_LOGS', 'public://logs');

/**
 * syddjurs_omega_subtheme
 *
 * PHP version 5
 *
 * @category Themes
 * @package  Themes_Syddjurs_Omega_Subtheme
 * @author   Stanislav Kutasevits <stan@bellcom.dk>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @file
 * This file is empty by default because the base theme chain (Alpha & Omega) provides
 * all the basic functionality. However, in case you wish to customize the output that Drupal
 * generates through Alpha & Omega this file is a good place to do so.
 *
 * Alpha comes with a neat solution for keeping this file as clean as possible while the code
 * for your subtheme grows. Please read the README.txt in the /preprocess and /process subfolders
 * for more information on this topic.
 */

/**
 * Implementation of hook_preprocess_page.
 * Adds needed JS behaviour, loads the notes/speaker paper indicators, makes the security log entries.
 *
 * @param mixed &$variables variables
 * @return none
 */
function syddjurs_omega_subtheme_preprocess_page(&$variables) 
{
    $view = views_get_page_view();
    if (!empty($view)) {
        drupal_add_js(drupal_get_path('theme', 'syddjurs_omega_subtheme') . '/js/syddjurs_omega_subtheme.js');
        if ($view->name == 'meeting_details') {
            //adding expand/collapse behaviour to meeting details view
            drupal_add_js('bullet_point_add_expand_behaviour()', 'inline');
            $variables['views'] = '';
        }
        if ($view->name == 'meeting_details' || $view->name == 'speaking_paper') {
            //adding has notes indicator to attachment
            $annotations = os2dagsorden_annotator_get_notes_by_meeting_id(arg(1));
            $attachment_ids = array();
            foreach ($annotations as $note) {
                $attachment_ids[] = $note['bilag_id'];
            }
            $attachment_ids = array_unique($attachment_ids);
            $attachment_ids = implode(",", $attachment_ids);
            drupal_add_js('ids = [' . $attachment_ids . ']; bullet_point_attachment_add_notes_indicator(ids)', 'inline');
        }
        if ($view->name == 'speaking_paper') {
            //adding expand/collapse behaviour bullet point details view
            drupal_add_js('bullet_point_details_init()', 'inline');
            $variables['views'] = '';

            //logging access of closed bullet point
            $nid = arg(3);
            $bullet_point = node_load(arg(3));
            if ($bullet_point->field_bul_point_closed['und'][0]['value'] == 1) {
                global $user;
                $full_user = user_load($user->uid);
                $log = drupal_realpath(SECURITY_LOGS . '/closed_bullet_point_access.log');
                $handle = fopen($log, 'a');
                $data = '[';
                $data .= date('d-m-Y H:i:s');
                $data .= ']';
                $data .= ' ' . $full_user->name . ' [ID: ' . $full_user->field_user_id['und'][0]['value'] . ']';
                $data .= ' [IP: ' . os2dagsorden_access_helper_get_client_ip() . ' ]';
                $data .= ' accessed closed bullet point [' . $bullet_point->title . ']';
                $data .= ' url: [' . $_SERVER['REQUEST_URI'] . ']';
                $data .= PHP_EOL;
                fwrite($handle, $data);
            }
        }
    }
}

/**
 * Implementation of theming the calendar title. 
 * Change the format of navigation title in calendar day view to be [weekday], [day]. [month] [year]
 *
 * @param mixed $params params
 *
 * @return reformatted title
 */
function syddjurs_omega_subtheme_date_nav_title($params) 
{
    $granularity = $params['granularity'];
    $view = $params['view'];
    $date_info = $view->date_info;
    $link = !empty($params['link']) ? $params['link'] : FALSE;
    $format = !empty($params['format']) ? $params['format'] : NULL;
    switch ($granularity) {
        case 'year':
            $title = $date_info->year;
            $date_arg = $date_info->year;
            break;
        case 'month':
            $format = !empty($format) ? $format : (empty($date_info->mini) ? 'F Y' : 'F');
            $title = date_format_date($date_info->min_date, 'custom', $format);
            $date_arg = $date_info->year . '-' . date_pad($date_info->month);
            break;
        case 'day':
            $format = !empty($format) ? $format : (empty($date_info->mini) ? 'l, j. F Y' : 'l, F j');
            $title = date_format_date($date_info->min_date, 'custom', $format);
            $date_arg = $date_info->year . '-' . date_pad($date_info->month) . '-' . date_pad($date_info->day);
            break;
        case 'week':
            $format = !empty($format) ? $format : (empty($date_info->mini) ? 'F j, Y' : 'F j');
            $title = t('Week of @date', array('@date' => date_format_date($date_info->min_date, 'custom', $format)));
            $date_arg = $date_info->year . '-W' . date_pad($date_info->week);
            break;
    }
    if (!empty($date_info->mini) || $link) {
        // Month navigation titles are used as links in the mini view.
        $attributes = array('title' => t('View full page month'));
        $url = date_pager_url($view, $granularity, $date_arg, TRUE);
        return l($title, $url, array('attributes' => $attributes));
    }
    else {
        return $title;
    }
}

/**
 * Format the time row headings in the week and day view.
 * Change the time format to be [hour].[minutes]
 *
 * @param mixed $vars vars
 *
 * @return reformatted title
 */
function syddjurs_omega_subtheme_calendar_time_row_heading($vars) 
{
    $start_time = $vars['start_time'];
    $next_start_time = $vars['next_start_time'];
    $curday_date = $vars['curday_date'];
    static $format_hour, $format_ampm;
    if (empty($format_hour)) {
        $format = variable_get('date_format_short', 'm/d/Y - H:i');
        $limit = array('hour', 'minute');
        $format_hour = str_replace(array('a', 'A'), '', date_limit_format($format, $limit));
        $format_ampm = strstr($format, 'a') ? 'a' : (strstr($format, 'A') ? 'A' : '');
    }
    if ($start_time == '00:00:00' && $next_start_time == '23:59:59') {
        $hour = t('All times');
    }
    elseif ($start_time == '00:00:00') {
        $date = date_create($curday_date . ' ' . $next_start_time);
        $hour = t('Before @time', array('@time' => date_format($date, $format_hour)));
    }
    else {
        $date = date_create($curday_date . ' ' . $start_time);
        $hour = date_format($date, $format_hour);
    }
    if (!empty($date)) {
        $ampm = date_format($date, $format_ampm);
    }
    else {
        $ampm = '';
    }
    return array('hour' => $hour, 'ampm' => $ampm);
}

/**
 * Changes the format of the exposed form - meetings search.
 *
 * @param mixed &$form       form
 * @param mixed &$form_state form state
 *
 * @return none
 */
function syddjurs_omega_subtheme_form_alter(&$form, &$form_state) {
    if ($form['#id'] == 'views-exposed-form-meetings-search-page') {
	$form['from_date']['value']['#date_format'] = 'd-m-Y';
        $form['to_date']['value']['#date_format'] = 'd-m-Y';
    }
}