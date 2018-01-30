import Collapsibles from 'colby-wp-collapsible';

import { initEventPicker } from './EventPicker';
import { initMaps } from './GoogleMap/initMaps';
import { initAddToCalendar } from './AddToCalendar/initAddToCalender';

initEventPicker();

window.addEventListener('load', Collapsibles.init);
window.addEventListener('load', initAddToCalendar);
window.addEventListener('load', initMaps);
