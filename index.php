<?php


require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/lib/formslib.php');


class usermapping_setting_form extends moodleform {
    public $companyid;

    function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'students', get_string('students', 'local_usermapping'));

        $mform->addElement('filepicker', 'studentcsv',
            get_string('students', 'local_usermapping'), null,
                array('subdirs' => 0,
                     'maxbytes' => 15 * 1024 * 1024,
                     'maxfiles' => 1,
                     'accepted_types' => array('*.csv')));

        $mform->addElement('filepicker', 'facultycsv',
            get_string('faculty', 'local_usermapping'), null,
                array('subdirs' => 0,
                     'maxbytes' => 15 * 1024 * 1024,
                     'maxfiles' => 1,
                     'accepted_types' => array('*.csv')));


        $this->add_action_buttons(true, get_string('savechanges'));
    }
}



require_login(1, true);
$systemcontext = context_system::instance();
$home = new moodle_url('/');

if (!is_siteadmin()) {
    redirect($home);
}


$usermapping = new usermapping_setting_form($CFG->wwwroot . '/local/usermapping/index.php');

$entry = new stdClass;

$draftitemid = file_get_submitted_draft_itemid('studentcsv');
file_prepare_draft_area($draftitemid,
                    $systemcontext->id,
                    'local_usermapping',
                    'studentcsv', 0,
                    array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
$entry->studentcsv = $draftitemid;

$draftitemid = file_get_submitted_draft_itemid('facultycsv');
file_prepare_draft_area($draftitemid,
                    $systemcontext->id,
                    'local_usermapping',
                    'facultycsv', 0,
                    array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
$entry->facultycsv = $draftitemid;


$usermapping = new usermapping_setting_form($CFG->wwwroot . '/local/usermapping/index.php');
$usermapping->set_data($entry);

if ($usermapping->is_cancelled()) {
    redirect($home);
} else if ($data = $usermapping->get_data()) {
    file_save_draft_area_files($data->studentcsv,
       $systemcontext->id,
       'local_usermapping',
       'studentcsv',
       0,
       array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 200));

    file_save_draft_area_files($data->facultycsv,
       $systemcontext->id,
       'local_usermapping',
       'facultycsv',
       0,
       array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 200));
} 


$PAGE->set_url('/local/usermapping/index.php');
$PAGE->set_title(format_string('Configure Startprep Usermapping'));
$PAGE->set_heading(format_string('Configure Startprep Usermapping'));
$PAGE->set_context($systemcontext);

echo $OUTPUT->header();

$usermapping->display();

echo $OUTPUT->footer();

?>