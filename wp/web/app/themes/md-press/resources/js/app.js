
import { createElement } from 'react';
import { createRoot } from 'react-dom/client';
import { MedicalSearchDirectory } from './components/MedicalSearchDirectory';

import.meta.webhook

document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('medical-search-root');

    if (rootElement) {
        const root = createRoot(rootElement);
        root.render(createElement(MedicalSearchDirectory));
    }
});