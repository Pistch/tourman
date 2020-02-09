export default function(entity, withTime) {
  if (!entity || !entity.start_date || !entity.end_date) {
    return '';
  }

  const start = new Date(entity.start_date);
  const end = new Date(entity.end_date);

  return `${
    withTime
      ? start.toLocaleTimeString('ru', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
      : start.toLocaleDateString('ru')
  } - ${
    withTime
      ? end.toLocaleTimeString('ru', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
      : end.toLocaleDateString('ru')
  }`;
}
