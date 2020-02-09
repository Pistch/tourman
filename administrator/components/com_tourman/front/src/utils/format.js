import moment from 'moment';

export function formatDateTime(value) {
  return moment(value).format('YYYY-MM-DD HH:mm:ss');
}

export function formatDate(value) {
  return moment(value).format('YYYY-MM-DD');
}

export function leadingZero(value) {
  const num = Number(value);
  return num > 9 ? '' + num : '0' + num;
}
