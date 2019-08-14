require('../scss/app.scss');
import $ from 'jquery';
import 'jquery-ui';
import 'jquery-ui-sortable';
import 'bootstrap';
import 'selectize';
import {
    builderform
} from './modules/builderform';
import {
    fullCalendar
} from './modules/fullcalendar';
import {
    datatables
} from './modules/datatables';
import {
    wysiwyg
} from './modules/wysiwyg';
import {
    prismjs
} from './modules/prismjs';
import {
    form
} from './modules/form';
import {
    admin
} from './modules/admin';
import {
    selectize
} from './modules/selectize';
import 'whatwg-fetch';
global.$      = $;
global.Jquery = $;
class Site {
    /**
     * TODO: Test
     */
    launch() {
        this.admin        = new admin();
        this.selectize    = new selectize();
        this.form         = new form();
        this.prismjs      = new prismjs();
        this.builderform  = new builderform('formBuilder');
        this.wysiwyg      = new wysiwyg();
        this.datatables   = new datatables();
        this.fullcalendar = new fullCalendar('fullCalendar');
        this.login();
    }

    login() {
        $('#login_username').trigger('focus');
    }
}
(($) => {
    const site = new Site();

    site.launch();
} )(jQuery);
