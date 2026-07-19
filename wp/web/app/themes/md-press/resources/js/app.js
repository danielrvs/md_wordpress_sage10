
import { createElement, createRoot } from '@wordpress/element';
import { MedicalSearchDirectory } from './components/MedicalSearchDirectory';

document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('medical-search-root');

    if (rootElement) {
        const root = createRoot(rootElement);
        root.render(createElement(MedicalSearchDirectory));
    }
});