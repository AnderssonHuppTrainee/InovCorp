export function formatDate(date?: string | Date, locale = 'pt-PT'): string {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleDateString(locale, {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  });
}

export function formatDateTime(date?: string | Date, locale = 'pt-PT'): string {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleString(locale, {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  });
}
