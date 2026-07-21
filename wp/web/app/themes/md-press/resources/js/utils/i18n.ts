declare global {
  interface Window {
    AppTranslations?: {
      locale: string;
      strings: Record<string, string>;
    };
  }
}

export function __(key: string, fallback?: string): string {
  if (typeof window !== 'undefined' && window.AppTranslations?.strings?.[key]) {
    return window.AppTranslations.strings[key];
  }
  return fallback || key;
}

export function getLocale(): string {
  if (typeof window !== 'undefined' && window.AppTranslations?.locale) {
    return window.AppTranslations.locale;
  }
  return 'es';
}
