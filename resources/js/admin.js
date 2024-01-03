import $ from "jquery";
import 'bootstrap';
window.$ = window.jQuery = $;

require('./bootstrap.js');
// MODULES

// Plugin FILES
require('./plugins/init.js');

window.Swal = swal;

// CUSTOM FILES
require('./custom/main.js');
require('./custom/helpers/init.js');
require('./custom/features/init.js');

// require('./general.js');
// require('./custom.js');
//
// require('./modules/helpers/ajax.js');
// require('./modules/helpers/constants.js');
// require('./modules/helpers/helper.js');
// require('./modules/helpers/sweet-alerts.js');
//
// require('./modules/features/users.js');
// require('./modules/features/giftCards.js');
// require('./modules/features/voucher.js');
// require('./modules/features/customer.js');
// require('./modules/features/early_access.js');
// require('./modules/features/request_response_logs.js');




