<?php

/******************************************************************************
Configuration variables
******************************************************************************/

$t = '$t';
date_default_timezone_set('America/Los_Angeles'); // squelch warning
$likey_innactive = array(
    'not available',
);

/******************************************************************************
Return the worksheet ids
******************************************************************************/

function retrieve_sheet_ids($sheet_key) {
    global $t;

    $sheet_info = ('https://spreadsheets.google.com/feeds/worksheets/'.
                   $sheet_key.'/public/full?alt=json');


    $sheet_data = retrieve_google_data($sheet_info);

    $output = Array();

    foreach ($sheet_data->feed->entry as $index=>$sheet) {
        $row = Array();
        $row['title'] = $sheet->title->$t;
        $row['id'] = end(explode('/', $sheet->id->$t));
        array_push($output, $row);
    }

    return $output;
}


/******************************************************************************
Analyze the sheet to create config file
******************************************************************************/

function analyze_sheet($sheet_key, $sheet_id=null) {
    global $t;

    if( is_null($sheet_id) ) return retrieve_sheet_ids($sheet_key);

    $sheet_info = ('https://spreadsheets.google.com/feeds/list/'.$sheet_key.'/'.
                   $sheet_id.'/public/values?alt=json');

    $sheet_data = retrieve_google_data($sheet_info, 'gsx$');

    // Get the dates
    $row_1 = $sheet_data->feed->entry[0];
    $columns = get_object_vars($row_1);
    $dates = Array();

    foreach ($columns as $key => $value) {
        $date = str_replace('weekof', '', $key).date("Y");
        if( is_a_date(ucwords($date)) ) {
            $date_filter = array(
                'date' => strtotime(ucwords($date)),
                'label' => $key
            );
            array_push($dates, $date_filter);
        }
    }

    // Get the types and states
    $types = Array();
    $states = Array();
    foreach ($sheet_data->feed->entry as $row) {
        $type = strtolower($row->type->$t);

        if( strlen($type) > 0) $types[$type] = build_type_objects($type);

        foreach ($dates as $date) {
            $state = strtolower($row->$date['label']->$t);
            if( strlen($type) > 0) array_push($states, $state);
        }
    }

    // Only return unique
    $states = array_unique($states);
    usort($states, 'innactive_at_the_top');

    return Array(
        'dates' => $dates,
        'types' => $types,
        'states' => $states,
        'url' => ('https://spreadsheets.google.com/feeds/list/'.$sheet_key.'/'.
                  $sheet_id.'/public/values')
    );
}

/******************************************************************************
Helper functions
******************************************************************************/

function is_a_date($x) {
    // sort of from http://stackoverflow.com/a/11029851/334743
    return (date('FjSY', strtotime($x)) == $x);
}

function retrieve_google_data($url, $search=false, $replace='') {
    $file = file_get_contents($url);

    if( strlen($file) < 1 )  throw new Exception('No data retreived - are '.
                                                 'you sure you published '.
                                                 'this sheet?');

    if( $search ) $file = str_replace($search, $replace, $file);

    return json_decode($file);
}
function is_probably_innactive($value) {
    global $likey_innactive;
    return in_array($value, $likey_innactive);
}
function innactive_at_the_top($a, $b) {
    if( probably_innactive($a) ) return -1;
    elseif( probably_innactive($b) ) return 1;
    else return ($a < $b) ? -1 : 1;

}
function build_type_objects($type) {
    return array(
        'long_title' => $type,
        'default' => false,
        'icon' => ('http://chart.apis.google.com/chart?chst=d_map_pin_letter&'.
                   'chld=|'.random_color()),
    );
}
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}
function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}


/******************************************************************************
Can run from the command line - will stdout the json config file
******************************************************************************/

if( php_sapi_name() == 'cli') {
    $key = $argv[1];
    $sheet_id = isset($argv[2]) ? $argv[2] : null;
    $data = analyze_sheet($key, $sheet_id);

    $data['states'] = $likey_innactive; // Just assume for the likely innactive
    echo json_encode($data);
}

?>
