import { initEventPicker } from './EventPicker';
import { initMaps } from './GoogleMap/initMaps';
import { initAddToCalendar } from './AddToCalendar/initAddToCalender';
import Collapsibles from 'colby-wp-collapsible';

const startSchedule = (options = {}) => {
  initMaps();
  initAddToCalendar();
  initEventPicker();
  if (options.doCollapsibles) {
    Collapsibles.init();
  }
};

export default startSchedule;
